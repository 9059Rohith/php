# 🎫 Event Registration System

A comprehensive core PHP-based event registration platform with multi-role dashboards, AJAX registration forms, coupon management, and QR code ticket generation.

---

## ✨ Features

### Public Features
- ✅ **Event Browsing** - Browse and search events
- ✅ **Event Details** - Complete event information
- ✅ **Event Registration** - Multi-step AJAX registration
- ✅ **Ticket Types** - Multiple ticket options per event
- ✅ **Coupon System** - Apply discount codes
- ✅ **Waitlist Management** - Join event waitlist
- ✅ **QR Code Tickets** - Automated QR generation
- ✅ **Email Confirmations** - Automated confirmation emails
- ✅ **Check-in System** - QR code scanning at venue

### Participant Features
- ✅ **Dashboard** - View registered events
- ✅ **My Registrations** - Track all registrations
- ✅ **Certificates** - Download event certificates
- ✅ **Check-in History** - View attendance records
- ✅ **Event Feedback** - Submit event reviews

### Organizer Features
- ✅ **Event Management** - Create and manage events
- ✅ **Event Dashboard** - Real-time analytics
- ✅ **Registration Tracking** - View all registrations
- ✅ **Attendee List** - Export attendee reports
- ✅ **Check-in Management** - Mark attendance
- ✅ **Reports** - Event statistics and analytics

### Admin Features
- ✅ **Platform Dashboard** - System overview
- ✅ **Event Management** - Approve/manage all events
- ✅ **Category Management** - Manage event categories
- ✅ **User Management** - Manage all users
- ✅ **Coupon Management** - Create and track coupons
- ✅ **Reports** - Platform-wide analytics

### Technical Features
- 🔐 Role-based authentication (Admin/Organizer/Participant)
- 🔐 Secure password hashing (bcrypt)
- 🔐 CSRF token protection
- 🔐 Session management
- 📱 QR code generation for tickets
- 📧 Email notifications
- 💳 Payment method selection (Free/Razorpay/UPI)

### Technical Stack
- **Backend**: Core PHP (PDO)
- **Database**: MySQL 5.7+
- **Frontend**: HTML/CSS + Vanilla JavaScript
- **API**: JSON-based AJAX endpoints
- **QR Code**: QR library integration
- **Payment**: Razorpay (framework ready)

---

## 🚀 Installation

### Prerequisites
- PHP 7.4+ with GD extension (for QR codes)
- MySQL 5.7+
- Web Server (Apache/Nginx)
- Git (optional)

### Step 1: Setup Database
```bash
# Create database
mysql -u root -p
> CREATE DATABASE events_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
> EXIT;

# Import schema
mysql -u root -p events_db < schema.sql

# Seed initial data
mysql -u root -p events_db < seed.sql
```

### Step 2: Configure Database Connection
Edit `includes/bootstrap.php`:
```php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'events_db');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### Step 3: Create Directories
```bash
mkdir -p uploads/events
mkdir -p uploads/qrcodes
mkdir -p uploads/certificates
chmod 755 uploads/*
```

### Step 4: Start Web Server
```bash
cd event-registration
php -S localhost:8000
```

### Step 5: Access Application
- **Public**: `http://localhost:8000/public/listing.php`
- **Admin**: `http://localhost:8000/admin/login.php`
- **Organizer**: `http://localhost:8000/organizer/login.php`
- **Participant**: `http://localhost:8000/participant/login.php`

---

## 📋 Default Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | rajuchaswik@gmail.com | Raju@2006 |
| Organizer | organizer@events.local | password123 |
| Participant | participant@example.com | password123 |

### Register New Participant Account
1. Navigate to: `http://localhost:8000/public/register.php`
2. Fill in your details
3. Login with your credentials

---

## 📚 Module Documentation

### Public Portal

#### Event Listing (`public/listing.php`)
- Browse all active events
- Search by event name or category
- Filter by date range
- Sort by popularity

#### Event Detail (`public/event-detail.php`)
- Full event information
- Event description and agenda
- Ticket types and pricing
- Organizer details
- Participant count
- Register button

#### Registration (`public/register.php`)
**5-Step AJAX Registration Form:**

1. **Participant Information**
   - First name, Last name
   - Email, Phone
   - Gender, Date of Birth

2. **Address Details**
   - Address line 1 & 2
   - City, State, Postal code

3. **Ticket Selection**
   - Ticket type dropdown
   - Quantity selector
   - Price calculation

4. **Payment Information**
   - Payment method (Free/Razorpay/UPI)
   - Coupon code input
   - Discount calculation

5. **Confirmation**
   - Review all details
   - Accept terms & conditions
   - Complete registration

**AJAX Implementation**:
```javascript
// api/register.php
fetch('../api/register.php', {
  method: 'POST',
  body: new FormData(form)
}).then(r => r.json());
```

#### Confirmation Page (`public/confirm.php`)
- Registration confirmation message
- QR code ticket display
- Ticket download link
- Email notification confirmation

### Participant Portal

#### Dashboard (`participant/dashboard.php`)
- Registered events summary
- Upcoming events
- Past events
- Quick actions

#### My Registrations (`participant/certificates.php`)
- List of attended events
- Attendance status
- Certificate links
- Download certificates

#### Check-in Status
- Event attendance confirmation
- Check-in timestamp
- QR code verification status

### Organizer Portal

#### Dashboard (`organizer/dashboard.php`)
- Event statistics
- Total registrations
- Ticket revenue
- Attendee breakdown
- Quick actions

#### Events (`organizer/events.php`)
- List my events
- Create new event
- Edit event details
- View registrations
- Export attendee list
- Check-in management

#### Registration Management
- View all registrations for event
- Export to CSV
- Mark attendance
- Generate reports

### Admin Panel

#### Dashboard (`admin/dashboard.php`)
- Platform overview
- Total events
- Total registrations
- Platform revenue
- User management shortcuts

#### Event Management (`admin/events.php`)
- Approve pending events
- Manage all events
- View event details
- Generate platform reports

#### Category Management (`admin/categories.php`)
- Create event categories
- Edit categories
- Delete categories
- View category usage

#### User Management (`admin/users.php`)
- List all users
- Manage user roles
- Activate/Deactivate accounts
- View user details

#### Coupon Management (`admin/reports.php`)
- Create discount codes
- View coupon usage
- Track coupon redemption
- Set expiration dates

#### Reports (`admin/reports.php`)
- Event statistics
- Registration analytics
- Revenue reports
- Attendee demographics

---

## 🔄 AJAX Implementation

### Event Registration API (`api/register.php`)

**Submit Registration**
```php
POST /api/register.php
{
  "first_name": "John",
  "last_name": "Doe",
  "email": "john@example.com",
  "phone": "9876543210",
  "event_id": 1,
  "ticket_id": 2,
  "quantity": 1,
  "payment_method": "razorpay",
  "coupon_code": "SAVE20",
  "_csrf": "token"
}

Response:
{
  "success": true,
  "message": "Registration successful",
  "registration_id": 45,
  "qr_code_url": "/uploads/qrcodes/REG45.png"
}
```

### Coupon API (`api/coupon.php`)

**Validate Coupon**
```php
POST /api/coupon.php
{
  "code": "SAVE20",
  "_csrf": "token"
}

Response:
{
  "success": true,
  "coupon": {
    "code": "SAVE20",
    "discount_type": "percentage",
    "discount_value": 20,
    "message": "20% discount applied"
  }
}
```

### Waitlist API (`api/waitlist.php`)

**Join Waitlist**
```php
POST /api/waitlist.php
{
  "action": "join",
  "event_id": 1,
  "_csrf": "token"
}

Response:
{
  "success": true,
  "state": "joined",
  "position": 5
}
```

### Check-in API (`api/checkin.php`)

**Mark Attendance**
```php
POST /api/checkin.php
{
  "qr_code": "REG45",
  "_csrf": "token"
}

Response:
{
  "success": true,
  "message": "Checked in successfully"
}
```

---

## 📂 Directory Structure

```
event-registration/
├── index.php                          # Home redirect
├── schema.sql                         # Database schema
├── seed.sql                           # Sample data
├── public/
│   ├── home.php                       # Public home
│   ├── listing.php                    # Event listing
│   ├── event-detail.php               # Event details
│   ├── register.php                   # Registration form (5-step)
│   ├── confirm.php                    # Confirmation page
│   └── index.html                     # Landing page
├── admin/
│   ├── login.php                      # Admin login
│   ├── logout.php                     # Logout
│   ├── dashboard.php                  # Admin dashboard
│   ├── events.php                     # Event management
│   ├── categories.php                 # Category management
│   ├── users.php                      # User management
│   ├── reports.php                    # Reports & analytics
│   └── assets/                        # Admin styles & scripts
├── organizer/
│   ├── login.php                      # Organizer login
│   ├── logout.php                     # Logout
│   ├── dashboard.php                  # Organizer dashboard
│   └── events.php                     # Manage events
├── participant/
│   ├── login.php                      # Participant login
│   ├── logout.php                     # Logout
│   ├── dashboard.php                  # Participant dashboard
│   └── certificates.php               # Certificates
├── api/
│   ├── register.php                   # Registration AJAX
│   ├── coupon.php                     # Coupon validation
│   ├── waitlist.php                   # Waitlist management
│   ├── checkin.php                    # Check-in AJAX
│   ├── notification.php               # Notifications
│   └── search.php                     # Event search
├── includes/
│   ├── bootstrap.php                  # Config & auth
│   ├── auth.php                       # Authentication
│   ├── functions.php                  # Helper functions
│   ├── qr.php                         # QR code generation
│   ├── certificate.php                # Certificate generation
│   ├── mail.php                       # Email functions
│   ├── header.php                     # Navigation template
│   └── footer.php                     # Footer template
├── mail_templates/
│   ├── registration_confirmation.php  # Registration email
│   ├── ticket.php                     # Ticket email
│   └── certificate.php                # Certificate email
├── assets/
│   ├── app.css                        # Styles
│   ├── app.js                         # AJAX scripts
│   └── images/                        # Static images
├── uploads/
│   ├── events/                        # Event images
│   ├── qrcodes/                       # QR code tickets
│   └── certificates/                  # Generated certificates
└── README.md                          # This file
```

---

## 🧪 Testing Guide

### Test Registration Flow

**1. Browse Events**
```
Navigate to: http://localhost:8000/public/listing.php
```

**2. View Event Details**
```
Click on any event to see full details
```

**3. Register (AJAX Form)**
```
Click "Register Now"
Fill 5-step form:
  - Participant details
  - Address
  - Ticket selection
  - Payment method
  - Confirmation
```

**4. Coupon Code (AJAX)**
```
Enter code: SAVE20 (20% off)
Or: EARLYBIRD (Early bird discount)
Discount auto-calculates via AJAX
```

**5. Complete Registration**
```
Confirmation page shows QR code ticket
Download ticket or check email
```

**6. Check-in**
```
Organizer scans QR code at event
System marks attendance
```

---

## 💳 Payment Gateway (Razorpay)

### Current Status
- ✅ Payment method selection in registration form
- ✅ Database schema ready for payment tracking
- 🔄 Integration in progress

### Integration Roadmap
1. Get Razorpay API credentials
2. Add keys to `includes/bootstrap.php`
3. Create payment processing endpoint
4. Update registration form with Razorpay button
5. Implement payment verification webhook

---

## 📨 Email Notifications

Automated emails sent for:
- ✅ Registration confirmation with QR code
- ✅ Ticket delivery
- ✅ Event reminders
- ✅ Certificate delivery
- ✅ Event cancellation notices

---

## 🔐 Security Features

- ✅ Bcrypt password hashing (cost=10)
- ✅ CSRF token protection on all forms
- ✅ Session management with role checks
- ✅ SQL injection prevention (prepared statements)
- ✅ Role-based access control (3 roles)
- ✅ Input sanitization
- ✅ Email verification
- ✅ Session destruction on logout

---

## 📝 Database Tables

- `users` - User accounts (admin/organizer/participant)
- `events` - Event information
- `event_sessions` - Event agenda/sessions
- `ticket_types` - Available ticket types
- `registrations` - Event registrations
- `registrants` - Participant details per registration
- `waitlist` - Event waitlist entries
- `coupons` - Discount codes
- `coupon_usage` - Coupon redemption tracking
- `checkins` - Attendance records
- `categories` - Event categories

---

## 🐛 Troubleshooting

| Issue | Solution |
|-------|----------|
| Registration form not submitting | Check CSRF token, verify JavaScript enabled |
| QR code not generating | Ensure GD extension is enabled in PHP |
| Emails not sending | Check mail configuration in `includes/mail.php` |
| Login fails | Verify user exists, check password hash |
| 403 Forbidden on organizer panel | Login with organizer account |
| Database connection error | Check credentials in `includes/bootstrap.php` |

---

## 🚀 Deployment

### cPanel Hosting
1. Upload to `public_html/events`
2. Import database via phpMyAdmin
3. Update database credentials
4. Set permissions (644 files, 755 directories)
5. Ensure upload directories are writable

### Nginx Configuration
```nginx
server {
    listen 80;
    server_name events.example.com;
    root /path/to/event-registration;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi_params;
    }
}
```

---

## 📞 Support

For detailed information, refer to:
- `SETUP_GUIDE.md` - Complete setup instructions
- `REGISTRATION_GUIDE.md` - Registration workflow
- `TESTING_REPORT.md` - Test cases and verification
- `TESTING_SUMMARY.md` - Quick reference

---

**Last Updated**: May 5, 2026  
**Version**: 1.0.0  
**Status**: ✅ Production Ready (Razorpay integration pending)
