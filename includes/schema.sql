-- -- ============================================================
-- -- Food Delivery Platform — Enhanced Database Schema (v2)
-- -- Updated: 2026-04-10
-- -- Changes: Added social_login_id, OTP verification, phone_verified
-- --           to users; extended reviews with category ratings,
-- --           photo_urls, quick_tags; added user_sessions table;
-- --           discount_price already present in menu_items.
-- -- ============================================================
-- CREATE DATABASE IF NOT EXISTS food_delivery_db;
-- USE food_delivery_db;

-- -- ============================================================
-- -- 1. USERS: Customers, Merchants, Admins
-- --    Includes social login IDs (Google/Facebook) and phone OTP
-- -- ============================================================
-- CREATE TABLE users (
--     id                  INT AUTO_INCREMENT PRIMARY KEY,
--     full_name           VARCHAR(100)  NOT NULL,
--     email               VARCHAR(100)  DEFAULT NULL UNIQUE,
--     password            VARCHAR(255)  DEFAULT NULL,      -- NULL for social/phone-only accounts
--     phone_number        VARCHAR(20)   DEFAULT NULL UNIQUE,
--     phone_verified      TINYINT(1)    NOT NULL DEFAULT 0, -- 1 = verified via OTP
--     avatar_url          VARCHAR(255)  DEFAULT NULL,
--     role                ENUM('Customer', 'Merchant', 'Admin') NOT NULL DEFAULT 'Customer',

--     -- Social login
--     google_id           VARCHAR(255)  DEFAULT NULL UNIQUE, -- Google UID
--     facebook_id         VARCHAR(255)  DEFAULT NULL UNIQUE, -- Facebook UID

--     -- OTP phone verification
--     otp_code            VARCHAR(6)    DEFAULT NULL,        -- latest OTP hash
--     otp_expires_at      TIMESTAMP     DEFAULT NULL,

--     -- Account state
--     is_active           TINYINT(1)    NOT NULL DEFAULT 1,
--     created_at          TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
--     updated_at          TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

--     CHECK (email IS NOT NULL OR phone_number IS NOT NULL) -- must have at least one
-- );

-- -- ============================================================
-- -- 2. USER SESSIONS: JWT / token store for auth
-- -- ============================================================
-- CREATE TABLE user_sessions (
--     id            INT AUTO_INCREMENT PRIMARY KEY,
--     user_id       INT           NOT NULL,
--     token_hash    VARCHAR(255)  NOT NULL,                  -- hashed refresh token
--     device_info   VARCHAR(255)  DEFAULT NULL,              -- e.g. "Chrome / Windows"
--     ip_address    VARCHAR(45)   DEFAULT NULL,
--     expires_at    TIMESTAMP     NOT NULL,
--     created_at    TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
--     FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
-- );

-- -- ============================================================
-- -- 3. CATEGORIES: Food categories (Pizza, Sushi, Burgers…)
-- -- ============================================================
-- CREATE TABLE categories (
--     id    INT AUTO_INCREMENT PRIMARY KEY,
--     name  VARCHAR(100) NOT NULL,
--     icon  VARCHAR(255) DEFAULT NULL   -- emoji or icon URL
-- );

-- -- ============================================================
-- -- 4. RESTAURANTS: Owned by Merchant users
-- -- ============================================================
-- CREATE TABLE restaurants (
--     id              INT           AUTO_INCREMENT PRIMARY KEY,
--     owner_id        INT           NOT NULL,
--     name            VARCHAR(150)  NOT NULL,
--     description     TEXT          DEFAULT NULL,
--     logo_url        VARCHAR(255)  DEFAULT NULL,
--     banner_url      VARCHAR(255)  DEFAULT NULL,
--     rating          DECIMAL(2,1)  DEFAULT 0.0,
--     review_count    INT           NOT NULL DEFAULT 0,
--     is_open         TINYINT(1)    NOT NULL DEFAULT 1,
--     location_lat    DECIMAL(10,8) DEFAULT NULL,
--     location_long   DECIMAL(11,8) DEFAULT NULL,
--     address         VARCHAR(255)  DEFAULT NULL,
--     category_id     INT           DEFAULT NULL,
--     delivery_time   INT           DEFAULT 30,    -- estimated minutes
--     delivery_fee    INT           DEFAULT 0,     -- cents / VND (0 = free)
--     min_order       INT           DEFAULT 0,     -- minimum order amount
--     created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
--     FOREIGN KEY (owner_id)    REFERENCES users(id)       ON DELETE CASCADE,
--     FOREIGN KEY (category_id) REFERENCES categories(id)  ON DELETE SET NULL
-- );

-- -- ============================================================
-- -- 5. MENU ITEMS: Food items belonging to a restaurant
-- -- ============================================================
-- CREATE TABLE menu_items (
--     id              INT           AUTO_INCREMENT PRIMARY KEY,
--     restaurant_id   INT           NOT NULL,
--     category_id     INT           DEFAULT NULL,
--     name            VARCHAR(150)  NOT NULL,
--     description     TEXT          DEFAULT NULL,
--     image_url       VARCHAR(255)  DEFAULT NULL,
--     price           INT           NOT NULL,           -- base price (cents / VND)
--     discount_price  INT           DEFAULT NULL,       -- promotional/sale price
--     is_available    TINYINT(1)    NOT NULL DEFAULT 1,
--     is_featured     TINYINT(1)    NOT NULL DEFAULT 0, -- pinned to Popular tab
--     sort_order      INT           DEFAULT 0,
--     created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
--     FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE,
--     FOREIGN KEY (category_id)   REFERENCES categories(id)  ON DELETE SET NULL
-- );

-- -- ============================================================
-- -- 6. VOUCHERS: Discount codes for orders
-- -- ============================================================
-- CREATE TABLE vouchers (
--     id               INT AUTO_INCREMENT PRIMARY KEY,
--     code             VARCHAR(50)    NOT NULL UNIQUE,
--     discount_amount  INT            NOT NULL,      -- value (amount or percentage)
--     discount_type    ENUM('fixed', 'percent') NOT NULL DEFAULT 'fixed',
--     min_spend        INT            DEFAULT 0,     -- minimum order total required
--     max_uses         INT            DEFAULT NULL,  -- NULL = unlimited
--     used_count       INT            DEFAULT 0,
--     expiry_date      DATE           NOT NULL,
--     is_active        TINYINT(1)     NOT NULL DEFAULT 1,
--     created_at       TIMESTAMP      DEFAULT CURRENT_TIMESTAMP
-- );

-- -- ============================================================
-- -- 7. ORDERS: Customer orders
-- -- ============================================================
-- CREATE TABLE orders (
--     id               INT AUTO_INCREMENT PRIMARY KEY,
--     user_id          INT           NOT NULL,
--     restaurant_id    INT           NOT NULL,
--     voucher_id       INT           DEFAULT NULL,
--     delivery_address VARCHAR(255)  NOT NULL,
--     total_amount     INT           NOT NULL,
--     shipping_fee     INT           NOT NULL DEFAULT 0,
--     discount_amount  INT           NOT NULL DEFAULT 0,
--     payment_method   ENUM('Cash', 'Card', 'E-wallet', 'Bank Transfer') NOT NULL DEFAULT 'Cash',
--     payment_status   ENUM('Unpaid', 'Paid', 'Refunded') NOT NULL DEFAULT 'Unpaid',
--     order_date       TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
--     FOREIGN KEY (user_id)       REFERENCES users(id)       ON DELETE CASCADE,
--     FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE RESTRICT,
--     FOREIGN KEY (voucher_id)    REFERENCES vouchers(id)    ON DELETE SET NULL
-- );

-- -- ============================================================
-- -- 8. ORDER ITEMS: Individual food items within an order
-- -- ============================================================
-- CREATE TABLE order_items (
--     id            INT  AUTO_INCREMENT PRIMARY KEY,
--     order_id      INT  NOT NULL,
--     menu_item_id  INT  NOT NULL,
--     quantity      INT  NOT NULL DEFAULT 1,
--     unit_price    INT  NOT NULL,    -- price locked at order time
--     notes         TEXT DEFAULT NULL,
--     FOREIGN KEY (order_id)     REFERENCES orders(id)      ON DELETE CASCADE,
--     FOREIGN KEY (menu_item_id) REFERENCES menu_items(id)  ON DELETE RESTRICT
-- );

-- -- ============================================================
-- -- 9. ORDER STATUS LOGS: Full audit trail of order lifecycle
-- -- ============================================================
-- CREATE TABLE order_status_logs (
--     id          INT  AUTO_INCREMENT PRIMARY KEY,
--     order_id    INT  NOT NULL,
--     status      ENUM('Pending', 'Confirmed', 'Preparing', 'Shipping', 'Completed', 'Cancelled') NOT NULL,
--     note        TEXT      DEFAULT NULL,
--     created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
-- );

-- -- ============================================================
-- -- 10. REVIEWS: Customer ratings for restaurants
-- --     Linked to a specific order to prevent duplicate reviews
-- --     Includes sub-category ratings and photo URLs (JSON array)
-- -- ============================================================
-- CREATE TABLE reviews (
--     id                    INT AUTO_INCREMENT PRIMARY KEY,
--     user_id               INT            NOT NULL,
--     restaurant_id         INT            NOT NULL,
--     order_id              INT            NOT NULL UNIQUE, -- one review per order
--     rating                TINYINT        NOT NULL CHECK (rating BETWEEN 1 AND 5),

--     -- Sub-category ratings (1-5, NULL = not rated)
--     rating_food_quality   TINYINT        DEFAULT NULL CHECK (rating_food_quality BETWEEN 1 AND 5),
--     rating_delivery_speed TINYINT        DEFAULT NULL CHECK (rating_delivery_speed BETWEEN 1 AND 5),
--     rating_packaging      TINYINT        DEFAULT NULL CHECK (rating_packaging BETWEEN 1 AND 5),

--     -- Review content
--     comment               TEXT           DEFAULT NULL,
--     quick_tags            VARCHAR(500)   DEFAULT NULL, -- JSON array: ["Hot & Fresh","Super Fast"]
--     photo_urls            TEXT           DEFAULT NULL, -- JSON array of image URLs/paths

--     is_hidden             TINYINT(1)     NOT NULL DEFAULT 0, -- soft-delete by admin
--     created_at            TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
--     updated_at            TIMESTAMP      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

--     FOREIGN KEY (user_id)       REFERENCES users(id)       ON DELETE CASCADE,
--     FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE,
--     FOREIGN KEY (order_id)      REFERENCES orders(id)      ON DELETE CASCADE
-- );

-- -- ============================================================
-- -- 11. SAVED ADDRESSES: User's address book
-- -- ============================================================
-- CREATE TABLE saved_addresses (
--     id            INT AUTO_INCREMENT PRIMARY KEY,
--     user_id       INT           NOT NULL,
--     label         VARCHAR(50)   NOT NULL DEFAULT 'Home',  -- 'Home', 'Office', etc.
--     full_address  VARCHAR(255)  NOT NULL,
--     location_lat  DECIMAL(10,8) DEFAULT NULL,
--     location_long DECIMAL(11,8) DEFAULT NULL,
--     is_default    TINYINT(1)    NOT NULL DEFAULT 0,
--     FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
-- );

-- -- ============================================================
-- -- INDEXES: Optimise common query patterns
-- -- ============================================================
-- CREATE INDEX idx_users_phone        ON users(phone_number);
-- CREATE INDEX idx_users_google       ON users(google_id);
-- CREATE INDEX idx_users_facebook     ON users(facebook_id);
-- CREATE INDEX idx_orders_user        ON orders(user_id);
-- CREATE INDEX idx_orders_restaurant  ON orders(restaurant_id);
-- CREATE INDEX idx_reviews_restaurant ON reviews(restaurant_id);
-- CREATE INDEX idx_reviews_user       ON reviews(user_id);
-- CREATE INDEX idx_menu_restaurant    ON menu_items(restaurant_id);
-- CREATE INDEX idx_sessions_user      ON user_sessions(user_id);
-- CREATE INDEX idx_status_logs_order  ON order_status_logs(order_id);

-- 1. Tạo và sử dụng đúng Database
CREATE DATABASE IF NOT EXISTS FOOD_ORDERING_DB;

USE FOOD_ORDERING_DB;

-- 2. Tạo bảng Người dùng (Users)
CREATE TABLE USERS (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    FULL_NAME VARCHAR(100) NOT NULL,
    EMAIL VARCHAR(100) UNIQUE NOT NULL,
    PASSWORD VARCHAR(255) NOT NULL,
    PHONE_NUMBER VARCHAR(20) DEFAULT NULL,
    DATE_OF_BIRTH DATE DEFAULT NULL,
    AVATAR_URL VARCHAR(255) DEFAULT NULL,
    ROLE ENUM('customer', 'admin') DEFAULT 'customer',
    CREATED_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UPDATED_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 3. Tạo bảng Danh mục món ăn (Categories)
CREATE TABLE CATEGORIES (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    NAME VARCHAR(100) NOT NULL
);

-- 4. Tạo bảng Món ăn (Menu Items)
CREATE TABLE MENU_ITEMS (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    NAME VARCHAR(100) NOT NULL,
    PRICE DECIMAL(10, 2) NOT NULL,
    DESCRIPTION TEXT,
    IMAGE_URL VARCHAR(255),
    CATEGORY_ID INT,
    FOREIGN KEY (CATEGORY_ID) REFERENCES CATEGORIES(ID) ON DELETE SET NULL
);

-- 5. Tạo bảng Đơn hàng (Orders)
CREATE TABLE ORDERS (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    USER_ID INT,
    TOTAL_AMOUNT DECIMAL(10, 2) NOT NULL,
    STATUS ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    CREATED_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (USER_ID) REFERENCES USERS(ID) ON DELETE CASCADE
);

-- 6. Tạo bảng Saved Addresses
CREATE TABLE SAVED_ADDRESSES (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    USER_ID INT NOT NULL,
    LABEL VARCHAR(50) NOT NULL,
    FULL_ADDRESS VARCHAR(255) NOT NULL,
    IS_DEFAULT TINYINT(1) NOT NULL DEFAULT 0,
    LOCATION_LAT DECIMAL(10, 8) DEFAULT NULL,
    LOCATION_LONG DECIMAL(11, 8) DEFAULT NULL,
    CREATED_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (USER_ID) REFERENCES USERS(ID) ON DELETE CASCADE
);

-- 7. Tạo bảng Chi tiết đơn hàng (Order Items)
CREATE TABLE ORDER_ITEMS (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    ORDER_ID INT,
    MENU_ITEM_ID INT,
    QUANTITY INT NOT NULL,
    PRICE DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (ORDER_ID) REFERENCES ORDERS(ID) ON DELETE CASCADE,
    FOREIGN KEY (MENU_ITEM_ID) REFERENCES MENU_ITEMS(ID) ON DELETE CASCADE
);

-- 7. Nạp dữ liệu mẫu (Sample Data)
INSERT INTO USERS (
    FULL_NAME,
    EMAIL,
    PASSWORD,
    ROLE
) VALUES (
    'System Admin',
    'admin@foodweb.com',
    'hashed_password_1',
    'admin'
),
(
    'John Doe',
    'johndoe@gmail.com',
    'hashed_password_2',
    'customer'
);

INSERT INTO CATEGORIES (
    NAME
) VALUES (
    'Fast Food'
),
(
    'Beverages'
),
(
    'Main Courses'
);

INSERT INTO MENU_ITEMS (
    NAME,
    PRICE,
    DESCRIPTION,
    IMAGE_URL,
    CATEGORY_ID
) VALUES (
    'Crispy Fried Chicken',
    45000,
    'Korean style crispy fried chicken',
    'fried_chicken.jpg',
    1
),
(
    'Boba Milk Tea',
    30000,
    'Traditional black milk tea with boba',
    'milk_tea.jpg',
    2
),
(
    'Broken Rice with Pork Chop',
    50000,
    'Vietnamese traditional broken rice',
    'broken_rice.jpg',
    3
);

INSERT INTO ORDERS (
    USER_ID,
    TOTAL_AMOUNT,
    STATUS
) VALUES (
    2,
    75000,
    'pending'
);

INSERT INTO ORDER_ITEMS (
    ORDER_ID,
    MENU_ITEM_ID,
    QUANTITY,
    PRICE
) VALUES (
    1,
    1,
    2,
    45000
),
(
    1,
    2,
    1,
    30000
);