-- School e-Café Seed Data
-- Password for all accounts: Password123!

USE ecafe_db;

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
