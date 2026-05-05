# Quick Reference Card

## ⚡ Quick Start (Copy & Paste)

### 1. Create Database
```bash
mysql -u root -p
```
Then paste:
```sql
CREATE DATABASE student_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### 2. Import Schema & Seed
```bash
cd c:\Users\BhaviChasvi\Downloads\php\student-management
mysql -u root student_db < schema.sql
mysql -u root student_db < seed.sql
```

### 3. Start Server
```bash
php -S localhost:8000
```

### 4. Open Browser
```
http://localhost:8000
```

## 🔐 Admin Credentials
| Field | Value |
|-------|-------|
| Email | rajuchaswik@gmail.com |
| Password | Raju@2006 |

## 📚 Documentation Files
| File | Purpose |
|------|---------|
| SETUP.md | Complete setup instructions |
| TESTING.md | Testing checklist |
| PASSWORD_REFERENCE.md | Password management |
| SUMMARY.md | Full overview |

## ✨ Features Ready
- ✅ Admin login
- ✅ User registration
- ✅ Student management
- ✅ Department listing
- ✅ Course listing
- ✅ Attendance tracking
- ✅ Marks management
- ✅ Fee tracking
- ✅ User management
- ✅ Dashboard with metrics
- ✅ Activity logging
- ✅ Search functionality

## 🗂️ Key Directories
```
student-management/
├── index.php              # Dashboard
├── login.php              # Login page
├── register.php           # Registration (NEW)
├── logout.php             # Logout
├── includes/              # Core functions
└── modules/               # Feature modules
```

## 🔍 Database Verification
```bash
mysql -u root student_db
```
```sql
-- Check tables created
SHOW TABLES;

-- Check admin user exists
SELECT * FROM users WHERE email = 'rajuchaswik@gmail.com';

-- Check students data
SELECT COUNT(*) FROM students;

-- Check departments
SELECT * FROM departments;
```

## 🐛 Common Issues & Fixes

| Issue | Solution |
|-------|----------|
| Connection refused | Start MySQL service |
| Table not found | Run schema.sql |
| Login fails | Run seed.sql, check credentials |
| Session error | Check PHP permissions |
| Blank page | Check PHP error logs |

## 📱 URLs at a Glance
- Login: http://localhost:8000/login.php
- Register: http://localhost:8000/register.php
- Dashboard: http://localhost:8000/index.php
- Students: http://localhost:8000/modules/students/index.php
- Logout: http://localhost:8000/logout.php

## 🎯 Next Steps
1. ✅ Setup database (see Quick Start above)
2. ✅ Start web server
3. ✅ Login with admin credentials
4. ✅ Add students via dashboard
5. ✅ Test all modules
6. ✅ Register new users

## 💡 Notes
- All new user registrations get "student" role
- Admins can manage all modules
- CSRF protection enabled on all forms
- Passwords use bcrypt hashing
- Database credentials configurable via environment variables

## 📞 Support
For detailed instructions, see:
- SETUP.md - Installation guide
- TESTING.md - Testing procedures
- PASSWORD_REFERENCE.md - Password management

---
**System Ready!** 🚀
