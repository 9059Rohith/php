# E-Commerce Platform - Setup & Testing Guide

## 1. Database Setup

### Create Database
```sql
CREATE DATABASE ecommerce_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ecommerce_db;
```

### Run Schema & Seed
Execute the SQL files in this order:
1. **schema.sql** - Creates all tables
2. **seed.sql** - Populates with sample data

### Database Configuration
Update environment variables or modify bootstrap files:
- **Database Host:** 127.0.0.1 (or your server)
- **Database Name:** ecommerce_db
- **Database User:** root (or your user)
- **Database Password:** (empty by default, update as needed)

These are configured in:
- `includes/bootstrap.php` (customer side)
- `admin/includes/bootstrap.php` (admin side)

---

## 2. Admin Account

### Login Credentials
```
Email: rajuchaswik@gmail.com
Password: Raju@2006
```

### Account Details
- Role: Admin
- Email Verified: Yes
- Phone: 9999999999
- Database: `users` table, id = 1

---

## 3. Customer Registration

### Feature
Users can register through the customer portal.

### Registration Form
- **URL:** /customer/pages/register.php
- **Required Fields:**
  - Name (string)
  - Email (unique, must not exist)
  - Phone (string)
  - Password (will be securely hashed)

### How It Works
1. User submits registration form with POST
2. CSRF token is validated
3. Password is hashed using PHP's `password_hash()` (bcrypt)
4. User is created with `role = 'customer'` and `email_verified = 1`
5. User is redirected to login page

### Test Registration
```
Name: John Doe
Email: john@example.com
Phone: 9876543210
Password: TestPass123
```

---

## 4. Customer Login

### Feature
Users login with email and password.

### Login Form
- **URL:** /customer/pages/login.php
- **Required Fields:**
  - Email
  - Password

### How It Works
1. User submits login form with POST
2. CSRF token is validated
3. Email is looked up in users table
4. Password is verified against stored hash
5. Session is created with user_id
6. Session ID is regenerated for security
7. User is redirected to /customer/pages/account.php on success
8. Error message shown on login failure

### Test Login (Customer)
```
Email: asha@shop.local
Password: (same as seed.sql hash - use register to create new user)
```

---

## 5. Admin Login & Access Control

### Admin Login
- Same as customer login at /customer/pages/login.php
- Uses admin credentials above
- Redirects to /admin/index.php after successful login

### Access Control
The `ecommerce_require_admin()` function in `includes/auth.php` protects admin pages:
- Checks if user is logged in
- Checks if user role = 'admin'
- Returns 403 Forbidden if not authorized

### Admin Modules
Located in `/admin/modules/`:
- categories/
- coupons/
- orders/
- products/
- reports/
- users/

---

## 6. Session & Security

### Session Management
- Session starts automatically in `shared/core.php`
- Session ID is regenerated on login (prevents session fixation)
- Logout clears all session data

### CSRF Protection
- Token generated in `csrf_token()`
- Stored in `$_SESSION['_csrf']`
- Required on all POST requests
- Generated using `csrf_field()` in forms
- Verified with `verify_csrf()`

### Password Security
- Passwords hashed with PHP's `PASSWORD_DEFAULT` (bcrypt)
- Cost factor: 10 (default)
- Used by `password_hash()` and `password_verify()`

---

## 7. Sample Users for Testing

### Admin
- **Email:** rajuchaswik@gmail.com
- **Password:** Raju@2006
- **Role:** admin

### Customer 1
- **Email:** asha@shop.local
- **Password:** (pre-hashed in seed, use register to create new)
- **Role:** customer
- **Phone:** 8888888888

### Customer 2
- **Email:** ravi@shop.local
- **Password:** (pre-hashed in seed, use register to create new)
- **Role:** customer
- **Phone:** 7777777777

---

## 8. File Structure

### Authentication Files
```
includes/
  auth.php              # ecommerce_login(), ecommerce_logout()
  bootstrap.php         # Configuration, ecommerce_current_user()

customer/pages/
  login.php            # Login form
  register.php         # Registration form
  account.php          # Requires login

admin/
  includes/bootstrap.php  # Admin configuration
  index.php            # Admin dashboard (requires admin role)
  modules/            # Protected admin pages
```

### Shared Files
```
shared/
  core.php             # PDO connection, session, helpers, CSRF
```

---

## 9. Testing Checklist

- [ ] Database created and seeded
- [ ] Admin can login with rajuchaswik@gmail.com / Raju@2006
- [ ] Admin can access /admin/index.php
- [ ] New customer can register
- [ ] Registered customer can login
- [ ] Non-admin user cannot access /admin/index.php
- [ ] Logout works and clears session
- [ ] CSRF protection works on forms

---

## 10. Common Issues & Fixes

### Issue: "Database not found"
- Check database name in bootstrap files (should be `ecommerce_db`)
- Run schema.sql first
- Verify MySQL connection settings

### Issue: "Invalid login" always shown
- Verify seed.sql was executed
- Check that email_verified = 1 in users table
- Try registering a new user and logging in

### Issue: "Forbidden" on admin pages
- Verify user has role = 'admin'
- Check session is preserved across pages
- Ensure session.cookie_httponly is not blocking session cookies

### Issue: CSRF token errors
- Ensure session starts before rendering forms
- Check that POST data includes _csrf field
- Verify csrf_token() function is called in form

---

## 11. API Endpoints

Available API endpoints in `/api/`:
- `cart.php` - Cart management
- `coupon.php` - Coupon validation
- `review.php` - Product reviews
- `search.php` - Product search
- `wishlist.php` - Wishlist management

---

## 12. Additional Resources

### Tables Overview
- **users** - User accounts (admin/customer)
- **products** - Product catalog
- **categories** - Product categories
- **carts** - Shopping carts
- **wishlist** - User wishlists
- **orders** - Customer orders
- **coupons** - Discount codes
- **product_reviews** - Product ratings/reviews
- **addresses** - Shipping/billing addresses

### Key Functions
- `ecommerce_login()` - Authenticate user
- `ecommerce_logout()` - End session
- `ecommerce_current_user()` - Get logged-in user
- `ecommerce_require_login()` - Redirect if not logged in
- `ecommerce_require_admin()` - Redirect if not admin
- `password_hash()` - Hash password
- `password_verify()` - Verify password

---

**Setup completed successfully!** Your e-commerce platform is ready for testing.
