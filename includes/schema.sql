-- ============================================================
-- Food Delivery Platform — Enhanced Database Schema
-- ============================================================
CREATE DATABASE IF NOT EXISTS food_delivery_db;
USE food_delivery_db;

-- ============================================================
-- 1. USERS: Customers, Merchants, Drivers, Admins
-- ============================================================
CREATE TABLE users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    full_name     VARCHAR(100)  NOT NULL,
    email         VARCHAR(100)  NOT NULL UNIQUE,
    password      VARCHAR(255)  NOT NULL,
    phone_number  VARCHAR(20)   DEFAULT NULL,
    avatar_url    VARCHAR(255)  DEFAULT NULL,
    role          ENUM('Customer', 'Merchant', 'Driver', 'Admin') NOT NULL DEFAULT 'Customer',
    created_at    TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- 2. CATEGORIES: Food categories (Pizza, Sushi, Burgers…)
-- ============================================================
CREATE TABLE categories (
    id    INT AUTO_INCREMENT PRIMARY KEY,
    name  VARCHAR(100) NOT NULL,
    icon  VARCHAR(255) DEFAULT NULL   -- emoji or icon URL
);

-- ============================================================
-- 3. RESTAURANTS: Owned by Merchant users
-- ============================================================
CREATE TABLE restaurants (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    owner_id        INT           NOT NULL,
    name            VARCHAR(150)  NOT NULL,
    description     TEXT          DEFAULT NULL,
    logo_url        VARCHAR(255)  DEFAULT NULL,
    banner_url      VARCHAR(255)  DEFAULT NULL,
    rating          DECIMAL(2,1)  DEFAULT 0.0,
    is_open         TINYINT(1)    NOT NULL DEFAULT 1,
    location_lat    DECIMAL(10,8) DEFAULT NULL,
    location_long   DECIMAL(11,8) DEFAULT NULL,
    address         VARCHAR(255)  DEFAULT NULL,
    category_id     INT           DEFAULT NULL,
    delivery_time   INT           DEFAULT 30,    -- minutes
    delivery_fee    INT           DEFAULT 0,     -- cents / VND
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id)    REFERENCES users(id)       ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id)  ON DELETE SET NULL
);

-- ============================================================
-- 4. MENU ITEMS: Food items belonging to a restaurant
-- ============================================================
CREATE TABLE menu_items (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    restaurant_id   INT           NOT NULL,
    category_id     INT           DEFAULT NULL,
    name            VARCHAR(150)  NOT NULL,
    price           INT           NOT NULL,          -- base price (cents / VND)
    discount_price  INT           DEFAULT NULL,      -- sale price if discounted
    description     TEXT          DEFAULT NULL,
    image_url       VARCHAR(255)  DEFAULT NULL,
    is_available    TINYINT(1)    NOT NULL DEFAULT 1,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id)   REFERENCES categories(id)  ON DELETE SET NULL
);

-- ============================================================
-- 5. VOUCHERS: Discount codes for orders
-- ============================================================
CREATE TABLE vouchers (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    code             VARCHAR(50)    NOT NULL UNIQUE,
    discount_amount  INT            NOT NULL,      -- fixed amount or percentage
    discount_type    ENUM('fixed', 'percent')    NOT NULL DEFAULT 'fixed',
    min_spend        INT            DEFAULT 0,
    max_uses         INT            DEFAULT NULL,  -- NULL = unlimited
    used_count       INT            DEFAULT 0,
    expiry_date      DATE           NOT NULL,
    is_active        TINYINT(1)     NOT NULL DEFAULT 1,
    created_at       TIMESTAMP      DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- 6. ORDERS: Customer orders
-- ============================================================
CREATE TABLE orders (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    user_id          INT           NOT NULL,
    restaurant_id    INT           NOT NULL,
    driver_id        INT           DEFAULT NULL,
    voucher_id       INT           DEFAULT NULL,
    delivery_address VARCHAR(255)  NOT NULL,
    total_amount     INT           NOT NULL,
    shipping_fee     INT           NOT NULL DEFAULT 0,
    discount_amount  INT           NOT NULL DEFAULT 0,
    payment_method   ENUM('Cash', 'Card', 'E-wallet', 'Bank Transfer') NOT NULL DEFAULT 'Cash',
    payment_status   ENUM('Unpaid', 'Paid', 'Refunded')                NOT NULL DEFAULT 'Unpaid',
    order_date       TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)       REFERENCES users(id)       ON DELETE CASCADE,
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE RESTRICT,
    FOREIGN KEY (driver_id)     REFERENCES users(id)       ON DELETE SET NULL,
    FOREIGN KEY (voucher_id)    REFERENCES vouchers(id)    ON DELETE SET NULL
);

-- ============================================================
-- 7. ORDER ITEMS: Individual food items within an order
-- ============================================================
CREATE TABLE order_items (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    order_id      INT  NOT NULL,
    menu_item_id  INT  NOT NULL,
    quantity      INT  NOT NULL DEFAULT 1,
    unit_price    INT  NOT NULL,    -- price at time of order
    notes         TEXT DEFAULT NULL,
    FOREIGN KEY (order_id)     REFERENCES orders(id)      ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id)  ON DELETE RESTRICT
);

-- ============================================================
-- 8. ORDER STATUS LOGS: Full audit trail of order lifecycle
-- ============================================================
CREATE TABLE order_status_logs (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    order_id    INT  NOT NULL,
    status      ENUM('Pending', 'Confirmed', 'Preparing', 'Shipping', 'Completed', 'Cancelled') NOT NULL,
    note        TEXT      DEFAULT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- ============================================================
-- 9. REVIEWS: Customer ratings for restaurants
-- ============================================================
CREATE TABLE reviews (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    user_id        INT            NOT NULL,
    restaurant_id  INT            NOT NULL,
    order_id       INT            DEFAULT NULL,
    rating         TINYINT        NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment        TEXT           DEFAULT NULL,
    created_at     TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)       REFERENCES users(id)       ON DELETE CASCADE,
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id)      REFERENCES orders(id)      ON DELETE SET NULL
);

-- ============================================================
-- 10. SAVED ADDRESSES: User's address book
-- ============================================================
CREATE TABLE saved_addresses (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    user_id       INT           NOT NULL,
    label         VARCHAR(50)   NOT NULL DEFAULT 'Home',  -- 'Home', 'Office', etc.
    full_address  VARCHAR(255)  NOT NULL,
    location_lat  DECIMAL(10,8) DEFAULT NULL,
    location_long DECIMAL(11,8) DEFAULT NULL,
    is_default    TINYINT(1)    NOT NULL DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);