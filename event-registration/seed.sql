INSERT INTO users (name, email, password_hash, phone, organization, role, email_verified) VALUES
('Admin User', 'rajuchaswik@gmail.com', '$2b$10$zhygwriItgQ6QLiae37qvO2h6KBEmM3FP4n05FaXN1yJpTiZz9sKi', '9999999999', 'Platform', 'admin', 1),
('Organizer One', 'org1@events.local', '$2y$10$wH2cM8UjQhQbQ8e1gF9bQeY8i2t8QW4pY1sJ7z0G7n4Q0r9YfFJ7V6', '8888888888', 'Tech Guild', 'organizer', 1),
('Organizer Two', 'org2@events.local', '$2y$10$wH2cM8UjQhQbQ8e1gF9bQeY8i2t8QW4pY1sJ7z0G7n4Q0r9YfFJ7V6', '7777777777', 'Design Lab', 'organizer', 1),
('Participant One', 'p1@events.local', '$2y$10$wH2cM8UjQhQbQ8e1gF9bQeY8i2t8QW4pY1sJ7z0G7n4Q0r9YfFJ7V6', '6666666666', NULL, 'participant', 1),
('Participant Two', 'p2@events.local', '$2y$10$wH2cM8UjQhQbQ8e1gF9bQeY8i2t8QW4pY1sJ7z0G7n4Q0r9YfFJ7V6', '5555555555', NULL, 'participant', 1),
('Participant Three', 'p3@events.local', '$2y$10$wH2cM8UjQhQbQ8e1gF9bQeY8i2t8QW4pY1sJ7z0G7n4Q0r9YfFJ7V6', '4444444444', NULL, 'participant', 1),
('Participant Four', 'p4@events.local', '$2y$10$wH2cM8UjQhQbQ8e1gF9bQeY8i2t8QW4pY1sJ7z0G7n4Q0r9YfFJ7V6', '3333333333', NULL, 'participant', 1),
('Participant Five', 'p5@events.local', '$2y$10$wH2cM8UjQhQbQ8e1gF9bQeY8i2t8QW4pY1sJ7z0G7n4Q0r9YfFJ7V6', '2222222222', NULL, 'participant', 1);

INSERT INTO event_categories (name, icon, color) VALUES
('Technology', 'laptop', '#4cc9f0'),
('Business', 'briefcase', '#f4a261'),
('Community', 'users', '#2a9d8f');

INSERT INTO events (organizer_id, title, slug, description, category_id, venue, address, city, state, event_date, end_date, start_time, end_time, max_capacity, registration_deadline, price, is_free, banner_image, status, featured) VALUES
(2, 'Tech Summit 2026', 'tech-summit-2026', 'A full-day summit on cloud and AI.', 1, 'City Convention Center', 'Main Hall Road', 'Bengaluru', 'Karnataka', '2026-06-20', '2026-06-20', '09:00:00', '17:30:00', 300, '2026-06-15', 999.00, 0, NULL, 'published', 1),
(2, 'Startup Growth Bootcamp', 'startup-growth-bootcamp', 'Hands-on bootcamp for founders.', 2, 'Innovation Hub', '22 Startup Street', 'Mumbai', 'Maharashtra', '2026-07-10', '2026-07-11', '10:00:00', '16:00:00', 150, '2026-07-05', 1499.00, 0, NULL, 'published', 1),
(3, 'Design Thinking Meetup', 'design-thinking-meetup', 'Community meetup for UX and product folks.', 3, 'Creative Space', 'Art Lane', 'Pune', 'Maharashtra', '2026-05-28', '2026-05-28', '18:00:00', '20:30:00', 120, '2026-05-25', 0.00, 1, NULL, 'published', 0),
(2, 'AI Hack Night', 'ai-hack-night', 'Build and demo AI prototypes in one night.', 1, 'Tech Park Auditorium', 'Block B', 'Hyderabad', 'Telangana', '2026-08-02', '2026-08-02', '19:00:00', '23:30:00', 200, '2026-07-28', 499.00, 0, NULL, 'published', 1);

INSERT INTO event_sessions (event_id, title, speaker_name, speaker_bio, speaker_photo, start_time, end_time, room, description) VALUES
(1, 'Opening Keynote', 'Dr. Amit Shah', 'Cloud architect and keynote speaker.', NULL, '09:30:00', '10:15:00', 'Main Hall', 'Kickoff keynote.'),
(1, 'AI at Scale', 'Meera Iyer', 'ML lead at a SaaS company.', NULL, '11:00:00', '11:45:00', 'Hall A', 'AI deployment patterns.'),
(2, 'Pitch Deck Essentials', 'Rahul Deshmukh', 'Startup mentor.', NULL, '10:30:00', '11:30:00', 'Room 1', 'Deck review session.'),
(3, 'Design Ops Panel', 'Nina Rao', 'Product design lead.', NULL, '18:30:00', '19:30:00', 'Studio 2', 'Panel and Q&A.');

INSERT INTO ticket_types (event_id, name, price, quantity_available, quantity_sold, perks, sale_start, sale_end) VALUES
(1, 'General', 999.00, 250, 120, 'Access to all sessions', '2026-05-01', '2026-06-15'),
(1, 'VIP', 1999.00, 50, 20, 'VIP seating and lunch', '2026-05-01', '2026-06-15'),
(2, 'General', 1499.00, 120, 60, 'Bootcamp kit', '2026-06-01', '2026-07-05'),
(2, 'VIP', 2499.00, 30, 10, 'Mentorship roundtable', '2026-06-01', '2026-07-05'),
(3, 'Free Pass', 0.00, 120, 55, 'Community meetup access', '2026-05-01', '2026-05-25'),
(3, 'Supporter', 499.00, 25, 8, 'Reserved seating', '2026-05-01', '2026-05-25'),
(4, 'General', 499.00, 150, 75, 'Hack night entry', '2026-07-01', '2026-07-28'),
(4, 'Team Pass', 1499.00, 20, 5, 'Team of 3 entry', '2026-07-01', '2026-07-28');

INSERT INTO registrations (event_id, user_id, registration_number, ticket_type_id, quantity, total_amount, payment_status, payment_id, qr_code_path, check_in_status, check_in_time, registered_at) VALUES
(1, 4, 'REG20260501001', 1, 1, 999.00, 'paid', 'PAY001', 'https://chart.googleapis.com/chart?chs=220x220&cht=qr&chl=REG20260501001', 'checked_in', '2026-06-20 09:15:00', NOW()),
(1, 5, 'REG20260501002', 2, 1, 1999.00, 'paid', 'PAY002', 'https://chart.googleapis.com/chart?chs=220x220&cht=qr&chl=REG20260501002', 'not_checked_in', NULL, NOW()),
(2, 6, 'REG20260501003', 3, 1, 1499.00, 'paid', 'PAY003', 'https://chart.googleapis.com/chart?chs=220x220&cht=qr&chl=REG20260501003', 'checked_in', '2026-07-10 09:45:00', NOW()),
(3, 7, 'REG20260501004', 5, 1, 0.00, 'free', NULL, 'https://chart.googleapis.com/chart?chs=220x220&cht=qr&chl=REG20260501004', 'checked_in', '2026-05-28 17:45:00', NOW()),
(4, 8, 'REG20260501005', 7, 1, 499.00, 'paid', 'PAY005', 'https://chart.googleapis.com/chart?chs=220x220&cht=qr&chl=REG20260501005', 'not_checked_in', NULL, NOW());

INSERT INTO notifications (user_id, title, message, type, read_status) VALUES
(4, 'Registration Confirmed', 'Your registration for Tech Summit 2026 is confirmed.', 'registration', 0),
(2, 'New Organizer Application', 'A new organizer application is pending review.', 'admin', 0);

INSERT INTO coupons (event_id, code, description, discount_percent, discount_amount, max_usage, valid_from, valid_to, status) VALUES
(1, 'EARLY15', 'Early bird discount', 15.00, NULL, 50, '2026-05-01', '2026-06-10', 'active'),
(1, 'STUDENT10', 'Student discount', 10.00, NULL, 100, '2026-05-01', '2026-06-15', 'active'),
(2, 'FOUNDER30', 'Founder special', 30.00, NULL, 20, '2026-06-01', '2026-07-05', 'active'),
(3, 'TECH20', 'Tech professionals', 20.00, NULL, 30, '2026-05-01', '2026-05-25', 'active');
