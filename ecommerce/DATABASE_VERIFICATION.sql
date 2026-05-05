-- SQL Verification Script for E-Commerce Platform
-- Run these queries to verify the database setup and test accounts

-- ============================================
-- 1. Verify Database Exists
-- ============================================
SELECT DATABASE();
-- Expected: ecommerce_db

-- ============================================
-- 2. Verify All Tables Exist
-- ============================================
SHOW TABLES;
-- Expected: 16 tables including users, products, orders, coupons, etc.

-- ============================================
-- 3. Verify Admin Account
-- ============================================
SELECT id, name, email, role, email_verified FROM users WHERE role = 'admin';
-- Expected: 1 admin with email rajuchaswik@gmail.com

-- ============================================
-- 4. Check Admin User Details
-- ============================================
SELECT id, name, email, password_hash, role, phone, email_verified, created_at 
FROM users WHERE email = 'rajuchaswik@gmail.com';
-- Expected: 1 row with admin role and email_verified = 1

-- ============================================
-- 5. List All Users
-- ============================================
SELECT id, name, email, role, phone, email_verified 
FROM users ORDER BY id;
-- Expected: Admin + 2 sample customers

-- ============================================
-- 6. Verify User Email Uniqueness Constraint
-- ============================================
SHOW CREATE TABLE users\G
-- Look for: UNIQUE KEY `email` (`email`)

-- ============================================
-- 7. Test: Try adding duplicate email (should fail)
-- ============================================
-- UNCOMMENT TO TEST - this should fail:
-- INSERT INTO users (name, email, password_hash, phone, role, email_verified) 
-- VALUES ('Test', 'rajuchaswik@gmail.com', 'hash', '9999999999', 'customer', 1);
-- Expected Error: Duplicate entry 'rajuchaswik@gmail.com' for key 'email'

-- ============================================
-- 8. Count Users by Role
-- ============================================
SELECT role, COUNT(*) as count FROM users GROUP BY role;
-- Expected: admin=1, customer=2 (or more if you created test accounts)

-- ============================================
-- 9. Verify Sample Data - Products
-- ============================================
SELECT id, name, slug, price, sale_price, status, featured 
FROM products LIMIT 5;
-- Expected: 20 sample products

-- ============================================
-- 10. Verify Categories
-- ============================================
SELECT id, name, slug, parent_id, status 
FROM categories ORDER BY id;
-- Expected: 5 categories

-- ============================================
-- 11. Verify Coupons
-- ============================================
SELECT id, code, type, value, min_order, status 
FROM coupons;
-- Expected: WELCOME10 and FLAT200 coupons

-- ============================================
-- 12. Verify Addresses Table
-- ============================================
DESC addresses;
-- Expected: Columns for user_id, type (billing/shipping), etc.

-- ============================================
-- 13. Verify Email Verification
-- ============================================
SELECT COUNT(*) as total_users, 
       SUM(CASE WHEN email_verified = 1 THEN 1 ELSE 0 END) as verified_users
FROM users;
-- Expected: All users should be verified (email_verified = 1)

-- ============================================
-- 14. Check Session Tables (if using DB sessions)
-- ============================================
-- SHOW TABLES LIKE 'sessions';
-- May not exist - that's OK if using PHP file-based sessions

-- ============================================
-- 15. Verify Password Hashes (should be bcrypt format)
-- ============================================
SELECT email, password_hash, 
       CASE 
           WHEN password_hash LIKE '$2%' THEN 'Bcrypt Hash'
           ELSE 'Other Format'
       END as hash_type
FROM users;
-- Expected: All passwords should be bcrypt ($2a$ or $2y$ or $2b$)

-- ============================================
-- 16. Verify Foreign Key Constraints
-- ============================================
SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME 
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
WHERE CONSTRAINT_NAME LIKE 'fk%' AND TABLE_SCHEMA = 'ecommerce_db'
ORDER BY TABLE_NAME;
-- Expected: Multiple FK constraints for data integrity

-- ============================================
-- 17. Sample Data Verification - Orders
-- ============================================
SELECT o.id, o.order_number, u.name, o.total_amount, o.status 
FROM orders o 
JOIN users u ON o.user_id = u.id;
-- Expected: Sample orders in database

-- ============================================
-- 18. Admin User Can Access Admin Dashboard
-- ============================================
SELECT id, email, role FROM users WHERE email = 'rajuchaswik@gmail.com' AND role = 'admin';
-- Expected: 1 row - confirms admin exists

-- ============================================
-- 19. Check Product Inventory
-- ============================================
SELECT COUNT(*) as total_products,
       SUM(stock_qty) as total_stock,
       MIN(price) as min_price,
       MAX(price) as max_price
FROM products;
-- Expected: 20 products with stock

-- ============================================
-- 20. Test Data for Manual Verification
-- ============================================
SELECT 
    'Admin Account' as test_item,
    'rajuchaswik@gmail.com' as email,
    'Raju@2006' as password,
    'Login to /customer/pages/login.php' as instructions
UNION ALL
SELECT 
    'Test Registration',
    'newuser@example.com',
    'YourPassword123',
    'Go to /customer/pages/register.php'
UNION ALL
SELECT
    'Admin Panel Access',
    'After admin login',
    'N/A',
    'Access /admin/index.php'
UNION ALL
SELECT
    'Customer Login',
    'asha@shop.local',
    'Use registration form to set',
    'Go to /customer/pages/login.php';

-- ============================================
-- Cleanup Scripts (use with caution)
-- ============================================

-- To reset admin password (generate new bcrypt hash):
-- UPDATE users SET password_hash = 'NEW_BCRYPT_HASH_HERE' 
-- WHERE email = 'rajuchaswik@gmail.com';

-- To delete test user:
-- DELETE FROM users WHERE email = 'test@example.com';

-- To verify no duplicate emails:
-- SELECT email, COUNT(*) FROM users GROUP BY email HAVING COUNT(*) > 1;

-- To check user account creation date:
-- SELECT name, email, created_at FROM users ORDER BY created_at DESC;

-- ============================================
-- Notes
-- ============================================
-- 1. All sample users have email_verified = 1 by default
-- 2. Password hashes use bcrypt with cost factor 10
-- 3. Email field has UNIQUE constraint - no duplicates allowed
-- 4. All timestamps are in UTC (set by DEFAULT CURRENT_TIMESTAMP)
-- 5. Role field is ENUM with values: 'admin', 'customer'
-- 6. For security, never query or display password_hash in production UI
