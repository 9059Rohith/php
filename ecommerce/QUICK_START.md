# Quick Start - Authentication & Testing

## Admin Credentials
```
Email: rajuchaswik@gmail.com
Password: Raju@2006
```

---

## Step-by-Step Testing

### 1. Database Setup
```bash
# In MySQL:
CREATE DATABASE ecommerce_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ecommerce_db;

# Then run schema.sql and seed.sql
```

### 2. Test Admin Login
1. Navigate to: `/customer/pages/login.php`
2. Enter:
   - Email: `rajuchaswik@gmail.com`
   - Password: `Raju@2006`
3. Click Login
4. Should redirect to `/customer/pages/account.php`
5. Can access `/admin/index.php` (admin only)

### 3. Test Customer Registration
1. Navigate to: `/customer/pages/register.php`
2. Fill form:
   - Name: `Test User`
   - Email: `test@example.com`
   - Phone: `9876543210`
   - Password: `TestPass123`
3. Click "Create Account"
4. Should redirect to login page
5. Login with newly created account

### 4. Test Registration Validations
Try these to verify error handling:

**Test duplicate email:**
- Register with: `rajuchaswik@gmail.com`
- Should show: "Email already registered."

**Test weak password:**
- Password with less than 6 characters
- Should show: "Password must be at least 6 characters."

**Test invalid email:**
- Email: `invalid-email`
- Should show: "Valid email is required."

**Test missing fields:**
- Leave any field blank
- Should show: "[Field] is required."

### 5. Test Login Validations
**Test wrong password:**
- Email: `rajuchaswik@gmail.com`
- Password: `WrongPassword`
- Should show: "Invalid email or password."

**Test non-existent email:**
- Email: `nonexistent@example.com`
- Password: `anything`
- Should show: "Invalid email or password."

### 6. Test Logout
1. Login as admin
2. Click "Logout" link (if available in UI)
3. Session should clear
4. Cannot access `/admin/` without login

### 7. Test Admin Access Control
1. Login as regular customer
2. Try accessing: `/admin/index.php`
3. Should show: **403 Forbidden**
4. Login as admin: Should have access

---

## Security Features Verified ✓

- ✓ Passwords hashed with bcrypt (cost=10)
- ✓ CSRF tokens on all forms
- ✓ Session ID regeneration on login
- ✓ Email uniqueness enforced
- ✓ Role-based access control (admin/customer)
- ✓ Session destruction on logout
- ✓ Input sanitization with `clean_text()`
- ✓ SQL injection prevention with prepared statements
- ✓ Email verification check before login

---

## Sample Test Accounts

### Admin
- Email: `rajuchaswik@gmail.com`
- Password: `Raju@2006`
- Role: Admin

### Test Customers (Pre-seeded)
- Email: `asha@shop.local`
- Email: `ravi@shop.local`
- (Use Register page to create your own customers)

---

## Common Test Scenarios

### Scenario 1: New User Journey
1. Register new account → Login → Browse products → Add to cart → Checkout

### Scenario 2: Admin Tasks
1. Admin login → Access admin panel → View products/orders/users

### Scenario 3: Duplicate Email Prevention
1. Try registering with same email twice
2. Second attempt should fail gracefully

### Scenario 4: Session Security
1. Login → Open another tab → Try accessing without login
2. Should redirect to home or login page

### Scenario 5: CSRF Protection
1. Try submitting form without CSRF token (via curl/postman)
2. Should get 419 error or be rejected

---

## Files Modified/Created

### Updated:
- `seed.sql` - Admin email updated
- `admin/includes/bootstrap.php` - Database name fixed
- `customer/pages/login.php` - Enhanced with error display
- `customer/pages/register.php` - Added validation & error handling
- `customer/includes/header.php` - Display success messages

### Created:
- `SETUP_GUIDE.md` - Complete setup documentation
- `QUICK_START.md` - This file

---

## Browser Testing Checklist

### Chrome / Firefox / Safari
- [ ] Registration form validates
- [ ] Login works
- [ ] Session persists across pages
- [ ] Logout clears session
- [ ] Admin pages require login
- [ ] Error messages display correctly
- [ ] Success messages display correctly
- [ ] CSRF token present in all forms

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| "Database not found" | Check database name is `ecommerce_db` in bootstrap files |
| "Invalid login" always | Verify email exists and is verified in database |
| Can't access admin panel | Verify user role is 'admin' in database |
| CSRF errors | Ensure session_start() is called before form |
| Password issues | Use bcrypt compatible password (PHP 5.5+) |
| Email not unique | Database email column has UNIQUE constraint |

---

**Everything is ready to test!** 🚀
