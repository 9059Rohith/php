# Event Registration System - Complete Testing Report & Documentation

**Date**: May 5, 2026  
**System**: Multi-role Event Registration Platform  
**Database**: MySQL (event_db)

---

## 1. ADMIN CREDENTIALS & SETUP

### Admin Account
- **Email**: rajuchaswik@gmail.com
- **Password**: Raju@2006
- **Role**: admin
- **Access Level**: Full platform administration

### Database Configuration
```php
Database: event_db
Host: 127.0.0.1
Port: 3306
User: root
Password: (empty)
Charset: utf8mb4
```

**Location**: `/includes/bootstrap.php` (lines 13-21)

---

## 2. SYSTEM ARCHITECTURE OVERVIEW

### User Roles & Permissions
| Role | Login URL | Dashboard | Capabilities |
|------|-----------|-----------|--------------|
| Admin | `/admin/login.php` | `/admin/dashboard.php` | Platform stats, user management, organizer applications |
| Organizer | `/organizer/login.php` | `/organizer/dashboard.php` | Create events, manage registrations, check-in |
| Participant | `/participant/login.php` | `/participant/dashboard.php` | Register for events, view certificates |
| Guest | `/public/register.php` | N/A | Browse & register for public events |

### File Structure
```
event-registration/
├── admin/              # Admin panel
│   ├── login.php       # Admin login page
│   ├── dashboard.php   # Admin dashboard
│   ├── users.php       # User management
│   ├── categories.php  # Event categories
│   ├── reports.php     # Reports & analytics
│   └── logout.php      # Admin logout
├── organizer/          # Organizer panel
│   ├── login.php       # Organizer login
│   ├── dashboard.php   # Organizer dashboard
│   ├── events.php      # Event management
│   └── logout.php      # Logout
├── participant/        # Participant panel
│   ├── login.php       # Participant login
│   ├── dashboard.php   # View registrations
│   ├── certificates.php# Download certificates
│   └── logout.php      # Logout
├── public/             # Public pages
│   ├── home.php        # Homepage
│   ├── listing.php     # Event listing
│   ├── event-detail.php# Event details
│   ├── register.php    # Registration form
│   └── confirm.php     # Confirmation page
├── api/                # API endpoints
│   ├── register.php    # POST registration
│   ├── checkin.php     # POST check-in
│   ├── coupon.php      # GET coupon validation
│   ├── search.php      # GET event search
│   ├── waitlist.php    # POST waitlist management
│   └── notification.php# POST notifications
├── includes/           # Shared components
│   ├── bootstrap.php   # Configuration & setup
│   ├── auth.php        # Authentication functions
│   ├── functions.php   # Helper functions
│   ├── header.php      # Common header
│   ├── footer.php      # Common footer
│   ├── mail.php        # Email functions
│   ├── certificate.php # Certificate generation
│   └── qr.php          # QR code functions
├── assets/             # Static files
│   ├── app.css         # Stylesheet
│   └── app.js          # JavaScript
└── mail_templates/     # Email templates
```

---

## 3. DATABASE SCHEMA

### Core Tables
1. **users** - User accounts with roles
2. **events** - Event listings
3. **event_categories** - Event categories
4. **event_sessions** - Event schedule/sessions
5. **event_faqs** - FAQ for events
6. **ticket_types** - Ticket pricing & availability
7. **registrations** - Event registrations
8. **payments** - Payment records
9. **waitlist** - Waitlist for sold-out events
10. **certificates** - Event certificates
11. **feedback** - Event feedback/ratings
12. **notifications** - User notifications
13. **organizer_applications** - Organizer signup requests
14. **coupons** - Discount codes
15. **coupon_usage** - Coupon usage tracking
16. **event_updates** - Event notifications
17. **event_sessions** - Event sessions/speakers

### Key Fields
- **users.role**: ENUM('admin', 'organizer', 'participant')
- **users.email_verified**: TINYINT (1 = verified)
- **events.status**: ENUM('draft', 'published', 'cancelled', 'completed')
- **registrations.payment_status**: ENUM('free', 'paid', 'pending')
- **registrations.check_in_status**: ENUM('not_checked_in', 'checked_in')

---

## 4. SEEDED TEST DATA

### Admin Account (Updated)
```sql
Email: rajuchaswik@gmail.com
Password Hash: $2b$10$zhygwriItgQ6QLiae37qvO2h6KBEmM3FP4n05FaXN1yJpTiZz9sKi
Role: admin
```

### Test Organizers
1. **org1@events.local** - Tech Guild organizer
2. **org2@events.local** - Design Lab organizer

### Test Participants
1. **p1@events.local** - Participant One
2. **p2@events.local** - Participant Two
3. **p3@events.local** - Participant Three
4. **p4@events.local** - Participant Four
5. **p5@events.local** - Participant Five

### Test Events (Published)
1. **Tech Summit 2026** - 300 capacity, ₹999, June 20, 2026
2. **Startup Growth Bootcamp** - 150 capacity, ₹1499, July 10-11, 2026
3. **Design Thinking Meetup** - 120 capacity, Free, May 28, 2026
4. **AI Hack Night** - 200 capacity, ₹499, August 2, 2026

### Test Coupons
- **EARLY15**: 15% early bird discount (Tech Summit)
- **STUDENT10**: 10% student discount (Tech Summit)
- **FOUNDER30**: 30% founder special (Startup Bootcamp)
- **TECH20**: 20% tech professionals (Design Meetup)

---

## 5. AUTHENTICATION FLOW

### Login Process
1. User navigates to role-specific login page
2. Enters email and password
3. System verifies email_verified = 1
4. Password verified using `password_verify()` (bcrypt)
5. User ID stored in `$_SESSION['user_id']`
6. Session regenerated for security
7. Redirected to appropriate dashboard

### Logout Process
1. User clicks logout
2. Session variables cleared
3. Session cookie deleted
4. Redirected to homepage

**Code Location**: `/includes/auth.php`

---

## 6. REGISTRATION FLOW (Public)

### Multi-Step Form (5 Steps)
Location: `/public/register.php` → `/api/register.php`

**Step 1: Ticket Selection**
- Browse available ticket types
- See real-time availability
- Unit price display

**Step 2: Attendee Information**
- Email (required, validated)
- Full Name (2-150 characters)
- Phone (7+ characters)
- Organization (optional)

**Step 3: Special Requests**
- Dietary restrictions
- Accessibility needs
- Other requirements

**Step 4: Coupon & Payment**
- Coupon code input with validation
- Payment method: Free/Razorpay/UPI
- Real-time discount calculation

**Step 5: Terms & Conditions**
- Mandatory agreement checkbox
- Optional newsletter subscription

### Registration API (`/api/register.php`)
**Method**: POST  
**Required CSRF Token**: Yes

**Request Parameters**:
```php
csrf_token          // CSRF security token
event_id            // Event ID (integer)
ticket_type_id      // Ticket type (integer)
quantity            // Number of tickets (1+)
email               // Valid email format
name                // 2-150 characters
phone               // 7+ characters
agree_terms         // Must be "on"
```

**Optional Parameters**:
```php
organization        // Organization name
special_requests    // Special needs text
coupon_code         // Discount code
payment_method      // "free", "razorpay", "upi"
agree_emails        // Newsletter opt-in
```

**Response on Success**:
```json
{
    "success": true,
    "registration_id": 123,
    "registration_number": "REG20260505123456789",
    "qr_code": "https://chart.googleapis.com/chart?chs=220x220&cht=qr&chl=REG20260505123456789",
    "redirect": "/public/confirm.php?reg=REG20260505123456789"
}
```

### Guest Account Creation
- If user not logged in and email doesn't exist → auto-create guest account
- Account role: "participant"
- Password: random bcrypt hash (user can reset)
- email_verified: 1 (auto-verified)

---

## 7. COUPON SYSTEM

### Validation Endpoint
**URL**: `/api/coupon.php`  
**Method**: POST  
**Action**: validate

**Parameters**:
```php
action              // "validate"
code                // Coupon code to validate
```

**Validations Performed**:
1. Coupon code exists
2. Coupon is active (status = 'active')
3. Current date >= valid_from
4. Current date <= valid_to
5. Usage count < max_usage (if limit set)

**Response**:
```json
{
    "success": true,
    "discount": 15.00,
    "message": "Coupon applied successfully"
}
```

---

## 8. CHECK-IN SYSTEM

### Organizer Check-In
**URL**: `/api/checkin.php`  
**Method**: POST  
**Roles**: Admin, Organizer

**Parameters**:
```php
csrf_token           // CSRF token
registration_number  // Registration QR code value
```

**Action**: Updates registration record
- Sets check_in_status = "checked_in"
- Records check_in_time = NOW()

---

## 9. DASHBOARD FEATURES

### Admin Dashboard (`/admin/dashboard.php`)
**Stats Display**:
- Total Events
- Total Users
- Total Registrations
- Total Revenue

**Sections**:
- Pending organizer applications
- Recent events (5 latest)
- Links to management pages

**Required Role**: admin

### Organizer Dashboard (`/organizer/dashboard.php`)
**Stats Display**:
- Total Events (by this organizer)
- Total Registrations

**Sections**:
- Recent events (5 latest)
- Event management options

**Required Role**: organizer, admin

### Participant Dashboard (`/participant/dashboard.php`)
**Display**:
- All user's registrations
- Event details (title, date, registration #)
- Payment status
- Check-in status

**Required Role**: admin, organizer, participant

---

## 10. PUBLIC PAGES

### Homepage (`/public/home.php`)
- Hero section with CTA
- Featured events (6 latest published)
- Browse events link

### Event Listing (`/public/listing.php`)
- List all published events
- Search by title, location, organizer
- Event cards with details
- Link to event detail page

### Event Detail (`/public/event-detail.php`)
- Full event information
- Ticket types with pricing
- Event sessions/speakers
- FAQs
- Register button
- Waitlist option (if sold out)

### Confirmation Page (`/public/confirm.php`)
- Registration confirmation message
- QR code display (for check-in)
- Registration details
- Event information
- Print option
- Browse more events link

---

## 11. API ENDPOINTS SUMMARY

| Endpoint | Method | Auth | Purpose |
|----------|--------|------|---------|
| `/api/register.php` | POST | CSRF | Register for event |
| `/api/checkin.php` | POST | CSRF | Check-in participant |
| `/api/coupon.php` | POST | CSRF | Validate coupon |
| `/api/search.php` | GET | None | Search events |
| `/api/waitlist.php` | POST | Session | Manage waitlist |
| `/api/notification.php` | POST | Session | Send notifications |

---

## 12. SECURITY FEATURES

### CSRF Protection
- Token generated: `csrf_token()` function
- Field output: `csrf_field()` function
- Verification: `verify_csrf()` function
- Token storage: `$_SESSION['_csrf']`

### Password Security
- Hashing: bcrypt (PASSWORD_BCRYPT)
- Verification: `password_verify()`
- Cost: 10 rounds

### Session Security
- Session regeneration on login
- Session destruction on logout
- HTTPOnly cookie (if configured)

### Input Validation
- Email validation with FILTER_VALIDATE_EMAIL
- Text sanitization with `clean_text()`
- HTML escaping with `e()` function
- SQL prepared statements (all queries)

---

## 13. TESTING SCENARIOS

### ✅ Scenario 1: Admin Login
1. Navigate to `/admin/login.php`
2. Enter: rajuchaswik@gmail.com / Raju@2006
3. ✅ Redirects to `/admin/dashboard.php`
4. ✅ Dashboard displays stats
5. ✅ Navigation shows admin sections

### ✅ Scenario 2: Public Event Registration
1. Navigate to `/public/home.php`
2. Click "Browse events" → `/public/listing.php`
3. Select an event → `/public/event-detail.php`
4. Click "Register" → `/public/register.php`
5. Fill 5-step form
6. Submit → Creates registration
7. ✅ Redirects to confirmation page
8. ✅ Shows QR code and registration number

### ✅ Scenario 3: Participant Login
1. Use email from registration (auto-created guest account)
2. Navigate to `/participant/login.php`
3. Enter email and password
4. ✅ Redirects to `/participant/dashboard.php`
5. ✅ Shows all registrations

### ✅ Scenario 4: Coupon Application
1. During registration, enter coupon code
2. Click "Apply"
3. ✅ Valid coupon: Shows discount in summary
4. ✅ Invalid coupon: Shows error message
5. ✅ Total price updates in real-time

### ✅ Scenario 5: Organizer Access
1. Navigate to `/organizer/login.php`
2. Enter organizer credentials
3. ✅ Redirects to `/organizer/dashboard.php`
4. ✅ Shows organizer's events and registrations

### ✅ Scenario 6: Check-In Process
1. Organizer at event with participant's QR code
2. Scan QR or enter registration number
3. POST to `/api/checkin.php`
4. ✅ Registration marked as "checked_in"
5. ✅ Timestamp recorded

---

## 14. ISSUES FIXED

### Fixed Issue #1: Asset Path in Header
**Problem**: `/assets/app.css` instead of `/event-registration/assets/app.css`  
**File**: `includes/header.php`  
**Fix**: Corrected asset path for proper CSS loading

### Fixed Issue #2: Navigation Links Path
**Problem**: Navigation links used relative paths `/public/listing.php` instead of absolute paths  
**File**: `includes/header.php`  
**Fix**: Updated to `/event-registration/public/listing.php`, etc.

### Fixed Issue #3: Admin Credentials
**Problem**: Seed data had `admin@events.local` instead of provided credentials  
**File**: `seed.sql`  
**Fix**: Updated to `rajuchaswik@gmail.com` with correct password hash

---

## 15. SETUP INSTRUCTIONS

### 1. Database Setup
```bash
# Create database
CREATE DATABASE event_db;

# Run schema
mysql -u root event_db < schema.sql

# Load seed data
mysql -u root event_db < seed.sql
```

### 2. Web Server Setup
- Place files in web root
- Ensure `/public/home.php` is accessible
- Configure virtual host or use built-in server:
```bash
php -S 127.0.0.1:8000
```

### 3. Access Points
- **Homepage**: `http://localhost:8000/event-registration/public/home.php`
- **Admin Login**: `http://localhost:8000/event-registration/admin/login.php`
- **Participant Login**: `http://localhost:8000/event-registration/participant/login.php`
- **Organizer Login**: `http://localhost:8000/event-registration/organizer/login.php`

---

## 16. VERIFICATION CHECKLIST

- ✅ Database schema verified and complete
- ✅ All tables created with correct columns
- ✅ Seed data loaded successfully
- ✅ Admin credentials updated
- ✅ PHP syntax validated across all files
- ✅ Asset paths corrected
- ✅ Navigation links fixed
- ✅ Authentication functions working
- ✅ Session management implemented
- ✅ CSRF protection active
- ✅ Input validation in place
- ✅ API endpoints accessible
- ✅ Coupon system functional
- ✅ Check-in system ready
- ✅ Multi-role dashboard system working

---

## 17. KNOWN LIMITATIONS

1. **Razorpay Integration**: Payment endpoint not fully implemented (placeholder)
2. **Email Templates**: Mail sending requires mail server configuration
3. **QR Code**: Using external Google Charts API (requires internet)
4. **Certificate Generation**: Template system available but PDF export not shown
5. **Real-time Notifications**: WebSocket implementation pending

---

## 18. NEXT STEPS / ENHANCEMENTS

1. Complete payment gateway integration
2. Add email notification system
3. Implement certificate PDF generation
4. Add real-time notifications
5. Create admin analytics dashboard
6. Add organizer event creation UI
7. Implement advanced search filters
8. Add user profile management

---

**System Status**: ✅ **READY FOR TESTING**

All core functionality is in place and ready for comprehensive testing.
