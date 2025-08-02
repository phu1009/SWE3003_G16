/* ---------- 1.  Users, Roles, Authentication ---------- */
CREATE TABLE roles (
    role_id       INT AUTO_INCREMENT PRIMARY KEY,
    role_name     VARCHAR(50) NOT NULL UNIQUE          -- 'customer','pharmacist','cashier','manager','admin'
);

CREATE TABLE users (
    user_id       INT AUTO_INCREMENT PRIMARY KEY,
    email         VARCHAR(120) NOT NULL UNIQUE,
    password_hash CHAR(60)     NOT NULL,
    full_name     VARCHAR(120) NOT NULL,
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE user_roles (
    user_id INT NOT NULL,
    role_id INT NOT NULL,
    PRIMARY KEY (user_id, role_id), 
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (role_id) REFERENCES roles(role_id)
);

/* Customers are optional if you want a separate profile
   (e.g., for shipping addresses, loyalty points)         */
CREATE TABLE customers (
    customer_id   INT PRIMARY KEY,                      -- FK mirrors users.user_id
    phone         VARCHAR(20),
    date_of_birth DATE,
    FOREIGN KEY (customer_id) REFERENCES users(user_id)
);

/* ---------- 2.  Branches & Products ---------- */
CREATE TABLE branches (
    branch_id   INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    address     VARCHAR(255),
    city        VARCHAR(100),
    phone       VARCHAR(20)
);

CREATE TABLE products (
    product_id    INT AUTO_INCREMENT PRIMARY KEY,
    sku           VARCHAR(50)  NOT NULL UNIQUE,
    product_name  VARCHAR(150) NOT NULL,
    description   TEXT,
    unit_price    DECIMAL(10,2) NOT NULL,
    is_rx_only    BOOLEAN DEFAULT FALSE             -- true  = prescription required
);

/* Inventory is per branch per product */
CREATE TABLE inventory (
    branch_id     INT NOT NULL,
    product_id    INT NOT NULL,
    quantity      INT NOT NULL DEFAULT 0,
    PRIMARY KEY (branch_id, product_id),
    FOREIGN KEY (branch_id)  REFERENCES branches(branch_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

/* ---------- 3.  Prescriptions ---------- */
CREATE TABLE prescriptions (
    prescription_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id     INT NOT NULL,
    pharmacist_id   INT,                              -- filled when validated
    file_path       VARCHAR(255) NOT NULL,            -- location in storage
    status          ENUM('UPLOADED','APPROVED','REJECTED') DEFAULT 'UPLOADED',
    notes           TEXT,
    uploaded_at     DATETIME DEFAULT CURRENT_TIMESTAMP,
    validated_at    DATETIME,
    FOREIGN KEY (customer_id)   REFERENCES customers(customer_id),
    FOREIGN KEY (pharmacist_id) REFERENCES users(user_id)
);

/* ---------- 4.  Orders & Line Items ---------- */
CREATE TABLE orders (
    order_id        INT AUTO_INCREMENT PRIMARY KEY,
    customer_id     INT NOT NULL,
    branch_id       INT NOT NULL,                     -- pick‑up / fulfilment branch
    prescription_id INT,                              -- nullable (OTC orders)
    total_amount    DECIMAL(10,2) NOT NULL,
    status          ENUM('PENDING','PAID','FULFILLED','CANCELLED') DEFAULT 'PENDING',
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id)     REFERENCES customers(customer_id),
    FOREIGN KEY (branch_id)       REFERENCES branches(branch_id),
    FOREIGN KEY (prescription_id) REFERENCES prescriptions(prescription_id)
);

CREATE TABLE order_items (
    order_id     INT NOT NULL,
    product_id   INT NOT NULL,
    quantity     INT NOT NULL,
    unit_price   DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (order_id, product_id),
    FOREIGN KEY (order_id)   REFERENCES orders(order_id)   ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

/* ---------- 5. Payments ---------- */
CREATE TABLE payments (
    payment_id     INT AUTO_INCREMENT PRIMARY KEY,
    order_id       INT NOT NULL,
    method         ENUM('CASH','CARD','EWALLET') NOT NULL,
    amount         DECIMAL(10,2) NOT NULL,
    status         ENUM('PENDING','SUCCESS','FAILED') DEFAULT 'PENDING',
    transaction_ref VARCHAR(100),                    -- returned by gateway / POS
    paid_at        DATETIME,
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
);

/* ---------- 6. Notifications ---------- */
CREATE TABLE notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id     INT NOT NULL,
    order_id        INT,
    channel         ENUM('EMAIL','SMS','IN_APP') NOT NULL,
    message         TEXT NOT NULL,
    sent_status     ENUM('PENDING','SENT','FAILED') DEFAULT 'PENDING',
    sent_at         DATETIME,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id),
    FOREIGN KEY (order_id)    REFERENCES orders(order_id)
);

/* ---------- 7. Audit / Soft Deletes (optional) ---------- */
/* Add `is_active` flags or history tables if regulations require it. */

/* ---------- Countries (manufacture / brand origin) ---------- */
CREATE TABLE IF NOT EXISTS countries (
  country_id   INT AUTO_INCREMENT PRIMARY KEY,
  country_name VARCHAR(80) UNIQUE NOT NULL
);

INSERT IGNORE INTO countries (country_name) VALUES
  ('Vietnam'), ('USA'), ('Germany'), ('France'), ('Japan');

/* ---------- Product Types (what kind of medical product?) ---------- */
CREATE TABLE IF NOT EXISTS product_types (
  type_id   INT AUTO_INCREMENT PRIMARY KEY,
  type_name VARCHAR(50) UNIQUE NOT NULL           -- Supplement, Drug, Cosmeceutical, ...
);

INSERT IGNORE INTO product_types (type_name) VALUES
  ('Drug'), ('Supplement'), ('Cosmeceutical'), ('Medical Device'), ('Other');

/* ---------- Targeted Patient Groups ---------- */
CREATE TABLE IF NOT EXISTS patient_groups (
  group_id   INT AUTO_INCREMENT PRIMARY KEY,
  group_name VARCHAR(50) UNIQUE NOT NULL          -- Children, Elderly, Pregnant, Diabetes, ...
);

INSERT IGNORE INTO patient_groups (group_name) VALUES
  ('Children'), ('Elderly'), ('Pregnant'), ('Adult'), ('Diabetes');

/* ---------- Brands ---------- */
CREATE TABLE IF NOT EXISTS brands (
  brand_id        INT AUTO_INCREMENT PRIMARY KEY,
  brand_name      VARCHAR(100) UNIQUE NOT NULL,
  origin_country  INT,
  FOREIGN KEY (origin_country) REFERENCES countries(country_id)
);

INSERT IGNORE INTO brands (brand_name, origin_country) VALUES
  ('DHG Pharma',  (SELECT country_id FROM countries WHERE country_name='Vietnam')),
  ('Bayer',       (SELECT country_id FROM countries WHERE country_name='Germany')),
  ('Pfizer',      (SELECT country_id FROM countries WHERE country_name='USA'));

ALTER TABLE products
  ADD COLUMN brand_id        INT,
  ADD COLUMN type_id         INT,                               -- FK to product_types
  ADD COLUMN origin_country  INT,                               -- if different from brand
  ADD COLUMN image_path      VARCHAR(255),                      -- relative or absolute URL
  ADD COLUMN rx_type         ENUM('OTC','RX','CONTROLLED') 
                             DEFAULT 'OTC',
  ADD COLUMN created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
  ADD COLUMN updated_at      DATETIME ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE products
  ADD FOREIGN KEY (brand_id)       REFERENCES brands(brand_id),
  ADD FOREIGN KEY (type_id)        REFERENCES product_types(type_id),
  ADD FOREIGN KEY (origin_country) REFERENCES countries(country_id);

CREATE TABLE IF NOT EXISTS product_patient_groups (
  product_id INT NOT NULL,
  group_id   INT NOT NULL,
  PRIMARY KEY (product_id, group_id),
  FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
  FOREIGN KEY (group_id)   REFERENCES patient_groups(group_id)
);

CREATE INDEX idx_products_price      ON products(unit_price);
CREATE INDEX idx_products_brand      ON products(brand_id);
CREATE INDEX idx_products_type       ON products(type_id);
CREATE INDEX idx_products_rx_type    ON products(rx_type);

ALTER TABLE products
  ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1
  COMMENT '1 = active / visible ‑‑ 0 = soft‑deleted';

UPDATE products SET is_active = 1;   -- ensure old rows are marked active

/* 1-a.  Keep one “avatar + default contact” row per user  */
CREATE TABLE IF NOT EXISTS user_profiles (
  user_id        INT PRIMARY KEY,                         -- 1 : 1 with users
  avatar_path    VARCHAR(255),                            -- /images/avatars/xxxx.jpg
  phone          VARCHAR(20),
  address_line1  VARCHAR(150),
  address_line2  VARCHAR(150),
  city           VARCHAR(100),
  state_region   VARCHAR(100),
  postal_code    VARCHAR(20),
  country        VARCHAR(100) DEFAULT 'Vietnam',
  latitude       DECIMAL(10,8),                           -- for “nearest store”
  longitude      DECIMAL(11,8),
  updated_at     DATETIME ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id)
);

/* 1-b.  Allow **multiple** addresses per user (shipping, work, etc.)        */
CREATE TABLE IF NOT EXISTS user_addresses (
  address_id     INT AUTO_INCREMENT PRIMARY KEY,
  user_id        INT NOT NULL,
  label          VARCHAR(50)  DEFAULT 'primary',          -- Home, Work …
  address_line1  VARCHAR(150) NOT NULL,
  address_line2  VARCHAR(150),
  city           VARCHAR(100),
  state_region   VARCHAR(100),
  postal_code    VARCHAR(20),
  country        VARCHAR(100) DEFAULT 'Vietnam',
  latitude       DECIMAL(10,8),
  longitude      DECIMAL(11,8),
  is_default     TINYINT(1) DEFAULT 0,
  created_at     DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at     DATETIME ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
  INDEX idx_user  (user_id),
  INDEX idx_geo   (latitude , longitude)
);

/* 1-c.  Quick one-liner if you just want an avatar and nothing else
         (put it right after full_name)                               */
ALTER TABLE users ADD COLUMN avatar_path VARCHAR(255) AFTER full_name;
