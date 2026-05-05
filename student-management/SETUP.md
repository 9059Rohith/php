# Student Management System - Setup Guide

## Overview
The Student Management System is a PHP-based application for managing students, departments, courses, marks, attendance, and fees.

## Requirements
- PHP 7.4+ (with PDO MySQL extension)
- MySQL 5.7+ or MariaDB 10.1+
- Web Server (Apache, Nginx, or built-in PHP server)

## Database Setup

### Step 1: Create Database
Connect to MySQL and run:
```sql
CREATE DATABASE student_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Step 2: Import Schema
Run the schema.sql file to create tables:
```bash
mysql -u root -p student_db < schema.sql
```

### Step 3: Seed Initial Data
Run the seed.sql file to populate initial data:
```bash
mysql -u root -p student_db < seed.sql
```

## Configuration

### Database Configuration
The application uses environment variables for database configuration. You can set them in your system or create a `.env` file (if using dotenv).

Default configuration (in `includes/bootstrap.php`):
- **Driver**: mysql
- **Host**: 127.0.0.1
- **Port**: 3306
- **Database**: student_db
- **Username**: root
- **Password**: (empty)

To override, set environment variables:
```bash
export STUDENT_DB_HOST=localhost
export STUDENT_DB_USER=root
export STUDENT_DB_PASSWORD=your_password
export STUDENT_DB_NAME=student_db
```

## Running the Application

### Using PHP Built-in Server
```bash
cd c:\Users\BhaviChasvi\Downloads\php\student-management
php -S localhost:8000
```

Then open: http://localhost:8000

### Using Apache/Nginx
Configure your web server to serve from the student-management directory.

## Default Admin Credentials

After importing the seed data, use these credentials to login:

**Email**: rajuchaswik@gmail.com  
**Password**: Raju@2006  
**Role**: Admin

## User Registration

Any user can register a new account by:
1. Click "Register" on the login page
2. Fill in name, email, and password
3. New users are assigned the "student" role by default

## Available Features

### Dashboard
- View student metrics
- Recent activity log

### Students Module
- Add new students
- View student details
- Edit student information
- Delete students
- Search students

### Departments Module
- View all departments

### Courses Module
- View all courses by department

### Users Module
- View all registered users

### Other Modules
- Marks management
- Attendance tracking
- Fees management
- Reports

## File Structure

```
student-management/
├── index.php                    # Dashboard
├── login.php                    # Login page
├── logout.php                   # Logout
├── register.php                 # User registration
├── schema.sql                   # Database schema
├── seed.sql                     # Initial data
├── includes/
│   ├── auth.php                 # Authentication functions
│   ├── bootstrap.php            # Application initialization
│   ├── footer.php               # Footer template
│   ├── functions.php            # Helper functions
│   └── header.php               # Header template
├── modules/
│   ├── students/                # Student management
│   ├── departments/             # Department listing
│   ├── courses/                 # Course management
│   ├── marks/                   # Marks management
│   ├── attendance/              # Attendance tracking
│   ├── fees/                    # Fee management
│   ├── reports/                 # Reports
│   └── users/                   # User management
├── assets/
│   ├── css/app.css             # Main stylesheet
│   └── js/app.js               # JavaScript functionality
└── ../shared/core.php           # Shared core functions
```

## Troubleshooting

### "Could not find database"
- Ensure MySQL is running
- Check database name and credentials in bootstrap.php
- Run schema.sql to create tables

### "Login fails"
- Ensure seed.sql has been imported
- Check that password_hash is correct (use: rajuchaswik@gmail.com / Raju@2006)

### "Session errors"
- Ensure PHP session directory is writable
- Check PHP session settings in php.ini

## Password Reset

To reset admin password or change roles, use the MySQL command line:

```sql
-- Update password (example hash for "newpassword")
UPDATE users SET password_hash = '$2y$10$...' WHERE email = 'rajuchaswik@gmail.com';

-- Make a user admin
UPDATE users SET role = 'admin' WHERE email = 'user@example.com';

-- Activate/Deactivate user
UPDATE users SET status = 'active' WHERE id = 1;
```

## Security Notes

- Always use HTTPS in production
- Change default admin credentials
- Use environment variables for sensitive configuration
- Implement rate limiting for login attempts
- Regularly backup the database
- Use prepared statements (already implemented)
