# Event Registration System - Guide

## Overview

The enhanced event registration system provides a comprehensive, multi-step registration flow with coupon support, real-time validation, and detailed confirmation.

## Key Features

### 1. **Multi-Step Registration Form** (`/public/register.php`)

The registration form has been redesigned with 5 organized steps:

#### Step 1: Ticket Selection
- Browse available ticket types for the event
- See real-time availability (tickets remaining)
- Display of unit price for each ticket
- Quantity selector with availability validation

#### Step 2: Attendee Information
- Email address (required, with validation)
- Full name (required, 2-150 characters)
- Phone number (required, validates international format)
- Organization (optional)
- Prepopulated for logged-in users

#### Step 3: Special Requests (Optional)
- Textarea for dietary restrictions
- Accessibility needs
- Other special requirements

#### Step 4: Coupon & Payment
- Coupon code input with validation
- Real-time discount preview
- Payment method selection (Free/Razorpay/UPI)
- Applied coupon display in order summary

#### Step 5: Terms & Conditions
- Mandatory agreement checkbox
- Optional newsletter subscription checkbox

### 2. **Order Summary Sidebar**

Real-time calculation and display of:
- Selected ticket type and name
- Quantity
- Unit price
- Applied coupon discount (if any)
- Total amount

## Registration Flow

```
1. User selects event → clicks "Register"
   ↓
2. /public/register.php loads with event details
   ↓
3. User fills out 5-step form with validation
   ↓
4. Form submits to /api/register.php (AJAX)
   ↓
5. Server validates all inputs
   ↓
6. Server creates user account if needed (guest)
   ↓
7. Server creates registration record
   ↓
8. Server generates QR code
   ↓
9. Server sends confirmation email
   ↓
10. JavaScript redirects to /public/confirm.php
    ↓
11. Confirmation page displays order summary + QR code
```

## API Endpoints

### POST `/api/register.php`

**Required Fields:**
- `csrf_token` - CSRF security token
- `event_id` - Event ID (integer)
- `ticket_type_id` - Selected ticket type ID (integer)
- `quantity` - Number of tickets (integer, min 1)
- `email` - Attendee email (valid email format)
- `name` - Attendee full name (2-150 characters)
- `phone` - Phone number (7+ characters)
- `agree_terms` - Must be "on" for registration to succeed

**Optional Fields:**
- `organization` - Organization name
- `special_requests` - Text for special needs/dietary restrictions
- `coupon_code` - Coupon code for discount
- `payment_method` - "free", "razorpay", or "upi"
- `agree_emails` - Subscribe to updates

**Response (JSON):**
```json
{
  "success": true,
  "registration_number": "REG20260504123045789",
  "qr_code": "https://chart.googleapis.com/...",
  "event_title": "Tech Summit 2026",
  "total_amount": 850.00,
  "payment_status": "pending"
}
```

**Validation Checks:**
- CSRF token verification
- Email format validation
- Name length (2-150 characters)
- Phone format (7+ characters)
- Event exists and is published
- Registration deadline not passed
- Ticket type exists for event
- Sufficient ticket availability
- User account created if doesn't exist
- Coupon code validation (if provided)

### POST `/api/coupon.php`

**Parameters:**
- `action` - "validate" (currently only action)
- `code` - Coupon code to validate

**Response (JSON):**
```json
{
  "success": true,
  "discount": 15.0,
  "message": "Coupon applied successfully"
}
```

**Validations:**
- Code must exist
- Coupon must be active
- Current date within valid_from and valid_to
- Usage limit not exceeded (if max_usage set)

## Database Schema

### registrations table (Enhanced)
```sql
- special_requests (TEXT) - Any special requirements
- coupon_code (VARCHAR(50)) - Applied coupon code
```

### New tables:
```sql
coupons:
- event_id (INT) - Event coupon applies to
- code (VARCHAR(50)) - Unique coupon code
- discount_percent (DECIMAL(5,2)) - Discount percentage
- valid_from (DATETIME) - Start of validity
- valid_to (DATETIME) - End of validity
- max_usage (INT) - Maximum times coupon can be used
- status (ENUM) - active/inactive/expired

coupon_usage:
- coupon_id (INT) - Reference to coupons
- registration_id (INT) - Reference to registrations
- used_at (DATETIME) - When coupon was used
```

## Confirmation Page Features (`/public/confirm.php`)

The confirmation page displays:

1. **Success Banner** - Animated green checkmark
2. **QR Code Section**
   - Large, scannable QR code
   - Registration number
   - Print button for physical confirmation
3. **Event Details**
   - Event title, date, time
   - Venue and full address
4. **Ticket Information**
   - Ticket type selected
   - Unit price
   - Quantity
   - Subtotal with coupon deduction (if applied)
   - Total amount
   - Payment status badge
5. **Attendee Information**
   - Name, email, phone, organization
6. **Next Steps**
   - 4-step guide on what happens next
7. **Action Buttons**
   - Browse more events
   - Print confirmation

## Security Features

1. **CSRF Token Validation** - All POST requests require valid CSRF token
2. **Input Sanitization** - All text inputs cleaned with `clean_text()`
3. **Email Validation** - `filter_var($email, FILTER_VALIDATE_EMAIL)`
4. **Prepared Statements** - All database queries use parameterized queries
5. **Phone Validation** - Minimum 7 characters
6. **Ticket Availability Check** - Prevents overbooking
7. **Event Status Check** - Only published events can be registered
8. **Coupon Usage Limits** - Enforced per coupon and registration

## User Experience Features

1. **Real-Time Price Calculation** - Updates as user changes quantity or applies coupon
2. **Live Availability Check** - Warns if not enough tickets available
3. **Form Autofill** - Pre-populated for logged-in users
4. **Feedback Messages** - Clear error/success messages for coupon validation
5. **Responsive Design** - Works on mobile, tablet, and desktop
6. **Print-Friendly** - Confirmation page optimized for printing
7. **Progress Indicator** - Clear 5-step form structure

## Email Features

Confirmation emails are sent with:
- Registration number
- Event details (date, venue)
- Ticket information
- Total amount
- QR code URL
- Check-in instructions

## Sample Coupons (Seed Data)

| Code | Event | Discount | Max Usage | Valid |
|------|-------|----------|-----------|-------|
| EARLY15 | Tech Summit 2026 | 15% | 50 | 5/1 - 6/10 |
| STUDENT10 | Tech Summit 2026 | 10% | 100 | 5/1 - 6/15 |
| FOUNDER30 | Startup Bootcamp | 30% | 20 | 6/1 - 7/5 |
| TECH20 | Design Meetup | 20% | 30 | 5/1 - 5/25 |

## Testing the Flow

### 1. Test Guest Registration
```
1. Navigate to /public/listing.php
2. Click on an event
3. Click "Register" button
4. Fill in all required fields
5. Submit - user account is created
6. Verify confirmation page shows order summary
```

### 2. Test Coupon Validation
```
1. In registration form, select a ticket
2. Enter coupon code: EARLY15
3. Click "Apply" button
4. Verify discount appears in summary
5. Submit registration with discount applied
```

### 3. Test Payment Status
```
- Free events → Payment Status: "Free Registration"
- Paid events → Payment Status: "Payment Pending"
- Confirm coupon discount is shown
```

### 4. Test QR Code
```
1. Complete registration
2. View confirmation page
3. QR code should be scannable (try phone camera)
4. Print confirmation page and verify QR prints correctly
```

## JavaScript Functions (in register.php)

- `updateTicketInfo()` - Updates unit price and availability when ticket changes
- `updateTotalPrice()` - Recalculates total with quantity and coupon
- `validateCoupon()` - AJAX call to validate coupon code
- `submitRegistration(e)` - Form submission handler with validation

## Troubleshooting

**Issue: Coupon not applying**
- Check coupon code is case-sensitive
- Verify coupon status is "active"
- Check valid_from and valid_to dates
- Verify max_usage limit not exceeded

**Issue: Ticket availability mismatch**
- Check quantity_available and quantity_sold in ticket_types
- Manual SQL query to verify: `SELECT quantity_available - quantity_sold as available FROM ticket_types WHERE id = X`

**Issue: Email not sending**
- Verify PHP mail() is configured
- Check logs: `/var/log/mail.log`
- Alternative: Implement PHPMailer for SMTP

**Issue: QR code not displaying**
- Verify Google Charts API is accessible
- Check registration_number format: should start with "REG"

## Future Enhancements

1. Payment gateway integration (Razorpay)
2. Email template customization
3. Multi-ticket purchase (bulk group registration)
4. Referral code system
5. Dynamic pricing based on registration count
6. Waitlist auto-promotion on cancellation
7. Registration modifications (add tickets to existing registration)
8. Attendance certificate generation
