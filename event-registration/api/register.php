<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

// Verify CSRF token
verify_csrf();

// Validate required inputs
$eventId = (int) request('event_id', 0);
$ticketTypeId = (int) request('ticket_type_id', 0);
$quantity = max(1, (int) request('quantity', 1));
$email = clean_text(request('email', ''));
$name = clean_text(request('name', ''));
$phone = clean_text(request('phone', ''));

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_response(['success' => false, 'message' => 'Invalid email address'], 400);
}

// Validate name
if (strlen($name) < 2 || strlen($name) > 150) {
    json_response(['success' => false, 'message' => 'Name must be between 2 and 150 characters'], 400);
}

// Validate phone
if (strlen($phone) < 7) {
    json_response(['success' => false, 'message' => 'Invalid phone number'], 400);
}

// Validate event exists
$stmt = pdo()->prepare('SELECT * FROM events WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $eventId]);
$event = $stmt->fetch();
if (!$event) {
    json_response(['success' => false, 'message' => 'Event not found'], 404);
}

// Check if event is published and registration is open
if ($event['status'] !== 'published') {
    json_response(['success' => false, 'message' => 'Event registration is not available'], 400);
}

if (strtotime($event['registration_deadline']) < time()) {
    json_response(['success' => false, 'message' => 'Registration deadline has passed'], 400);
}

// Validate ticket type
$stmt = pdo()->prepare('SELECT * FROM ticket_types WHERE id = :id AND event_id = :event_id LIMIT 1');
$stmt->execute([':id' => $ticketTypeId, ':event_id' => $eventId]);
$ticket = $stmt->fetch();
if (!$ticket) {
    json_response(['success' => false, 'message' => 'Invalid ticket type'], 400);
}

// Check ticket availability
$available = $ticket['quantity_available'] - $ticket['quantity_sold'];
if ($available <= 0) {
    json_response(['success' => false, 'message' => 'This ticket type is sold out'], 400);
}

if ($quantity > $available) {
    json_response(['success' => false, 'message' => 'Only ' . $available . ' tickets available'], 400);
}

// Calculate total amount
$totalAmount = ((float) $ticket['price']) * $quantity;

// Apply coupon if provided
$couponCode = clean_text(request('coupon_code', ''));
$appliedDiscount = 0;
if ($couponCode) {
    $stmt = pdo()->prepare('SELECT * FROM coupons WHERE code = :code AND event_id = :event_id AND valid_from <= NOW() AND valid_to >= NOW() LIMIT 1');
    $stmt->execute([':code' => $couponCode, ':event_id' => $eventId]);
    $coupon = $stmt->fetch();
    
    if ($coupon) {
        // Check if coupon is still valid (usage limit)
        $stmt = pdo()->prepare('SELECT COUNT(*) AS usage_count FROM coupon_usage WHERE coupon_id = :coupon_id');
        $stmt->execute([':coupon_id' => $coupon['id']]);
        $usageCount = (int) $stmt->fetch()['usage_count'];
        
        if ($coupon['max_usage'] > 0 && $usageCount >= $coupon['max_usage']) {
            json_response(['success' => false, 'message' => 'Coupon usage limit exceeded'], 400);
        }
        
        $appliedDiscount = $coupon['discount_percent'] ?? 0;
        $totalAmount = $totalAmount - (($totalAmount * $appliedDiscount) / 100);
    }
}

// Get or create user account for guest registration
$currentUser = event_current_user();
$userId = null;

if ($currentUser) {
    // Logged-in user
    $userId = $currentUser['id'];
} else {
    // Check if user exists by email
    $stmt = pdo()->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $existingUser = $stmt->fetch();
    
    if ($existingUser) {
        $userId = $existingUser['id'];
    } else {
        // Create guest account
        $organization = clean_text(request('organization', ''));
        $stmt = pdo()->prepare('INSERT INTO users (name, email, phone, organization, role, password_hash, email_verified) VALUES (:name, :email, :phone, :organization, "participant", :password_hash, 1)');
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':organization' => $organization,
            ':password_hash' => password_hash(bin2hex(random_bytes(16)), PASSWORD_BCRYPT),
        ]);
        $userId = pdo()->lastInsertId();
    }
}

// Generate registration
$regNo = event_registration_number();
$qr = event_qr_url($regNo);
$specialRequests = clean_text(request('special_requests', ''));
$paymentMethod = clean_text(request('payment_method', 'free'));
$isFree = (int) $event['is_free'] === 1 || ((float) $event['price'] <= 0) || ((float) $totalAmount <= 0);
$paymentStatus = $isFree ? 'free' : 'pending';

$stmt = pdo()->prepare('INSERT INTO registrations (event_id, user_id, registration_number, ticket_type_id, quantity, total_amount, payment_status, qr_code_path, check_in_status, registered_at, special_requests, coupon_code) VALUES (:event_id, :user_id, :registration_number, :ticket_type_id, :quantity, :total_amount, :payment_status, :qr_code_path, "not_checked_in", NOW(), :special_requests, :coupon_code)');
$stmt->execute([
    ':event_id' => $eventId,
    ':user_id' => $userId,
    ':registration_number' => $regNo,
    ':ticket_type_id' => $ticketTypeId,
    ':quantity' => $quantity,
    ':total_amount' => $totalAmount,
    ':payment_status' => $paymentStatus,
    ':qr_code_path' => $qr,
    ':special_requests' => $specialRequests,
    ':coupon_code' => $couponCode,
]);

$registrationId = pdo()->lastInsertId();

// Update ticket sold count
pdo()->prepare('UPDATE ticket_types SET quantity_sold = quantity_sold + :qty WHERE id = :id')->execute([
    ':qty' => $quantity,
    ':id' => $ticketTypeId,
]);

// Record coupon usage
if ($couponCode) {
    $stmt = pdo()->prepare('SELECT id FROM coupons WHERE code = :code LIMIT 1');
    $stmt->execute([':code' => $couponCode]);
    $coupon = $stmt->fetch();
    if ($coupon) {
        pdo()->prepare('INSERT INTO coupon_usage (coupon_id, registration_id, used_at) VALUES (:coupon_id, :registration_id, NOW())')->execute([
            ':coupon_id' => $coupon['id'],
            ':registration_id' => $registrationId,
        ]);
    }
}

// Send confirmation email
$to = $email;
$subject = 'Registration Confirmed - ' . $event['title'];
$body = "Dear " . $name . ",\n\n";
$body .= "Thank you for registering for " . $event['title'] . "!\n\n";
$body .= "Registration Number: " . $regNo . "\n";
$body .= "Event Date: " . date('F d, Y', strtotime($event['event_date'])) . "\n";
$body .= "Venue: " . $event['venue'] . "\n";
$body .= "Total Amount: $" . number_format((float) $totalAmount, 2) . "\n\n";
$body .= "Your QR Code: " . $qr . "\n\n";
$body .= "Please keep this registration number safe. You'll need it for check-in.\n\n";
$body .= "Best regards,\nEvent Team";

$headers = "From: noreply@events.local\r\nContent-Type: text/plain; charset=UTF-8";
mail($to, $subject, $body, $headers);

json_response([
    'success' => true,
    'registration_number' => $regNo,
    'qr_code' => $qr,
    'event_title' => $event['title'],
    'total_amount' => $totalAmount,
    'payment_status' => $paymentStatus,
]);
