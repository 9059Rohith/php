# Testing Checklist for Student Management System

## Setup
- [ ] Install MySQL and create database `student_db`
- [ ] Import `schema.sql` to create tables
- [ ] Import `seed.sql` to populate initial data
- [ ] Configure database credentials if needed (in `includes/bootstrap.php`)
- [ ] Start web server: `php -S localhost:8000`
- [ ] Open browser to `http://localhost:8000`

## Authentication Tests

### Admin Login
- [ ] Navigate to login page
- [ ] Enter email: `rajuchaswik@gmail.com`
- [ ] Enter password: `Raju@2006`
- [ ] Click "Sign in"
- [ ] Should redirect to dashboard as Admin

### User Registration
- [ ] Click "Register here" link on login page
- [ ] Fill in: Name, Email, Password, Confirm Password
- [ ] Test validation:
  - [ ] Empty fields should show error
  - [ ] Mismatched passwords should show error
  - [ ] Short password (<6 chars) should show error
  - [ ] Duplicate email should show error
- [ ] Successfully register a test user
- [ ] Should redirect to login page with success message
- [ ] Login with newly registered user
- [ ] New user should have "student" role

### Logout
- [ ] Click "Logout" in top-right corner
- [ ] Should redirect to login page
- [ ] Session should be cleared

## Dashboard Tests
- [ ] View stat cards (Total Students, Active Students, Departments, Pending Fees)
- [ ] All metrics should display numbers
- [ ] View Recent Activity table
- [ ] Table should show recent actions

## Students Module Tests
- [ ] Click "Students" in sidebar
- [ ] View all students in table
- [ ] Test search functionality:
  - [ ] Search by name
  - [ ] Search by roll number
  - [ ] Search by email
  - [ ] Search by department
- [ ] Click "Add Student" button
- [ ] Fill in all required fields
- [ ] Save new student
- [ ] View student in list
- [ ] Click "View" to see student details
- [ ] Click "Edit" to modify student info
- [ ] Click "Delete" and confirm deletion

## Departments Module Tests
- [ ] Click "Departments" in sidebar
- [ ] View all departments with:
  - [ ] Department name
  - [ ] Department code
  - [ ] HOD name
  - [ ] Student count

## Courses Module Tests
- [ ] Click "Courses" in sidebar
- [ ] View all courses with:
  - [ ] Course name
  - [ ] Department
  - [ ] Duration in years

## Users Module Tests
- [ ] Click menu for Users (if accessible)
- [ ] View all registered users with:
  - [ ] Name
  - [ ] Email
  - [ ] Role (admin/staff/student)
  - [ ] Status (active/inactive)

## Attendance Module Tests
- [ ] Click "Attendance" in sidebar
- [ ] System should display attendance records
- [ ] Mark attendance for students (if edit functionality exists)

## Fees Module Tests
- [ ] Click "Fees" in sidebar
- [ ] View fee records
- [ ] Display fees with status (paid/unpaid/partial)

## Marks Module Tests
- [ ] Click "Marks" in sidebar
- [ ] View marks/grades (if data exists)

## Reports Module Tests
- [ ] Click "Reports" in sidebar
- [ ] Generate or view reports as needed

## Security Tests
- [ ] Unauthenticated users cannot access protected pages:
  - [ ] Direct access to `/index.php` redirects to `/login.php`
  - [ ] Direct access to `/modules/students/index.php` redirects to `/login.php`
- [ ] CSRF tokens are present in forms:
  - [ ] Check form source for `_csrf` field
- [ ] Session management works correctly:
  - [ ] Login creates session
  - [ ] Logout destroys session
  - [ ] Multiple concurrent sessions work

## Database Tests
- [ ] All tables are created correctly
- [ ] Seed data is populated
- [ ] Check database structure:
  ```sql
  USE student_db;
  SHOW TABLES;
  SELECT COUNT(*) FROM users;
  SELECT COUNT(*) FROM students;
  SELECT COUNT(*) FROM departments;
  ```

## Error Handling Tests
- [ ] Try invalid login credentials → should show error
- [ ] Try registering with existing email → should show error
- [ ] Try accessing with corrupted session → should redirect to login
- [ ] Database connection error → should show appropriate error

## Performance Tests
- [ ] Dashboard loads quickly
- [ ] Student list loads with many records
- [ ] Search functionality responds in reasonable time
- [ ] No timeout errors

## Browser Compatibility
- [ ] Chrome/Edge: Works correctly
- [ ] Firefox: Works correctly
- [ ] Safari: Works correctly (if on Mac)

## Notes
- Admin user: rajuchaswik@gmail.com / Raju@2006
- Seeded staff user: staff@student.local (same password hash)
- Seeded student user: student@student.local (same password hash)
- All new registrations default to "student" role
- Admins can change user roles via database queries if needed
