INSERT INTO users (full_name, email, password, role) VALUES 
('System Admin', 'admin@foodweb.com', 'hashed_password_1', 'admin'),
('John Doe', 'johndoe@gmail.com', 'hashed_password_2', 'customer');

INSERT INTO categories (name) VALUES 
('Fast Food'), 
('Beverages'), 
('Main Courses');

INSERT INTO menu_items (name, price, description, image_url, category_id) VALUES 
('Crispy Fried Chicken', 45000, 'Korean style crispy fried chicken', 'fried_chicken.jpg', 1),
('Boba Milk Tea', 30000, 'Traditional black milk tea with boba', 'milk_tea.jpg', 2),
('Broken Rice with Pork Chop', 50000, 'Vietnamese traditional broken rice', 'broken_rice.jpg', 3);

INSERT INTO orders (user_id, total_amount, status) VALUES 
(2, 75000, 'pending');

INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES 
(1, 1, 2, 45000),
(1, 2, 1, 30000);

SELECT * FROM categories;
SELECT * FROM menu_items;
SELECT * FROM orders;
SELECT * FROM users;
SELECT * FROM order_items;