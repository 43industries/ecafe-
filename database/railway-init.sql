-- School e-Café Database Schema (Railway)
-- MySQL 8+ — omit CREATE DATABASE / USE (Railway provisions the database)

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS coupon_redemptions;
DROP TABLE IF EXISTS coupons;
DROP TABLE IF EXISTS favorites;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS cart;
DROP TABLE IF EXISTS inventory;
DROP TABLE IF EXISTS menu_items;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS announcements;
DROP TABLE IF EXISTS sso_tokens;
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS staff;
DROP TABLE IF EXISTS admins;

SET FOREIGN_KEY_CHECKS = 1;

-- Administrators
CREATE TABLE admins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    last_login_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Café staff
CREATE TABLE staff (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    last_login_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Students
CREATE TABLE students (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NULL,
    grade VARCHAR(20) NULL,
    loyalty_points INT UNSIGNED NOT NULL DEFAULT 0,
    avatar VARCHAR(255) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    external_sso_id VARCHAR(100) NULL,
    last_login_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_students_sso (external_sso_id)
) ENGINE=InnoDB;

-- SSO tokens (future school portal integration)
CREATE TABLE sso_tokens (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    external_id VARCHAR(100) NOT NULL,
    provider VARCHAR(50) NOT NULL DEFAULT 'school_portal',
    access_token TEXT NULL,
    refresh_token TEXT NULL,
    student_id INT UNSIGNED NULL,
    expires_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE SET NULL,
    INDEX idx_sso_external (external_id, provider)
) ENGINE=InnoDB;

-- Menu categories
CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL,
    image VARCHAR(255) NULL,
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Menu items
CREATE TABLE menu_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE,
    description TEXT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) NULL,
    calories INT UNSIGNED NULL,
    is_available TINYINT(1) NOT NULL DEFAULT 1,
    is_special TINYINT(1) NOT NULL DEFAULT 0,
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    prep_time_minutes INT UNSIGNED NOT NULL DEFAULT 10,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    INDEX idx_menu_category_available (category_id, is_available),
    FULLTEXT INDEX ft_menu_search (name, description)
) ENGINE=InnoDB;

-- Inventory
CREATE TABLE inventory (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    menu_item_id INT UNSIGNED NOT NULL UNIQUE,
    quantity INT NOT NULL DEFAULT 0,
    low_stock_threshold INT NOT NULL DEFAULT 10,
    last_restocked_at DATETIME NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Shopping cart
CREATE TABLE cart (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id INT UNSIGNED NOT NULL,
    menu_item_id INT UNSIGNED NOT NULL,
    quantity INT UNSIGNED NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE,
    UNIQUE KEY uk_cart_student_item (student_id, menu_item_id)
) ENGINE=InnoDB;

-- Orders
CREATE TABLE orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id INT UNSIGNED NOT NULL,
    order_number VARCHAR(30) NOT NULL UNIQUE,
    status ENUM('pending','accepted','preparing','ready','completed','rejected','cancelled') NOT NULL DEFAULT 'pending',
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0,
    discount_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    loyalty_points_used INT UNSIGNED NOT NULL DEFAULT 0,
    pickup_time DATETIME NOT NULL,
    notes TEXT NULL,
    qr_code VARCHAR(255) NULL,
    coupon_id INT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE RESTRICT,
    INDEX idx_orders_status_created (status, created_at),
    INDEX idx_orders_student (student_id)
) ENGINE=InnoDB;

-- Order line items
CREATE TABLE order_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    menu_item_id INT UNSIGNED NOT NULL,
    item_name VARCHAR(150) NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE RESTRICT,
    INDEX idx_order_items_order (order_id)
) ENGINE=InnoDB;

-- Payments
CREATE TABLE payments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    method ENUM('cash','mobile_money','card') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
    mpesa_checkout_id VARCHAR(100) NULL,
    mpesa_receipt VARCHAR(50) NULL,
    mpesa_phone VARCHAR(20) NULL,
    transaction_ref VARCHAR(100) NULL,
    paid_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    INDEX idx_payments_checkout (mpesa_checkout_id),
    INDEX idx_payments_order (order_id)
) ENGINE=InnoDB;

-- Notifications
CREATE TABLE notifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id INT UNSIGNED NOT NULL,
    type VARCHAR(50) NOT NULL DEFAULT 'info',
    title VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    link VARCHAR(255) NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    INDEX idx_notifications_student_read (student_id, is_read)
) ENGINE=InnoDB;

-- Announcements
CREATE TABLE announcements (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    content TEXT NOT NULL,
    image VARCHAR(255) NULL,
    target_role ENUM('all','student','staff','admin') NOT NULL DEFAULT 'all',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    starts_at DATETIME NULL,
    ends_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Coupons
CREATE TABLE coupons (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(30) NOT NULL UNIQUE,
    description VARCHAR(255) NULL,
    discount_type ENUM('percentage','fixed') NOT NULL DEFAULT 'percentage',
    discount_value DECIMAL(10,2) NOT NULL,
    min_order_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    usage_limit INT UNSIGNED NULL,
    times_used INT UNSIGNED NOT NULL DEFAULT 0,
    starts_at DATETIME NULL,
    expires_at DATETIME NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Coupon redemptions
CREATE TABLE coupon_redemptions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    coupon_id INT UNSIGNED NOT NULL,
    student_id INT UNSIGNED NOT NULL,
    order_id INT UNSIGNED NOT NULL,
    discount_applied DECIMAL(10,2) NOT NULL,
    redeemed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE RESTRICT,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Favorites
CREATE TABLE favorites (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id INT UNSIGNED NOT NULL,
    menu_item_id INT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE,
    UNIQUE KEY uk_favorites (student_id, menu_item_id)
) ENGINE=InnoDB;

-- Add FK for orders.coupon_id after coupons table exists
ALTER TABLE orders
    ADD CONSTRAINT fk_orders_coupon FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE SET NULL;

-- School e-Café Seed Data
-- Password for all accounts: Password123!

SET @pwd = '$2b$10$IhYoNcXsz1IsAn5NquYD1efa3gBheDrzigGFwkiQqSCpSHKrI4SEO';

INSERT INTO admins (username, email, password_hash, full_name) VALUES
('admin', 'admin@schoolcafe.local', @pwd, 'System Administrator');

INSERT INTO staff (username, email, password_hash, full_name, phone) VALUES
('staff01', 'staff01@schoolcafe.local', @pwd, 'Jane Wanjiku', '254712345678'),
('staff02', 'staff02@schoolcafe.local', @pwd, 'Peter Ochieng', '254723456789');

INSERT INTO students (student_id, email, password_hash, full_name, phone, grade, loyalty_points) VALUES
('STU001', 'john@student.school.edu', @pwd, 'John Kamau', '254711111111', 'Form 3', 150),
('STU002', 'mary@student.school.edu', @pwd, 'Mary Akinyi', '254722222222', 'Form 4', 80),
('STU003', 'david@student.school.edu', @pwd, 'David Mwangi', '254733333333', 'Form 2', 200),
('STU004', 'grace@student.school.edu', @pwd, 'Grace Wambui', '254744444444', 'Form 1', 50),
('STU005', 'james@student.school.edu', @pwd, 'James Otieno', '254755555555', 'Form 4', 120);

INSERT INTO categories (name, slug, description, sort_order) VALUES
('Breakfast', 'breakfast', 'Start your day right', 1),
('Lunch', 'lunch', 'Hearty midday meals', 2),
('Snacks', 'snacks', 'Quick bites', 3),
('Drinks', 'drinks', 'Beverages and refreshments', 4),
('Specials', 'specials', 'Chef specials', 5),
('Healthy', 'healthy', 'Nutritious options', 6);

INSERT INTO menu_items (category_id, name, slug, description, price, is_available, is_special, prep_time_minutes) VALUES
(1, 'Pancakes & Syrup', 'pancakes-syrup', 'Fluffy pancakes with maple syrup', 120.00, 1, 1, 10),
(1, 'Eggs & Toast', 'eggs-toast', 'Scrambled eggs with buttered toast', 100.00, 1, 0, 8),
(1, 'Oatmeal Bowl', 'oatmeal-bowl', 'Warm oatmeal with fruits', 90.00, 1, 0, 5),
(2, 'Chicken Rice', 'chicken-rice', 'Grilled chicken with seasoned rice', 250.00, 1, 1, 15),
(2, 'Beef Stew & Ugali', 'beef-stew-ugali', 'Traditional beef stew with ugali', 200.00, 1, 0, 15),
(2, 'Veggie Pasta', 'veggie-pasta', 'Pasta with mixed vegetables', 180.00, 1, 0, 12),
(2, 'Fish & Chips', 'fish-chips', 'Crispy fish fillet with chips', 280.00, 1, 0, 18),
(3, 'Samosa (3pc)', 'samosa', 'Crispy beef samosas', 60.00, 1, 0, 5),
(3, 'Chapati Wrap', 'chapati-wrap', 'Chapati rolled with filling', 80.00, 1, 0, 8),
(3, 'Fruit Salad', 'fruit-salad', 'Fresh seasonal fruits', 70.00, 1, 0, 5),
(4, 'Fresh Juice', 'fresh-juice', 'Orange or mango juice', 50.00, 1, 0, 3),
(4, 'Milkshake', 'milkshake', 'Chocolate or vanilla milkshake', 80.00, 1, 1, 5),
(4, 'Bottled Water', 'bottled-water', '500ml mineral water', 30.00, 1, 0, 1),
(4, 'Hot Tea', 'hot-tea', 'Kenyan tea with milk', 40.00, 1, 0, 3),
(5, 'Chef Burger', 'chef-burger', 'Special loaded burger', 300.00, 1, 1, 20),
(5, 'Pizza Slice', 'pizza-slice', 'Cheese or pepperoni slice', 150.00, 1, 0, 10),
(6, 'Grilled Chicken Salad', 'grilled-chicken-salad', 'Lean protein salad bowl', 220.00, 1, 0, 12),
(6, 'Veggie Wrap', 'veggie-wrap', 'Whole wheat wrap with veggies', 160.00, 1, 0, 10),
(6, 'Smoothie Bowl', 'smoothie-bowl', 'Acai smoothie with granola', 190.00, 1, 1, 8),
(6, 'Brown Rice Bowl', 'brown-rice-bowl', 'Brown rice with steamed veggies', 170.00, 1, 0, 12);

INSERT INTO inventory (menu_item_id, quantity, low_stock_threshold)
SELECT id, 50 + (id * 3) % 40, 10 FROM menu_items;

INSERT INTO announcements (title, content, target_role, is_active) VALUES
('Welcome to e-Café!', 'Order online and skip the queue. New students get 10% off with code WELCOME10.', 'student', 1),
('Extended Hours', 'Café now open until 4 PM on weekdays.', 'all', 1),
('Healthy Eating Week', 'Try our healthy menu items and earn double loyalty points!', 'student', 1);

INSERT INTO coupons (code, description, discount_type, discount_value, min_order_amount, usage_limit, expires_at) VALUES
('WELCOME10', '10% off for new students', 'percentage', 10.00, 100.00, 100, DATE_ADD(NOW(), INTERVAL 90 DAY)),
('SAVE50', 'KES 50 off orders over 300', 'fixed', 50.00, 300.00, 50, DATE_ADD(NOW(), INTERVAL 60 DAY));

INSERT INTO favorites (student_id, menu_item_id) VALUES (1, 4), (1, 12), (2, 8), (3, 15);

INSERT INTO orders (student_id, order_number, status, subtotal, discount_amount, total_amount, pickup_time, created_at) VALUES
(1, 'EC-20250623-0001', 'completed', 250.00, 0, 250.00, DATE_ADD(NOW(), INTERVAL -2 HOUR), DATE_ADD(NOW(), INTERVAL -3 HOUR)),
(2, 'EC-20250623-0002', 'ready', 180.00, 18.00, 162.00, DATE_ADD(NOW(), INTERVAL 30 MINUTE), DATE_ADD(NOW(), INTERVAL -1 HOUR)),
(3, 'EC-20250623-0003', 'preparing', 340.00, 0, 340.00, DATE_ADD(NOW(), INTERVAL 45 MINUTE), DATE_ADD(NOW(), INTERVAL -30 MINUTE)),
(4, 'EC-20250623-0004', 'accepted', 120.00, 0, 120.00, DATE_ADD(NOW(), INTERVAL 1 HOUR), DATE_ADD(NOW(), INTERVAL -15 MINUTE)),
(5, 'EC-20250623-0005', 'pending', 200.00, 0, 200.00, DATE_ADD(NOW(), INTERVAL 1 HOUR), NOW());

INSERT INTO order_items (order_id, menu_item_id, item_name, quantity, unit_price, subtotal) VALUES
(1, 4, 'Chicken Rice', 1, 250.00, 250.00),
(2, 6, 'Veggie Pasta', 1, 180.00, 180.00),
(3, 15, 'Chef Burger', 1, 300.00, 300.00),
(3, 11, 'Fresh Juice', 1, 50.00, 50.00),
(4, 1, 'Pancakes & Syrup', 1, 120.00, 120.00),
(5, 5, 'Beef Stew & Ugali', 1, 200.00, 200.00);

INSERT INTO payments (order_id, method, amount, status, paid_at) VALUES
(1, 'mobile_money', 250.00, 'paid', DATE_ADD(NOW(), INTERVAL -3 HOUR)),
(2, 'cash', 162.00, 'paid', NULL),
(3, 'mobile_money', 340.00, 'pending', NULL),
(4, 'cash', 120.00, 'pending', NULL),
(5, 'card', 200.00, 'pending', NULL);

INSERT INTO notifications (student_id, type, title, message, is_read) VALUES
(1, 'order', 'Order Placed', 'Your order EC-20250623-0001 has been placed.', 1),
(2, 'success', 'Order Ready!', 'Your order EC-20250623-0002 is ready for pickup.', 0),
(3, 'order', 'Order Update', 'Order EC-20250623-0003 status: Preparing', 0);
