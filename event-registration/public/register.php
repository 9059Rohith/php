<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

$event = event_by_slug(clean_text(request('event', '')));
if (!$event) {
    http_response_code(404);
    echo 'Event not found';
    exit;
}

$tickets = event_ticket_types((int) $event['id']);
$currentUser = event_current_user();
$errors = [];

// Parse query params if returning from coupon validation
$appliedCoupon = null;
$couponDiscount = 0;
if (request('coupon')) {
    $appliedCoupon = clean_text(request('coupon', ''));
}

require __DIR__ . '/../includes/header.php';
?>
<div class="container registration-container">
    <div class="registration-wrapper">
        <!-- Event Summary -->
        <aside class="event-summary">
            <div class="event-header">
                <h2><?php echo e($event['title']); ?></h2>
                <p class="event-meta"><?php echo date('M d, Y', strtotime($event['event_date'])); ?> at <?php echo date('g:i A', strtotime($event['start_time'])); ?></p>
                <p class="event-location"><?php echo e($event['venue']); ?>, <?php echo e($event['city']); ?></p>
            </div>
            
            <div class="ticket-summary" id="ticketSummary">
                <h3>Your Registration</h3>
                <div class="summary-item">
                    <span>Ticket Type:</span>
                    <span id="selectedTicketName">—</span>
                </div>
                <div class="summary-item">
                    <span>Quantity:</span>
                    <span id="selectedQuantity">1</span>
                </div>
                <div class="summary-item">
                    <span>Unit Price:</span>
                    <span id="unitPrice">$0.00</span>
                </div>
                <div class="summary-item" id="couponSummary" style="display:none;">
                    <span>Coupon Discount:</span>
                    <span id="discountAmount">-$0.00</span>
                </div>
                <div class="summary-item summary-total">
                    <span>Total:</span>
                    <strong id="totalPrice">$0.00</strong>
                </div>
            </div>
        </aside>

        <!-- Registration Form -->
        <main class="registration-form-section">
            <form id="registrationForm" class="register-form" onsubmit="submitRegistration(event)">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="event_id" value="<?php echo (int) $event['id']; ?>">

                <!-- Step 1: Ticket Selection -->
                <fieldset class="form-section">
                    <legend>1. Select Your Ticket</legend>
                    <div class="form-group">
                        <label for="ticketType">Ticket Type *</label>
                        <select id="ticketType" name="ticket_type_id" required onchange="updateTicketInfo()">
                            <option value="">Choose a ticket type...</option>
                            <?php foreach ($tickets as $ticket): 
                                $available = $ticket['quantity_available'] - $ticket['quantity_sold'];
                                $disabled = $available <= 0;
                            ?>
                                <option value="<?php echo e($ticket['id']); ?>" 
                                    data-price="<?php echo (float) $ticket['price']; ?>"
                                    data-available="<?php echo $available; ?>"
                                    data-name="<?php echo e($ticket['name']); ?>"
                                    <?php echo $disabled ? 'disabled' : ''; ?>>
                                    <?php echo e($ticket['name']); ?> ($<?php echo number_format((float) $ticket['price'], 2); ?>) - <?php echo $available; ?> available
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="quantity">Number of Tickets *</label>
                        <input type="number" id="quantity" name="quantity" min="1" value="1" required onchange="updateTotalPrice()">
                        <small id="availabilityWarning" style="color: #e74c3c; display: none;"></small>
                    </div>
                </fieldset>

                <!-- Step 2: Attendee Information -->
                <fieldset class="form-section">
                    <legend>2. Your Information</legend>
                    
                    <div class="form-group">
                        <label for="attendeeEmail">Email *</label>
                        <input type="email" id="attendeeEmail" name="email" required 
                            value="<?php echo $currentUser ? e($currentUser['email']) : ''; ?>" 
                            placeholder="your@email.com">
                    </div>

                    <div class="form-group">
                        <label for="attendeeName">Full Name *</label>
                        <input type="text" id="attendeeName" name="name" required 
                            value="<?php echo $currentUser ? e($currentUser['name']) : ''; ?>" 
                            placeholder="John Doe">
                    </div>

                    <div class="form-group">
                        <label for="attendeePhone">Phone Number *</label>
                        <input type="tel" id="attendeePhone" name="phone" required 
                            value="<?php echo $currentUser ? e($currentUser['phone'] ?? '') : ''; ?>" 
                            placeholder="+1 (555) 123-4567">
                    </div>

                    <div class="form-group">
                        <label for="organization">Organization (Optional)</label>
                        <input type="text" id="organization" name="organization" 
                            value="<?php echo $currentUser ? e($currentUser['organization'] ?? '') : ''; ?>" 
                            placeholder="Your company or school">
                    </div>
                </fieldset>

                <!-- Step 3: Special Requests -->
                <fieldset class="form-section">
                    <legend>3. Special Requests (Optional)</legend>
                    <div class="form-group">
                        <label for="specialRequests">Dietary restrictions, accessibility needs, etc.</label>
                        <textarea id="specialRequests" name="special_requests" rows="3" placeholder="Let us know if you have any special requirements..."></textarea>
                    </div>
                </fieldset>

                <!-- Step 4: Coupon & Payment -->
                <fieldset class="form-section">
                    <legend>4. Coupon & Payment</legend>
                    
                    <div class="coupon-section">
                        <div class="form-group coupon-input-group">
                            <label for="couponCode">Have a coupon code?</label>
                            <div class="coupon-input-wrapper">
                                <input type="text" id="couponCode" name="coupon_code" placeholder="Enter coupon code" 
                                    value="<?php echo $appliedCoupon ? e($appliedCoupon) : ''; ?>">
                                <button type="button" class="btn-secondary" onclick="validateCoupon()">Apply</button>
                            </div>
                            <div id="couponFeedback" class="form-feedback"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="paymentMethod">Payment Method</label>
                        <select id="paymentMethod" name="payment_method">
                            <option value="free" <?php echo (int) $event['is_free'] === 1 ? 'selected' : ''; ?>>Free Registration</option>
                            <option value="razorpay" <?php echo (int) $event['is_free'] === 0 ? 'selected' : ''; ?>>Credit/Debit Card (Razorpay)</option>
                            <option value="upi">UPI</option>
                        </select>
                    </div>
                </fieldset>

                <!-- Step 5: Terms & Conditions -->
                <fieldset class="form-section">
                    <legend>5. Terms & Conditions</legend>
                    <div class="form-group checkbox-group">
                        <input type="checkbox" id="agreeTerms" name="agree_terms" required>
                        <label for="agreeTerms">I agree to the <a href="#" target="_blank">terms and conditions</a> *</label>
                    </div>
                    <div class="form-group checkbox-group">
                        <input type="checkbox" id="agreeEmails" name="agree_emails">
                        <label for="agreeEmails">Send me event updates and promotional offers</label>
                    </div>
                </fieldset>

                <!-- Submit -->
                <div class="form-actions">
                    <button type="submit" class="btn-primary btn-lg" id="submitBtn">Complete Registration</button>
                    <a href="listing.php" class="btn-link">Back to events</a>
                </div>
            </form>
        </main>
    </div>
</div>

<style>
.registration-container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
}

.registration-wrapper {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 40px;
    background: #fff;
    border-radius: 8px;
}

.event-summary {
    position: sticky;
    top: 20px;
    height: fit-content;
}

.event-header {
    border-bottom: 2px solid #ecf0f1;
    padding-bottom: 20px;
    margin-bottom: 20px;
}

.event-header h2 {
    margin: 0 0 10px 0;
    font-size: 1.5em;
}

.event-meta, .event-location {
    margin: 5px 0;
    color: #7f8c8d;
    font-size: 0.9em;
}

.ticket-summary {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 6px;
}

.ticket-summary h3 {
    margin-top: 0;
    font-size: 1.1em;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    margin: 12px 0;
    font-size: 0.95em;
}

.summary-item span:first-child {
    color: #7f8c8d;
}

.summary-total {
    border-top: 1px solid #ddd;
    padding-top: 12px;
    margin-top: 12px;
    font-size: 1.1em;
}

.register-form {
    display: flex;
    flex-direction: column;
    gap: 30px;
}

.form-section {
    border: none;
    padding: 0;
    margin: 0;
}

.form-section legend {
    font-size: 1.1em;
    font-weight: 600;
    margin-bottom: 15px;
    color: #2c3e50;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #2c3e50;
}

.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="tel"],
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #bdc3c7;
    border-radius: 4px;
    font-size: 1em;
    font-family: inherit;
}

.form-group textarea {
    resize: vertical;
    min-height: 80px;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.coupon-input-wrapper {
    display: flex;
    gap: 10px;
}

.coupon-input-wrapper input {
    flex: 1;
}

.btn-secondary {
    padding: 10px 20px;
    background: #95a5a6;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.9em;
    font-weight: 600;
    white-space: nowrap;
}

.btn-secondary:hover {
    background: #7f8c8d;
}

.form-feedback {
    margin-top: 8px;
    font-size: 0.9em;
    padding: 8px 10px;
    border-radius: 4px;
    display: none;
}

.form-feedback.success {
    display: block;
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.form-feedback.error {
    display: block;
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.checkbox-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.checkbox-group input[type="checkbox"] {
    width: auto;
    margin: 0;
}

.checkbox-group label {
    margin-bottom: 0;
}

.checkbox-group a {
    color: #3498db;
    text-decoration: none;
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 2px solid #ecf0f1;
}

.btn-primary {
    padding: 12px 30px;
    background: #27ae60;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 1em;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s;
}

.btn-primary:hover {
    background: #229954;
}

.btn-primary:disabled {
    background: #bdc3c7;
    cursor: not-allowed;
}

.btn-lg {
    padding: 14px 40px;
    font-size: 1.1em;
}

.btn-link {
    color: #3498db;
    text-decoration: none;
    padding: 12px 20px;
}

.btn-link:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .registration-wrapper {
        grid-template-columns: 1fr;
    }
    
    .event-summary {
        position: static;
    }
    
    .form-actions {
        flex-direction: column;
    }
}
</style>

<script>
function updateTicketInfo() {
    const ticketSelect = document.getElementById('ticketType');
    const selected = ticketSelect.options[ticketSelect.selectedIndex];
    
    if (selected.value) {
        const price = parseFloat(selected.dataset.price);
        const available = parseInt(selected.dataset.available);
        const name = selected.dataset.name;
        
        document.getElementById('selectedTicketName').textContent = name;
        document.getElementById('unitPrice').textContent = '$' + price.toFixed(2);
        
        // Reset quantity
        const qtyInput = document.getElementById('quantity');
        qtyInput.value = 1;
        qtyInput.max = available;
        
        updateTotalPrice();
    }
}

function updateTotalPrice() {
    const ticketSelect = document.getElementById('ticketType');
    const qtyInput = document.getElementById('quantity');
    
    if (!ticketSelect.value) {
        document.getElementById('totalPrice').textContent = '$0.00';
        return;
    }
    
    const selected = ticketSelect.options[ticketSelect.selectedIndex];
    const price = parseFloat(selected.dataset.price);
    const available = parseInt(selected.dataset.available);
    const quantity = Math.max(1, Math.min(parseInt(qtyInput.value) || 1, available));
    
    // Check availability
    const warning = document.getElementById('availabilityWarning');
    if (quantity > available) {
        warning.textContent = 'Only ' + available + ' tickets available';
        warning.style.display = 'block';
        qtyInput.value = available;
    } else {
        warning.style.display = 'none';
    }
    
    document.getElementById('selectedQuantity').textContent = quantity;
    let total = price * quantity;
    
    // Apply coupon discount if applicable
    const couponFeedback = document.getElementById('couponFeedback');
    if (couponFeedback.classList.contains('success')) {
        const discountPercent = parseFloat(couponFeedback.dataset.discount) || 0;
        const discount = (total * discountPercent) / 100;
        total -= discount;
        document.getElementById('discountAmount').textContent = '-$' + discount.toFixed(2);
        document.getElementById('couponSummary').style.display = 'flex';
    } else {
        document.getElementById('couponSummary').style.display = 'none';
    }
    
    document.getElementById('totalPrice').textContent = '$' + total.toFixed(2);
}

async function validateCoupon() {
    const couponCode = document.getElementById('couponCode').value.trim();
    const feedback = document.getElementById('couponFeedback');
    
    if (!couponCode) {
        feedback.textContent = 'Please enter a coupon code';
        feedback.className = 'form-feedback error';
        return;
    }
    
    if (!document.getElementById('ticketType').value) {
        feedback.textContent = 'Please select a ticket first';
        feedback.className = 'form-feedback error';
        return;
    }
    
    try {
        const response = await fetch('../api/coupon.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=validate&code=' + encodeURIComponent(couponCode)
        });
        
        const result = await response.json();
        
        if (result.success) {
            feedback.textContent = 'Coupon applied! ' + result.discount + '% discount';
            feedback.className = 'form-feedback success';
            feedback.dataset.discount = result.discount;
            updateTotalPrice();
        } else {
            feedback.textContent = result.message || 'Invalid coupon code';
            feedback.className = 'form-feedback error';
        }
    } catch (error) {
        console.error('Coupon validation error:', error);
        feedback.textContent = 'Error validating coupon';
        feedback.className = 'form-feedback error';
    }
}

async function submitRegistration(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('registrationForm');
    
    // Validation
    if (!document.getElementById('ticketType').value) {
        alert('Please select a ticket type');
        return;
    }
    
    if (!document.getElementById('attendeeEmail').value || !document.getElementById('attendeeName').value || !document.getElementById('attendeePhone').value) {
        alert('Please fill in all required fields');
        return;
    }
    
    if (!document.getElementById('agreeTerms').checked) {
        alert('Please agree to the terms and conditions');
        return;
    }
    
    submitBtn.disabled = true;
    submitBtn.textContent = 'Processing...';
    
    try {
        const formData = new FormData(form);
        const response = await fetch('../api/register.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Redirect to confirmation page with registration number
            window.location.href = 'confirm.php?reg=' + encodeURIComponent(result.registration_number);
        } else {
            alert(result.message || 'Registration failed. Please try again.');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Complete Registration';
        }
    } catch (error) {
        console.error('Registration error:', error);
        alert('An error occurred. Please try again.');
        submitBtn.disabled = false;
        submitBtn.textContent = 'Complete Registration';
    }
}

// Initialize ticket info on page load
document.addEventListener('DOMContentLoaded', function() {
    updateTicketInfo();
});
</script>
<?php require __DIR__ . '/../includes/footer.php'; ?>
