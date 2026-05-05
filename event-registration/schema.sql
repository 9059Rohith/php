CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NULL,
    organization VARCHAR(180) NULL,
    role ENUM('admin','organizer','participant') NOT NULL DEFAULT 'participant',
    profile_pic VARCHAR(255) NULL,
    email_verified TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE event_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    icon VARCHAR(50) NULL,
    color VARCHAR(20) NULL
);

CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organizer_id INT NOT NULL,
    title VARCHAR(180) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    description TEXT NOT NULL,
    category_id INT NOT NULL,
    venue VARCHAR(180) NOT NULL,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    event_date DATE NOT NULL,
    end_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    max_capacity INT NOT NULL,
    registration_deadline DATE NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    is_free TINYINT(1) NOT NULL DEFAULT 0,
    banner_image VARCHAR(255) NULL,
    status ENUM('draft','published','cancelled','completed') NOT NULL DEFAULT 'draft',
    featured TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE event_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    title VARCHAR(180) NOT NULL,
    speaker_name VARCHAR(150) NOT NULL,
    speaker_bio TEXT NULL,
    speaker_photo VARCHAR(255) NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    room VARCHAR(100) NULL,
    description TEXT NULL
);

CREATE TABLE event_faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    question VARCHAR(255) NOT NULL,
    answer TEXT NOT NULL
);

CREATE TABLE ticket_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity_available INT NOT NULL,
    quantity_sold INT NOT NULL DEFAULT 0,
    perks TEXT NULL,
    sale_start DATE NULL,
    sale_end DATE NULL
);

CREATE TABLE registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NULL,
    registration_number VARCHAR(60) NOT NULL UNIQUE,
    ticket_type_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_status ENUM('free','paid','pending') NOT NULL DEFAULT 'pending',
    payment_id VARCHAR(100) NULL,
    qr_code_path VARCHAR(255) NULL,
    check_in_status ENUM('not_checked_in','checked_in') NOT NULL DEFAULT 'not_checked_in',
    check_in_time DATETIME NULL,
    cancellation_reason VARCHAR(255) NULL,
    special_requests TEXT NULL,
    coupon_code VARCHAR(50) NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registration_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    method ENUM('free','razorpay','upi') NOT NULL,
    transaction_id VARCHAR(100) NULL,
    status VARCHAR(30) NOT NULL,
    paid_at DATETIME NULL
);

CREATE TABLE waitlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    position INT NOT NULL,
    joined_at DATETIME NOT NULL,
    notified TINYINT(1) NOT NULL DEFAULT 0
);

CREATE TABLE event_updates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    title VARCHAR(180) NOT NULL,
    message TEXT NOT NULL,
    sent_at DATETIME NOT NULL
);

CREATE TABLE certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registration_id INT NOT NULL,
    template_id INT NULL,
    issued_at DATETIME NULL,
    download_count INT NOT NULL DEFAULT 0
);

CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL,
    comments TEXT NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(180) NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(50) NOT NULL,
    read_status TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE organizer_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    organization VARCHAR(180) NOT NULL,
    reason TEXT NOT NULL,
    status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    code VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) NULL,
    discount_percent DECIMAL(5,2) NOT NULL,
    discount_amount DECIMAL(10,2) NULL,
    max_usage INT NULL,
    valid_from DATETIME NOT NULL,
    valid_to DATETIME NOT NULL,
    status ENUM('active','inactive','expired') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE coupon_usage (
    id INT AUTO_INCREMENT PRIMARY KEY,
    coupon_id INT NOT NULL,
    registration_id INT NOT NULL,
    used_at DATETIME NOT NULL
);
