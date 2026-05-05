INSERT INTO users (name, email, password_hash, phone, role, email_verified) VALUES
('Admin User', 'rajuchaswik@gmail.com', '$2b$10$1gz8IP8Cw26TKePsIJr6Vu4DGbi6F1uWHvTa3/p6Irmkkd3hkbx3W', '9999999999', 'admin', 1),
('Asha Buyer', 'asha@shop.local', '$2y$10$wH2cM8UjQhQbQ8e1gF9bQeY8i2t8QW4pY1sJ7z0G7n4Q0r9YfFJ7V6', '8888888888', 'customer', 1),
('Ravi Buyer', 'ravi@shop.local', '$2y$10$wH2cM8UjQhQbQ8e1gF9bQeY8i2t8QW4pY1sJ7z0G7n4Q0r9YfFJ7V6', '7777777777', 'customer', 1);

INSERT INTO categories (name, slug, parent_id, status) VALUES
('Electronics', 'electronics', NULL, 'active'),
('Fashion', 'fashion', NULL, 'active'),
('Home & Kitchen', 'home-kitchen', NULL, 'active'),
('Mobiles', 'mobiles', 1, 'active'),
('Men Clothing', 'men-clothing', 2, 'active');

INSERT INTO products (name, slug, description, category_id, price, sale_price, stock_qty, sku, images_json, rating_avg, rating_count, status, featured) VALUES
('Smartphone Alpha', 'smartphone-alpha', 'A fast everyday smartphone.', 4, 15999, 14999, 24, 'SKU-ALPHA', '["/uploads/products/p1.svg"]', 4.5, 82, 'active', 1),
('Laptop Pro 14', 'laptop-pro-14', 'Lightweight productivity laptop.', 1, 59999, 54999, 12, 'SKU-LAP14', '["/uploads/products/p2.svg"]', 4.7, 36, 'active', 1),
('Wireless Earbuds', 'wireless-earbuds', 'Compact earbuds with charging case.', 1, 2499, 1999, 45, 'SKU-EARBUDS', '["/uploads/products/p3.svg"]', 4.3, 110, 'active', 1),
('Men Shirt Classic', 'men-shirt-classic', 'Cotton regular fit shirt.', 5, 1299, 999, 60, 'SKU-SHIRT', '["/uploads/products/p4.svg"]', 4.1, 18, 'active', 0),
('Mixer Grinder', 'mixer-grinder', '3-jar mixer grinder for kitchen.', 3, 3499, 2999, 9, 'SKU-MIXER', '["/uploads/products/p5.svg"]', 4.4, 27, 'active', 0),
('Office Chair', 'office-chair', 'Ergonomic chair for work-from-home.', 3, 8999, 7999, 8, 'SKU-CHAIR', '["/uploads/products/p6.svg"]', 4.6, 14, 'active', 0),
('Running Shoes', 'running-shoes', 'Breathable running sneakers.', 2, 3999, 3299, 20, 'SKU-SHOES', '["/uploads/products/p7.svg"]', 4.2, 19, 'active', 0),
('Bluetooth Speaker', 'bluetooth-speaker', 'Portable speaker with bass boost.', 1, 2999, 2499, 30, 'SKU-SPEAKER', '["/uploads/products/p8.svg"]', 4.5, 51, 'active', 1),
('Coffee Maker', 'coffee-maker', 'One-touch coffee machine.', 3, 6999, 6499, 6, 'SKU-COFFEE', '["/uploads/products/p9.svg"]', 4.3, 9, 'active', 0),
('Tablet S', 'tablet-s', 'Tablet for entertainment and notes.', 4, 21999, 19999, 15, 'SKU-TABLET', '["/uploads/products/p10.svg"]', 4.4, 40, 'active', 1),
('Headphones Max', 'headphones-max', 'Over-ear immersive headphones.', 1, 4999, 4499, 18, 'SKU-HEADPHONES', '["/uploads/products/p11.svg"]', 4.7, 71, 'active', 1),
('Denim Jacket', 'denim-jacket', 'Classic blue denim jacket.', 2, 3499, 2999, 16, 'SKU-JACKET', '["/uploads/products/p12.svg"]', 4.0, 11, 'active', 0),
('Water Bottle', 'water-bottle', 'Insulated steel water bottle.', 3, 999, 799, 70, 'SKU-BOTTLE', '["/uploads/products/p13.svg"]', 4.8, 60, 'active', 0),
('Smart Watch', 'smart-watch', 'Health tracking smart watch.', 4, 8999, 8499, 22, 'SKU-WATCH', '["/uploads/products/p14.svg"]', 4.6, 34, 'active', 1),
('Trimmer', 'trimmer', 'Beard trimmer with precision settings.', 1, 1999, 1799, 27, 'SKU-TRIMMER', '["/uploads/products/p15.svg"]', 4.2, 26, 'active', 0),
('Sofa Cover', 'sofa-cover', 'Stretchable sofa cover set.', 3, 1499, 1299, 40, 'SKU-SOFA', '["/uploads/products/p16.svg"]', 4.1, 7, 'active', 0),
('Power Bank', 'power-bank', '20,000mAh fast charging power bank.', 1, 2499, 2199, 33, 'SKU-POWER', '["/uploads/products/p17.svg"]', 4.5, 45, 'active', 1),
('Sneakers Lite', 'sneakers-lite', 'Casual sneakers for daily wear.', 2, 2799, 2399, 28, 'SKU-LITE', '["/uploads/products/p18.svg"]', 4.2, 17, 'active', 0),
('Air Fryer', 'air-fryer', 'Healthy low-oil air fryer.', 3, 9999, 8999, 11, 'SKU-AIR', '["/uploads/products/p19.svg"]', 4.7, 23, 'active', 1),
('Backpack', 'backpack', 'Laptop backpack with multiple compartments.', 2, 1899, 1599, 52, 'SKU-BACKPACK', '["/uploads/products/p20.svg"]', 4.3, 13, 'active', 0);

INSERT INTO coupons (code, type, value, min_order, max_discount, usage_limit, used_count, status) VALUES
('WELCOME10', 'percent', 10, 999, 500, 100, 0, 'active'),
('FLAT200', 'fixed', 200, 1499, NULL, 50, 0, 'active');

INSERT INTO orders (user_id, order_number, status, total_amount, discount_amount, shipping_amount, tax_amount, payment_method, payment_status, payment_id, created_at) VALUES
(2, 'ORD20260501001', 'delivered', 18998, 500, 49, 3432.82, 'razorpay', 'paid', 'PAY123', NOW()),
(3, 'ORD20260502001', 'shipped', 2999, 0, 49, 539.82, 'cod', 'pending', NULL, NOW());
