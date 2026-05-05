INSERT INTO departments (name, code, hod) VALUES
('Computer Science', 'CSE', 'Dr. Asha Patel'),
('Business Administration', 'BBA', 'Dr. Rakesh Mehta'),
('Electronics', 'ECE', 'Dr. Neha Verma');

INSERT INTO courses (name, department_id, duration_years) VALUES
('B.Tech Computer Science', 1, 4),
('MCA', 1, 2),
('BBA', 2, 3),
('MBA', 2, 2),
('B.Tech Electronics', 3, 4),
('Diploma Electronics', 3, 3);

INSERT INTO users (name, email, password_hash, role) VALUES
('Admin User', 'rajuchaswik@gmail.com', '$2b$10$7/BQ5crKO9xCL56pwDKY6OeUDcOkQO.oYQV6FE4g9ti5Tcln70lRu', 'admin'),
('Staff User', 'staff@student.local', '$2y$10$wH2cM8UjQhQbQ8e1gF9bQeY8i2t8QW4pY1sJ7z0G7n4Q0r9YfFJ7V6', 'staff'),
('Student User', 'student@student.local', '$2y$10$wH2cM8UjQhQbQ8e1gF9bQeY8i2t8QW4pY1sJ7z0G7n4Q0r9YfFJ7V6', 'student');

INSERT INTO students (roll_no, name, email, phone, dob, gender, address, photo, department_id, course_id, semester, admission_date, status) VALUES
('CSE-001', 'Arjun Kumar', 'arjun01@example.com', '9000000001', '2002-01-12', 'male', '12 Lake Road', NULL, 1, 1, 'Semester 1', '2023-07-01', 'active'),
('CSE-002', 'Priya Sharma', 'priya02@example.com', '9000000002', '2002-02-18', 'female', '21 Hill Street', NULL, 1, 2, 'Semester 2', '2023-07-01', 'active'),
('CSE-003', 'Rahul Singh', 'rahul03@example.com', '9000000003', '2001-12-11', 'male', '44 Green Ave', NULL, 1, 1, 'Semester 3', '2022-07-01', 'active'),
('CSE-004', 'Ananya Roy', 'ananya04@example.com', '9000000004', '2002-05-22', 'female', '8 Park Lane', NULL, 1, 2, 'Semester 4', '2022-07-01', 'active'),
('CSE-005', 'Siddharth Jain', 'siddharth05@example.com', '9000000005', '2001-09-08', 'male', '78 River View', NULL, 1, 1, 'Semester 5', '2021-07-01', 'inactive'),
('CSE-006', 'Kavya Iyer', 'kavya06@example.com', '9000000006', '2002-03-30', 'female', '6 Temple Street', NULL, 1, 2, 'Semester 6', '2021-07-01', 'active'),
('BBA-001', 'Mohit Verma', 'mohit07@example.com', '9000000007', '2001-04-14', 'male', '90 Market Road', NULL, 2, 3, 'Semester 1', '2023-07-01', 'active'),
('BBA-002', 'Neha Kapoor', 'neha08@example.com', '9000000008', '2002-08-19', 'female', '19 Lake View', NULL, 2, 4, 'Semester 2', '2023-07-01', 'active'),
('BBA-003', 'Aman Yadav', 'aman09@example.com', '9000000009', '2001-06-27', 'male', '54 City Center', NULL, 2, 3, 'Semester 3', '2022-07-01', 'active'),
('BBA-004', 'Riya Das', 'riya10@example.com', '9000000010', '2002-10-03', 'female', '32 Canal Road', NULL, 2, 4, 'Semester 4', '2022-07-01', 'active'),
('BBA-005', 'Vikram Bose', 'vikram11@example.com', '9000000011', '2001-11-21', 'male', '13 North End', NULL, 2, 3, 'Semester 5', '2021-07-01', 'active'),
('BBA-006', 'Pooja Nair', 'pooja12@example.com', '9000000012', '2002-07-17', 'female', '17 South Street', NULL, 2, 4, 'Semester 6', '2021-07-01', 'inactive'),
('ECE-001', 'Aditya Rao', 'aditya13@example.com', '9000000013', '2001-02-09', 'male', '66 Tech Park', NULL, 3, 5, 'Semester 1', '2023-07-01', 'active'),
('ECE-002', 'Sneha Menon', 'sneha14@example.com', '9000000014', '2002-09-29', 'female', '99 Circuit Lane', NULL, 3, 6, 'Semester 2', '2023-07-01', 'active'),
('ECE-003', 'Nitin Gill', 'nitin15@example.com', '9000000015', '2001-05-16', 'male', '71 Signal Road', NULL, 3, 5, 'Semester 3', '2022-07-01', 'active'),
('ECE-004', 'Meera Joshi', 'meera16@example.com', '9000000016', '2002-12-04', 'female', '24 Ampere Street', NULL, 3, 6, 'Semester 4', '2022-07-01', 'active'),
('ECE-005', 'Harish Khan', 'harish17@example.com', '9000000017', '2001-01-25', 'male', '51 Voltage Ave', NULL, 3, 5, 'Semester 5', '2021-07-01', 'active'),
('ECE-006', 'Tanya Sethi', 'tanya18@example.com', '9000000018', '2002-06-11', 'female', '7 Diode Park', NULL, 3, 6, 'Semester 6', '2021-07-01', 'active'),
('CSE-007', 'Karan Mehta', 'karan19@example.com', '9000000019', '2001-08-15', 'male', '38 Binary Lane', NULL, 1, 1, 'Semester 7', '2020-07-01', 'active'),
('BBA-007', 'Isha Malhotra', 'isha20@example.com', '9000000020', '2002-04-02', 'female', '12 Commerce Road', NULL, 2, 3, 'Semester 7', '2020-07-01', 'inactive');

INSERT INTO marks (student_id, subject, internal_marks, external_marks, semester, year) VALUES
(1, 'Data Structures', 18, 68, 'Semester 1', 2024),
(1, 'DBMS', 17, 64, 'Semester 1', 2024),
(2, 'Data Structures', 20, 70, 'Semester 2', 2024),
(3, 'Operating Systems', 15, 60, 'Semester 3', 2024),
(4, 'Computer Networks', 16, 61, 'Semester 4', 2024);

INSERT INTO attendance (student_id, date, status, subject) VALUES
(1, '2026-05-01', 'present', 'Data Structures'),
(2, '2026-05-01', 'late', 'Data Structures'),
(3, '2026-05-01', 'absent', 'Operating Systems'),
(1, '2026-05-02', 'present', 'DBMS'),
(2, '2026-05-02', 'present', 'DBMS');

INSERT INTO fees (student_id, amount, due_date, paid_date, status, transaction_id) VALUES
(1, 25000.00, '2026-06-10', '2026-05-15', 'paid', 'TXN1001'),
(2, 25000.00, '2026-06-10', NULL, 'partial', NULL),
(3, 22000.00, '2026-06-10', NULL, 'unpaid', NULL);

INSERT INTO activity_log (user_id, action, entity, entity_id) VALUES
(1, 'added', 'student', 1),
(1, 'edited', 'student', 2),
(2, 'marked attendance', 'attendance', 1);
