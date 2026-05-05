<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

$reg = clean_text(request('reg', ''));
$stmt = pdo()->prepare('
    SELECT 
        r.*, 
        e.title AS event_title, 
        e.event_date,
        e.start_time,
        e.venue,
        e.city,
        e.address,
        t.name AS ticket_name,
        t.price AS ticket_price,
        u.name AS user_name,
        u.email AS user_email,
        u.phone AS user_phone,
        u.organization AS user_organization
    FROM registrations r 
    LEFT JOIN events e ON e.id = r.event_id 
    LEFT JOIN ticket_types t ON t.id = r.ticket_type_id
    LEFT JOIN users u ON u.id = r.user_id
    WHERE r.registration_number = :registration_number 
    LIMIT 1
');
$stmt->execute([':registration_number' => $reg]);
$registration = $stmt->fetch();

if (!$registration) {
    http_response_code(404);
    echo 'Registration not found';
    exit;
}

require __DIR__ . '/../includes/header.php';
?>

<div class="confirmation-container">
    <div class="confirmation-card success">
        <div class="success-icon">✓</div>
        <h1>Registration Confirmed!</h1>
        <p class="confirmation-subtitle">Your registration has been successfully processed.</p>
    </div>

    <div class="confirmation-details">
        <!-- QR Code Section -->
        <section class="qr-section">
            <h2>Your Check-In Code</h2>
            <p class="qr-instructions">Show this QR code at the event entrance for quick check-in</p>
            <div class="qr-code-wrapper">
                <img src="<?php echo e($registration['qr_code_path']); ?>" alt="QR Code" class="qr-code-image">
            </div>
            <p class="registration-number">Registration #: <strong><?php echo e($registration['registration_number']); ?></strong></p>
            <button class="btn-secondary" onclick="window.print()">Print QR Code</button>
        </section>

        <!-- Event Details Section -->
        <section class="event-details-section">
            <h2>Event Details</h2>
            <div class="detail-group">
                <div class="detail-item">
                    <span class="detail-label">Event</span>
                    <span class="detail-value"><?php echo e($registration['event_title']); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Date</span>
                    <span class="detail-value"><?php echo date('l, F d, Y', strtotime($registration['event_date'])); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Time</span>
                    <span class="detail-value"><?php echo date('g:i A', strtotime($registration['start_time'])); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Venue</span>
                    <span class="detail-value"><?php echo e($registration['venue']); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Location</span>
                    <span class="detail-value"><?php echo e($registration['address']); ?>, <?php echo e($registration['city']); ?></span>
                </div>
            </div>
        </section>

        <!-- Ticket Information Section -->
        <section class="ticket-info-section">
            <h2>Ticket Information</h2>
            <table class="ticket-table">
                <tr>
                    <td class="ticket-label">Ticket Type</td>
                    <td class="ticket-value"><?php echo e($registration['ticket_name']); ?></td>
                </tr>
                <tr>
                    <td class="ticket-label">Unit Price</td>
                    <td class="ticket-value">$<?php echo number_format((float) $registration['ticket_price'], 2); ?></td>
                </tr>
                <tr>
                    <td class="ticket-label">Quantity</td>
                    <td class="ticket-value"><?php echo (int) $registration['quantity']; ?></td>
                </tr>
                <tr>
                    <td class="ticket-label">Subtotal</td>
                    <td class="ticket-value">$<?php echo number_format(((float) $registration['ticket_price'] * (int) $registration['quantity']), 2); ?></td>
                </tr>
                <?php if ($registration['coupon_code']): ?>
                <tr class="coupon-row">
                    <td class="ticket-label">Coupon Applied</td>
                    <td class="ticket-value">-$<?php echo number_format(((float) $registration['ticket_price'] * (int) $registration['quantity']) - (float) $registration['total_amount'], 2); ?></td>
                </tr>
                <?php endif; ?>
                <tr class="total-row">
                    <td class="ticket-label"><strong>Total Amount</strong></td>
                    <td class="ticket-value"><strong>$<?php echo number_format((float) $registration['total_amount'], 2); ?></strong></td>
                </tr>
            </table>

            <!-- Payment Status -->
            <div class="payment-status <?php echo strtolower($registration['payment_status']); ?>">
                <span class="status-icon">●</span>
                <span class="status-text">
                    <?php 
                    if ($registration['payment_status'] === 'free') {
                        echo 'Free Registration - No payment required';
                    } elseif ($registration['payment_status'] === 'paid') {
                        echo 'Payment Completed';
                    } else {
                        echo 'Payment Pending - Please complete payment to confirm your seat';
                    }
                    ?>
                </span>
            </div>
        </section>

        <!-- Attendee Information Section -->
        <section class="attendee-info-section">
            <h2>Attendee Information</h2>
            <div class="detail-group">
                <div class="detail-item">
                    <span class="detail-label">Name</span>
                    <span class="detail-value"><?php echo e($registration['user_name']); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Email</span>
                    <span class="detail-value"><?php echo e($registration['user_email']); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Phone</span>
                    <span class="detail-value"><?php echo e($registration['user_phone']); ?></span>
                </div>
                <?php if ($registration['user_organization']): ?>
                <div class="detail-item">
                    <span class="detail-label">Organization</span>
                    <span class="detail-value"><?php echo e($registration['user_organization']); ?></span>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Special Requests Section -->
        <?php if ($registration['special_requests']): ?>
        <section class="special-requests-section">
            <h2>Special Requests</h2>
            <p class="special-requests-text"><?php echo nl2br(e($registration['special_requests'])); ?></p>
        </section>
        <?php endif; ?>

        <!-- Next Steps Section -->
        <section class="next-steps-section">
            <h2>What Happens Next?</h2>
            <ol class="steps-list">
                <li>
                    <strong>Confirmation Email</strong>
                    <p>You'll receive a confirmation email at <?php echo e($registration['user_email']); ?> with all the details</p>
                </li>
                <li>
                    <strong>Check-In on Event Day</strong>
                    <p>Show your QR code at the entrance. Our team will scan it for check-in</p>
                </li>
                <li>
                    <strong>Event Updates</strong>
                    <p>Watch your email for any updates or changes to the event schedule</p>
                </li>
                <li>
                    <strong>Post-Event Feedback</strong>
                    <p>After the event, we'll send you a feedback form - your feedback helps us improve!</p>
                </li>
            </ol>
        </section>

        <!-- Action Buttons -->
        <div class="confirmation-actions">
            <a href="listing.php" class="btn-primary">Browse More Events</a>
            <a href="javascript:window.print()" class="btn-secondary">Print This Confirmation</a>
        </div>
    </div>
</div>

<style>
.confirmation-container {
    max-width: 900px;
    margin: 40px auto;
    padding: 0 20px;
}

.confirmation-card {
    text-align: center;
    padding: 40px 30px;
    background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
    color: white;
    border-radius: 8px;
    margin-bottom: 40px;
}

.success-icon {
    font-size: 3em;
    margin-bottom: 15px;
    animation: scaleIn 0.6s ease-out;
}

@keyframes scaleIn {
    from {
        transform: scale(0);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

.confirmation-card h1 {
    margin: 0 0 10px 0;
    font-size: 2em;
}

.confirmation-subtitle {
    margin: 0;
    opacity: 0.95;
}

.confirmation-details {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 40px;
    margin-bottom: 40px;
}

.qr-section {
    background: white;
    padding: 30px;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.qr-section h2 {
    margin-top: 0;
    color: #2c3e50;
}

.qr-instructions {
    color: #7f8c8d;
    font-size: 0.9em;
    margin-bottom: 20px;
}

.qr-code-wrapper {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 6px;
    margin: 20px 0;
    display: flex;
    justify-content: center;
}

.qr-code-image {
    width: 200px;
    height: 200px;
    border: 2px solid #e0e0e0;
    border-radius: 4px;
}

.registration-number {
    margin: 15px 0;
    color: #2c3e50;
}

.event-details-section,
.ticket-info-section,
.attendee-info-section,
.special-requests-section,
.next-steps-section {
    background: white;
    padding: 25px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.event-details-section h2,
.ticket-info-section h2,
.attendee-info-section h2,
.special-requests-section h2,
.next-steps-section h2 {
    margin-top: 0;
    color: #2c3e50;
    font-size: 1.2em;
}

.detail-group {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.detail-item {
    display: flex;
    flex-direction: column;
}

.detail-label {
    font-weight: 600;
    color: #7f8c8d;
    font-size: 0.9em;
    margin-bottom: 5px;
}

.detail-value {
    color: #2c3e50;
    font-size: 1em;
}

.ticket-table {
    width: 100%;
    margin: 20px 0;
    border-collapse: collapse;
}

.ticket-table tr {
    border-bottom: 1px solid #ecf0f1;
}

.ticket-table td {
    padding: 12px 0;
}

.ticket-label {
    color: #7f8c8d;
    font-weight: 500;
}

.ticket-value {
    text-align: right;
    color: #2c3e50;
}

.coupon-row .ticket-value {
    color: #27ae60;
    font-weight: 600;
}

.total-row {
    border-top: 2px solid #ecf0f1;
    padding-top: 12px !important;
}

.payment-status {
    margin-top: 20px;
    padding: 15px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.payment-status.free {
    background: #d4edda;
    color: #155724;
}

.payment-status.paid {
    background: #d4edda;
    color: #155724;
}

.payment-status.pending {
    background: #fff3cd;
    color: #856404;
}

.status-icon {
    font-size: 1.5em;
}

.status-text {
    font-weight: 500;
}

.special-requests-text {
    color: #2c3e50;
    white-space: pre-wrap;
    line-height: 1.6;
}

.steps-list {
    list-style: decimal;
    padding-left: 25px;
    color: #2c3e50;
}

.steps-list li {
    margin-bottom: 20px;
    line-height: 1.6;
}

.steps-list strong {
    color: #27ae60;
}

.steps-list p {
    margin: 8px 0 0 0;
    color: #7f8c8d;
    font-size: 0.95em;
}

.confirmation-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-primary, .btn-secondary {
    padding: 12px 30px;
    border: none;
    border-radius: 4px;
    font-size: 1em;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    transition: background 0.3s;
}

.btn-primary {
    background: #27ae60;
    color: white;
}

.btn-primary:hover {
    background: #229954;
}

.btn-secondary {
    background: #95a5a6;
    color: white;
}

.btn-secondary:hover {
    background: #7f8c8d;
}

@media (max-width: 768px) {
    .confirmation-details {
        grid-template-columns: 1fr;
    }
    
    .detail-group {
        grid-template-columns: 1fr;
    }
    
    .confirmation-actions {
        flex-direction: column;
    }
    
    .confirmation-actions a {
        width: 100%;
        text-align: center;
    }
}

@media print {
    .confirmation-actions {
        display: none;
    }
    
    body {
        background: white;
    }
}
</style>

<?php require __DIR__ . '/../includes/footer.php'; ?>
