# 🎓 Student Management System

A complete core PHP-based student management system with AJAX-powered CRUD operations, role-based access control, and comprehensive dashboard analytics.

---

## ✨ Features

### Core Functionality
- ✅ **User Authentication** - Login/Register with email and password
- ✅ **Role-Based Access** - Admin, Staff, and Student roles
- ✅ **Student Management** - AJAX CRUD for student records with search
- ✅ **Department Management** - Department listing and organization
- ✅ **Course Management** - Course tracking by department
- ✅ **Attendance Tracking** - Mark and view attendance records
- ✅ **Marks Management** - Grade tracking and reporting
- ✅ **Fee Management** - Fee tracking and payment status
- ✅ **Reports Module** - Comprehensive analytics and statistics
- ✅ **Activity Logging** - Track all system actions

### Security Features
- 🔐 CSRF protection on all forms
- 🔐 Bcrypt password hashing (cost=10)
- 🔐 Session management with ID regeneration
- 🔐 SQL injection prevention with prepared statements
- 🔐 Input sanitization and validation
- 🔐 Role-based access control

### Technical Stack
- **Backend**: Core PHP (PDO)
- **Database**: MySQL 5.7+
- **Frontend**: HTML/CSS + Vanilla JavaScript
- **API**: JSON-based AJAX endpoints
- **Authentication**: Session-based

---

## 🚀 Installation

### Prerequisites
- PHP 7.4+
- MySQL 5.7+
- Web Server (Apache/Nginx)
- Git (optional)

### Step 1: Setup Database
```bash
# Create database
mysql -u root -p
> CREATE DATABASE student_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
> EXIT;

# Import schema
mysql -u root -p student_db < schema.sql

# Seed initial data
mysql -u root -p student_db < seed.sql
```

### Step 2: Configure Database Connection
Edit `includes/bootstrap.php` and set your database credentials:
```php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'student_db');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### Step 3: Start Web Server
```bash
cd student-management
php -S localhost:8000
```

### Step 4: Access Application
Open browser: `http://localhost:8000`

---

## 📋 Default Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | rajuchaswik@gmail.com | Raju@2006 |

### Create New Account
- Click **"Register here"** on login page
- Fill in your details
- Login with your credentials

---

## 📚 Module Documentation

### Dashboard (`index.php`)
- Real-time metrics (students, departments, etc.)
- Recent activity log
- Quick statistics

### Students Module (`modules/students/`)
**AJAX CRUD Operations:**
- **Add Student** - `add.php` with form submission
- **Edit Student** - `edit.php` with pre-filled data
- **View Student** - `view.php` with full details
- **Delete Student** - `delete.php` with confirmation
- **Search Student** - `ajax_handlers.php` AJAX endpoint

**Key File**: `ajax_handlers.php`
```javascript
// Frontend (app.js)
fetch(form.action, {
  method: form.method || 'POST',
  body: new FormData(form)
})
```

### Departments (`modules/departments/`)
- List all departments
- Department-wise student count
- Course assignment

### Courses (`modules/courses/`)
- Course listing
- Department association
- Duration tracking

### Attendance (`modules/attendance/`)
- Mark attendance
- View attendance records
- Attendance reports

### Marks (`modules/marks/`)
- Grade management
- Subject-wise marks
- Performance tracking

### Fees (`modules/fees/`)
- Fee tracking
- Payment status
- Outstanding fees report

### Reports (`modules/reports/`)
- Student statistics
- Attendance analysis
- Fee collection reports
- Performance metrics

### Users (`modules/users/`)
- User management
- Role assignment
- Status control

---

## 🔄 AJAX Implementation

### Student Search API
**File**: `modules/students/ajax_handlers.php`

```php
// Request
POST /modules/students/ajax_handlers.php
{
  "action": "search",
  "term": "john"
}

// Response
{
  "success": true,
  "data": [
    {
      "id": 1,
      "roll_no": "2023001",
      "name": "John Doe",
      "email": "john@example.com"
    }
  ]
}
```

### Student Save API
```php
// Request
POST /modules/students/ajax_handlers.php
{
  "action": "save",
  "id": 0,  // 0 for new, ID for update
  "roll_no": "2024001",
  "name": "Jane Smith",
  "email": "jane@example.com",
  "phone": "9876543210",
  "gender": "Female",
  "department_id": 1,
  "course_id": 1,
  "semester": "1",
  "_csrf": "token"
}

// Response
{
  "success": true,
  "message": "Student saved successfully.",
  "id": 15
}
```

---

## 📂 Directory Structure

```
student-management/
├── index.php                      # Dashboard
├── login.php                      # Login page
├── register.php                   # Registration page
├── logout.php                     # Logout handler
├── schema.sql                     # Database schema
├── seed.sql                       # Sample data
├── assets/
│   ├── css/                       # Stylesheets
│   └── js/app.js                  # AJAX form handling
├── includes/
│   ├── bootstrap.php              # DB connection
│   ├── auth.php                   # Authentication logic
│   ├── functions.php              # Helper functions
│   └── footer.php                 # Footer template
├── modules/
│   ├── students/
│   │   ├── index.php              # Student list
│   │   ├── add.php                # Add student form
│   │   ├── edit.php               # Edit student form
│   │   ├── view.php               # Student details
│   │   ├── delete.php             # Delete handler
│   │   └── ajax_handlers.php      # AJAX endpoints
│   ├── departments/
│   ├── courses/
│   ├── attendance/
│   ├── marks/
│   ├── fees/
│   ├── reports/
│   └── users/
└── README.md                      # This file
```

---

## 🧪 Testing Guide

### Test Student CRUD via AJAX

**1. Add Student**
```javascript
const form = document.querySelector('[data-ajax-form]');
form.dispatchEvent(new Event('submit'));
```

**2. Search Student**
```javascript
fetch('/modules/students/ajax_handlers.php', {
  method: 'POST',
  body: JSON.stringify({
    action: 'search',
    term: 'john',
    _csrf: document.querySelector('[name="_csrf"]').value
  })
}).then(r => r.json()).then(console.log);
```

**3. View All Students**
- Navigate to: `http://localhost:8000/modules/students/`
- Click student name to view details

---

## 🔐 Security Checklist

- ✅ CSRF tokens on all forms
- ✅ Session ID regeneration on login
- ✅ Password hashing with bcrypt
- ✅ Input validation and sanitization
- ✅ Prepared statements for all queries
- ✅ Role-based access control
- ✅ Activity logging for audit trail

---

## 🐛 Troubleshooting

| Issue | Solution |
|-------|----------|
| Database connection error | Check `includes/bootstrap.php` credentials |
| Login fails | Verify user exists in database, check password hash |
| AJAX not working | Ensure `app.js` is loaded, check browser console |
| CSRF token error | Clear browser cache and cookies |
| Permission denied | Check user role in database |

---

## 📝 API Response Format

All AJAX endpoints return JSON:

```json
{
  "success": true/false,
  "message": "Human-readable message",
  "data": null/object/array,
  "id": null/number
}
```

---

## 🚀 Deployment

### cPanel Hosting
1. Upload files to `public_html` directory
2. Import database using cPanel phpMyAdmin
3. Update database credentials in `includes/bootstrap.php`
4. Ensure file permissions (644 for files, 755 for directories)

### Nginx Configuration
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/student-management;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

---

## 📞 Support

For issues or questions, refer to:
- `SETUP.md` - Detailed setup guide
- `TESTING.md` - Comprehensive testing checklist
- `PASSWORD_REFERENCE.md` - Password management

---

**Last Updated**: May 5, 2026  
**Version**: 1.0.0  
**Status**: ✅ Production Ready
