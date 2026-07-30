-- GiftVibe.lk Phase 1 database schema and initial seed data.
-- Database: dbof-giftvibelk

CREATE DATABASE IF NOT EXISTS `dbof-giftvibelk`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `dbof-giftvibelk`;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS activity_logs;
DROP TABLE IF EXISTS uploads;
DROP TABLE IF EXISTS expenses;
DROP TABLE IF EXISTS newsletter_subscribers;
DROP TABLE IF EXISTS gift_reminders;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS wishlists;
DROP TABLE IF EXISTS contact_messages;
DROP TABLE IF EXISTS faqs;
DROP TABLE IF EXISTS pages;
DROP TABLE IF EXISTS banners;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS deliveries;
DROP TABLE IF EXISTS order_status_history;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS cart_items;
DROP TABLE IF EXISTS carts;
DROP TABLE IF EXISTS coupons;
DROP TABLE IF EXISTS delivery_time_slots;
DROP TABLE IF EXISTS delivery_zones;
DROP TABLE IF EXISTS product_option_values;
DROP TABLE IF EXISTS product_options;
DROP TABLE IF EXISTS product_variants;
DROP TABLE IF EXISTS product_images;
DROP TABLE IF EXISTS product_recipients;
DROP TABLE IF EXISTS product_occasions;
DROP TABLE IF EXISTS product_categories;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS recipients;
DROP TABLE IF EXISTS occasions;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS customer_addresses;
DROP TABLE IF EXISTS customer_profiles;
DROP TABLE IF EXISTS password_resets;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS settings;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE roles (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  slug VARCHAR(80) NOT NULL UNIQUE,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  role_id BIGINT UNSIGNED NOT NULL,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  phone VARCHAR(40) NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  status ENUM('active','inactive','blocked') NOT NULL DEFAULT 'active',
  email_verified_at TIMESTAMP NULL,
  phone_verified_at TIMESTAMP NULL,
  last_login_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB;

CREATE TABLE password_resets (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  token VARCHAR(255) NOT NULL UNIQUE,
  expires_at TIMESTAMP NOT NULL,
  used_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_password_resets_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE customer_profiles (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL UNIQUE,
  date_of_birth DATE NULL,
  gender VARCHAR(40) NULL,
  notes TEXT NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_customer_profiles_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE customer_addresses (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  label VARCHAR(80) NOT NULL DEFAULT 'Home',
  recipient_name VARCHAR(190) NOT NULL,
  phone VARCHAR(40) NOT NULL,
  address_line_1 VARCHAR(255) NOT NULL,
  address_line_2 VARCHAR(255) NULL,
  city VARCHAR(120) NOT NULL,
  district VARCHAR(120) NOT NULL,
  province VARCHAR(120) NULL,
  postal_code VARCHAR(20) NULL,
  is_default TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_customer_addresses_user (user_id),
  CONSTRAINT fk_customer_addresses_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE categories (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  parent_id BIGINT UNSIGNED NULL,
  name VARCHAR(160) NOT NULL,
  slug VARCHAR(190) NOT NULL UNIQUE,
  description TEXT NULL,
  image_path VARCHAR(255) NULL,
  sort_order INT UNSIGNED NOT NULL DEFAULT 0,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  meta_title VARCHAR(190) NULL,
  meta_description VARCHAR(255) NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_categories_parent FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE products (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  sku VARCHAR(80) NOT NULL UNIQUE,
  name VARCHAR(190) NOT NULL,
  slug VARCHAR(190) NOT NULL UNIQUE,
  short_description VARCHAR(255) NULL,
  description MEDIUMTEXT NULL,
  base_price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  sale_price DECIMAL(12,2) NULL,
  cost_price DECIMAL(12,2) NULL,
  stock_quantity INT NOT NULL DEFAULT 0,
  low_stock_threshold INT NOT NULL DEFAULT 5,
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  status ENUM('draft','active','inactive','archived') NOT NULL DEFAULT 'draft',
  meta_title VARCHAR(190) NULL,
  meta_description VARCHAR(255) NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_products_status (status),
  INDEX idx_products_featured (is_featured)
) ENGINE=InnoDB;

CREATE TABLE product_categories (
  product_id BIGINT UNSIGNED NOT NULL,
  category_id BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (product_id, category_id),
  CONSTRAINT fk_product_categories_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  CONSTRAINT fk_product_categories_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE occasions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(160) NOT NULL,
  slug VARCHAR(190) NOT NULL UNIQUE,
  description TEXT NULL,
  image_path VARCHAR(255) NULL,
  sort_order INT UNSIGNED NOT NULL DEFAULT 0,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE recipients (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(160) NOT NULL,
  slug VARCHAR(190) NOT NULL UNIQUE,
  description TEXT NULL,
  image_path VARCHAR(255) NULL,
  sort_order INT UNSIGNED NOT NULL DEFAULT 0,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE product_occasions (
  product_id BIGINT UNSIGNED NOT NULL,
  occasion_id BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (product_id, occasion_id),
  CONSTRAINT fk_product_occasions_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  CONSTRAINT fk_product_occasions_occasion FOREIGN KEY (occasion_id) REFERENCES occasions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE product_recipients (
  product_id BIGINT UNSIGNED NOT NULL,
  recipient_id BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (product_id, recipient_id),
  CONSTRAINT fk_product_recipients_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  CONSTRAINT fk_product_recipients_recipient FOREIGN KEY (recipient_id) REFERENCES recipients(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE product_images (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id BIGINT UNSIGNED NOT NULL,
  image_path VARCHAR(255) NOT NULL,
  alt_text VARCHAR(190) NULL,
  sort_order INT UNSIGNED NOT NULL DEFAULT 0,
  is_primary TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_product_images_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE product_variants (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(160) NOT NULL,
  sku VARCHAR(80) NOT NULL UNIQUE,
  color_name VARCHAR(120) NULL,
  color_hex CHAR(7) NULL,
  image_path VARCHAR(255) NULL,
  price_adjustment DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  stock_quantity INT NOT NULL DEFAULT 0,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_product_variants_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE product_options (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(160) NOT NULL,
  type ENUM('text','textarea','select','radio','checkbox','file') NOT NULL DEFAULT 'text',
  is_required TINYINT(1) NOT NULL DEFAULT 0,
  sort_order INT UNSIGNED NOT NULL DEFAULT 0,
  CONSTRAINT fk_product_options_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE product_option_values (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  option_id BIGINT UNSIGNED NOT NULL,
  label VARCHAR(160) NOT NULL,
  price_adjustment DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  sort_order INT UNSIGNED NOT NULL DEFAULT 0,
  CONSTRAINT fk_product_option_values_option FOREIGN KEY (option_id) REFERENCES product_options(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE delivery_zones (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(160) NOT NULL,
  district VARCHAR(120) NOT NULL,
  city VARCHAR(120) NULL,
  fee DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  minimum_order_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE delivery_time_slots (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  label VARCHAR(120) NOT NULL,
  start_time TIME NOT NULL,
  end_time TIME NOT NULL,
  capacity INT UNSIGNED NULL,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE coupons (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(80) NOT NULL UNIQUE,
  type ENUM('fixed','percentage') NOT NULL,
  value DECIMAL(12,2) NOT NULL,
  minimum_order_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  usage_limit INT UNSIGNED NULL,
  used_count INT UNSIGNED NOT NULL DEFAULT 0,
  starts_at TIMESTAMP NULL,
  expires_at TIMESTAMP NULL,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE carts (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NULL,
  session_id VARCHAR(128) NULL,
  status ENUM('active','converted','abandoned') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_carts_session (session_id),
  CONSTRAINT fk_carts_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE cart_items (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cart_id BIGINT UNSIGNED NOT NULL,
  product_id BIGINT UNSIGNED NOT NULL,
  variant_id BIGINT UNSIGNED NULL,
  quantity INT UNSIGNED NOT NULL DEFAULT 1,
  unit_price DECIMAL(12,2) NOT NULL,
  gift_message TEXT NULL,
  custom_options_json JSON NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_cart_items_cart FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
  CONSTRAINT fk_cart_items_product FOREIGN KEY (product_id) REFERENCES products(id),
  CONSTRAINT fk_cart_items_variant FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE orders (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_number VARCHAR(40) NOT NULL UNIQUE,
  user_id BIGINT UNSIGNED NULL,
  coupon_id BIGINT UNSIGNED NULL,
  delivery_time_slot_id BIGINT UNSIGNED NULL,
  customer_name VARCHAR(190) NOT NULL,
  customer_email VARCHAR(190) NOT NULL,
  customer_phone VARCHAR(40) NOT NULL,
  recipient_name VARCHAR(190) NOT NULL,
  recipient_phone VARCHAR(40) NOT NULL,
  delivery_address_line_1 VARCHAR(255) NOT NULL,
  delivery_address_line_2 VARCHAR(255) NULL,
  delivery_city VARCHAR(120) NOT NULL,
  delivery_district VARCHAR(120) NOT NULL,
  delivery_province VARCHAR(120) NULL,
  delivery_postal_code VARCHAR(20) NULL,
  delivery_date DATE NULL,
  subtotal DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  discount_total DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  delivery_fee DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  tax_total DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  grand_total DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  payment_status ENUM('pending','paid','failed','refunded','partially_refunded') NOT NULL DEFAULT 'pending',
  order_status ENUM('pending','confirmed','processing','ready','out_for_delivery','delivered','cancelled','refunded') NOT NULL DEFAULT 'pending',
  admin_notes TEXT NULL,
  customer_notes TEXT NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_orders_user (user_id),
  INDEX idx_orders_status (order_status),
  INDEX idx_orders_payment_status (payment_status),
  CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_orders_coupon FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE SET NULL,
  CONSTRAINT fk_orders_delivery_slot FOREIGN KEY (delivery_time_slot_id) REFERENCES delivery_time_slots(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE order_items (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id BIGINT UNSIGNED NOT NULL,
  product_id BIGINT UNSIGNED NULL,
  variant_id BIGINT UNSIGNED NULL,
  product_name VARCHAR(190) NOT NULL,
  sku VARCHAR(80) NULL,
  quantity INT UNSIGNED NOT NULL DEFAULT 1,
  unit_price DECIMAL(12,2) NOT NULL,
  cost_price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  total_price DECIMAL(12,2) NOT NULL,
  gift_message TEXT NULL,
  custom_options_json JSON NULL,
  CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
  CONSTRAINT fk_order_items_variant FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE order_status_history (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id BIGINT UNSIGNED NOT NULL,
  status VARCHAR(80) NOT NULL,
  note TEXT NULL,
  changed_by BIGINT UNSIGNED NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_order_status_history_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_order_status_history_user FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE deliveries (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id BIGINT UNSIGNED NOT NULL UNIQUE,
  assigned_staff_id BIGINT UNSIGNED NULL,
  courier_service_name VARCHAR(160) NULL,
  courier_tracking_number VARCHAR(120) NULL,
  parcel_reference_number VARCHAR(120) NULL,
  qr_code_path VARCHAR(255) NULL,
  delivery_charge DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  dispatch_date DATE NULL,
  expected_delivery_date DATE NULL,
  delivered_date DATE NULL,
  delivery_status ENUM('pending','assigned','dispatched','picked_up','out_for_delivery','delivered','failed','returned') NOT NULL DEFAULT 'pending',
  tracking_code VARCHAR(80) NULL UNIQUE,
  delivered_at TIMESTAMP NULL,
  proof_image VARCHAR(255) NULL,
  notes TEXT NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_deliveries_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_deliveries_staff FOREIGN KEY (assigned_staff_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE delivery_status_history (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  delivery_id BIGINT UNSIGNED NOT NULL,
  order_id BIGINT UNSIGNED NOT NULL,
  status VARCHAR(80) NOT NULL,
  note TEXT NULL,
  changed_by BIGINT UNSIGNED NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_delivery_status_history_delivery (delivery_id),
  INDEX idx_delivery_status_history_order (order_id),
  CONSTRAINT fk_delivery_status_history_delivery FOREIGN KEY (delivery_id) REFERENCES deliveries(id) ON DELETE CASCADE,
  CONSTRAINT fk_delivery_status_history_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_delivery_status_history_user FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE delivery_reminders (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  delivery_id BIGINT UNSIGNED NOT NULL,
  order_id BIGINT UNSIGNED NOT NULL,
  admin_id BIGINT UNSIGNED NULL,
  reminder_at DATETIME NOT NULL,
  message VARCHAR(255) NOT NULL,
  status ENUM('open','completed','cancelled') NOT NULL DEFAULT 'open',
  completed_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_delivery_reminders_status_date (status, reminder_at),
  CONSTRAINT fk_delivery_reminders_delivery FOREIGN KEY (delivery_id) REFERENCES deliveries(id) ON DELETE CASCADE,
  CONSTRAINT fk_delivery_reminders_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_delivery_reminders_admin FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE payments (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id BIGINT UNSIGNED NOT NULL,
  provider VARCHAR(80) NOT NULL,
  method VARCHAR(80) NOT NULL,
  transaction_reference VARCHAR(190) NULL UNIQUE,
  amount DECIMAL(12,2) NOT NULL,
  currency CHAR(3) NOT NULL DEFAULT 'LKR',
  status ENUM('pending','authorized','paid','failed','refunded','cancelled') NOT NULL DEFAULT 'pending',
  receipt_path VARCHAR(255) NULL,
  verified_by BIGINT UNSIGNED NULL,
  verified_at TIMESTAMP NULL,
  verification_notes TEXT NULL,
  paid_at TIMESTAMP NULL,
  raw_response_json JSON NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_payments_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_payments_verified_by FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE banners (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(190) NOT NULL,
  subtitle VARCHAR(255) NULL,
  image_path VARCHAR(255) NOT NULL,
  link_url VARCHAR(255) NULL,
  placement VARCHAR(80) NOT NULL DEFAULT 'home_hero',
  sort_order INT UNSIGNED NOT NULL DEFAULT 0,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE pages (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(190) NOT NULL,
  slug VARCHAR(190) NOT NULL UNIQUE,
  content MEDIUMTEXT NOT NULL,
  meta_title VARCHAR(190) NULL,
  meta_description VARCHAR(255) NULL,
  status ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE faqs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  question VARCHAR(255) NOT NULL,
  answer TEXT NOT NULL,
  category VARCHAR(120) NULL,
  sort_order INT UNSIGNED NOT NULL DEFAULT 0,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE contact_messages (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(190) NOT NULL,
  email VARCHAR(190) NOT NULL,
  phone VARCHAR(40) NULL,
  subject VARCHAR(190) NULL,
  message TEXT NOT NULL,
  status ENUM('new','read','replied','archived') NOT NULL DEFAULT 'new',
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE custom_gift_requests (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NULL,
  request_number VARCHAR(40) NOT NULL UNIQUE,
  customer_name VARCHAR(190) NOT NULL,
  customer_email VARCHAR(190) NOT NULL,
  customer_phone VARCHAR(40) NULL,
  occasion VARCHAR(160) NULL,
  recipient VARCHAR(160) NULL,
  budget DECIMAL(12,2) NULL,
  delivery_date DATE NULL,
  message TEXT NOT NULL,
  status ENUM('new','reviewing','quoted','approved','rejected','completed','cancelled') NOT NULL DEFAULT 'new',
  admin_notes TEXT NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_custom_gift_requests_user (user_id),
  INDEX idx_custom_gift_requests_status (status),
  CONSTRAINT fk_custom_gift_requests_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE custom_gift_request_images (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  request_id BIGINT UNSIGNED NOT NULL,
  image_path VARCHAR(255) NOT NULL,
  original_name VARCHAR(190) NULL,
  mime_type VARCHAR(120) NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_custom_gift_request_images_request FOREIGN KEY (request_id) REFERENCES custom_gift_requests(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE message_replies (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  entity_type ENUM('contact','gift_request') NOT NULL,
  entity_id BIGINT UNSIGNED NOT NULL,
  sender_type ENUM('admin','customer','guest') NOT NULL,
  sender_user_id BIGINT UNSIGNED NULL,
  sender_name VARCHAR(190) NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_message_replies_entity (entity_type, entity_id),
  CONSTRAINT fk_message_replies_user FOREIGN KEY (sender_user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE admin_notifications (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  type VARCHAR(80) NOT NULL,
  title VARCHAR(190) NOT NULL,
  message VARCHAR(255) NOT NULL,
  entity_type VARCHAR(80) NULL,
  entity_id BIGINT UNSIGNED NULL,
  status ENUM('unread','read','archived') NOT NULL DEFAULT 'unread',
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  read_at TIMESTAMP NULL,
  INDEX idx_admin_notifications_status (status),
  INDEX idx_admin_notifications_entity (entity_type, entity_id)
) ENGINE=InnoDB;

CREATE TABLE expenses (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  admin_id BIGINT UNSIGNED NULL,
  order_id BIGINT UNSIGNED NULL,
  paid_source VARCHAR(30) NOT NULL DEFAULT 'company_cash',
  paid_by_admin_id BIGINT UNSIGNED NULL,
  reimbursement_status VARCHAR(30) NULL,
  expense_number VARCHAR(40) NOT NULL UNIQUE,
  category VARCHAR(120) NOT NULL,
  title VARCHAR(190) NOT NULL,
  description TEXT NULL,
  amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  expense_date DATE NOT NULL,
  receipt_path VARCHAR(255) NULL,
  receipt_original_name VARCHAR(190) NULL,
  status ENUM('pending','approved','rejected','paid') NOT NULL DEFAULT 'approved',
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_expenses_admin (admin_id),
  INDEX idx_expenses_order (order_id),
  INDEX idx_expenses_paid_by_admin (paid_by_admin_id),
  INDEX idx_expenses_date_status (expense_date, status),
  CONSTRAINT fk_expenses_admin FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_expenses_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
  CONSTRAINT fk_expenses_paid_by_admin FOREIGN KEY (paid_by_admin_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE wishlists (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  product_id BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_wishlists_user_product (user_id, product_id),
  CONSTRAINT fk_wishlists_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_wishlists_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE reviews (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NULL,
  order_id BIGINT UNSIGNED NULL,
  rating TINYINT UNSIGNED NOT NULL,
  title VARCHAR(190) NULL,
  body TEXT NULL,
  status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT chk_reviews_rating CHECK (rating BETWEEN 1 AND 5),
  CONSTRAINT fk_reviews_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  CONSTRAINT fk_reviews_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_reviews_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE gift_reminders (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  recipient_name VARCHAR(190) NOT NULL,
  occasion VARCHAR(120) NOT NULL,
  reminder_date DATE NOT NULL,
  notes TEXT NULL,
  notification_method ENUM('email','sms','whatsapp') NOT NULL DEFAULT 'email',
  status ENUM('active','paused','sent','cancelled') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_gift_reminders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE newsletter_subscribers (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(190) NOT NULL UNIQUE,
  status ENUM('subscribed','unsubscribed') NOT NULL DEFAULT 'subscribed',
  subscribed_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  unsubscribed_at TIMESTAMP NULL
) ENGINE=InnoDB;

CREATE TABLE uploads (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  disk VARCHAR(80) NOT NULL DEFAULT 'local',
  path VARCHAR(255) NOT NULL,
  original_name VARCHAR(190) NOT NULL,
  mime_type VARCHAR(120) NOT NULL,
  size_bytes BIGINT UNSIGNED NOT NULL,
  uploaded_by BIGINT UNSIGNED NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_uploads_user FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE settings (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(120) NOT NULL UNIQUE,
  setting_value TEXT NULL,
  setting_group VARCHAR(80) NOT NULL DEFAULT 'general',
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE activity_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NULL,
  action VARCHAR(120) NOT NULL,
  entity_type VARCHAR(120) NULL,
  entity_id BIGINT UNSIGNED NULL,
  metadata_json JSON NULL,
  ip_address VARCHAR(45) NULL,
  user_agent VARCHAR(255) NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_activity_logs_user (user_id),
  INDEX idx_activity_logs_entity (entity_type, entity_id),
  CONSTRAINT fk_activity_logs_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO roles (id, name, slug) VALUES
  (1, 'Super Admin', 'super_admin'),
  (2, 'Admin', 'admin'),
  (3, 'Manager', 'manager'),
  (4, 'Staff', 'staff'),
  (5, 'Customer', 'customer');

INSERT INTO users (
  id, role_id, first_name, last_name, email, phone, password_hash, status, email_verified_at
) VALUES (
  1,
  1,
  'GiftVibe',
  'Admin',
  'admin@giftvibe.lk',
  '+94770000000',
  '$2y$10$y4bC0I/ZGVM8/qfR4i0zNOFuc66kDIadGxjYSHZyO8OmOQN0VR7M2',
  'active',
  NOW()
);

INSERT INTO settings (setting_key, setting_value, setting_group) VALUES
  ('site.name', 'Gift Vibe LK', 'general'),
  ('site.currency', 'LKR', 'commerce'),
  ('site.timezone', 'Asia/Colombo', 'general'),
  ('brand.primary', '#0C2B4E', 'theme'),
  ('brand.secondary', '#1A3D64', 'theme'),
  ('brand.dark', '#1D546C', 'theme'),
  ('brand.light', '#F4F4F4', 'theme'),
  ('uploads.max_size_mb', '5', 'uploads'),
  ('orders.default_payment_status', 'pending', 'orders'),
  ('orders.default_order_status', 'pending', 'orders');

INSERT INTO delivery_time_slots (label, start_time, end_time, capacity, status) VALUES
  ('Morning Delivery', '09:00:00', '12:00:00', 25, 'active'),
  ('Afternoon Delivery', '12:00:00', '16:00:00', 25, 'active'),
  ('Evening Delivery', '16:00:00', '20:00:00', 20, 'active');

INSERT INTO categories (id, name, slug, description, image_path, sort_order, status, meta_title, meta_description) VALUES
  (1, 'Birthday Gifts', 'birthday-gifts', 'Bright gift boxes, flowers, and treats for birthdays across Sri Lanka.', 'public/assets/images/categories/birthday-gifts.png', 1, 'active', 'Birthday Gifts Sri Lanka', 'Shop birthday gifts with GiftVibe.lk delivery.'),
  (2, 'Luxury Hampers', 'luxury-hampers', 'Premium hampers with chocolates, florals, keepsakes, and handwritten notes.', 'public/assets/images/categories/luxury-hampers.png', 2, 'active', 'Luxury Hampers Sri Lanka', 'Premium gift hampers delivered by GiftVibe.lk.'),
  (3, 'Flowers & Chocolates', 'flowers-chocolates', 'Classic flowers and chocolate pairings for thoughtful same-week gifting.', 'public/assets/images/categories/flowers-chocolates.png', 3, 'active', 'Flowers and Chocolates Sri Lanka', 'Order flowers and chocolates from GiftVibe.lk.');

INSERT INTO products (id, sku, name, slug, short_description, description, base_price, sale_price, cost_price, stock_quantity, low_stock_threshold, is_featured, status, meta_title, meta_description) VALUES
  (1, 'GV-BDAY-2026', 'Birthday Glow Gift Box', 'birthday-glow-gift-box', 'A polished birthday box with florals, sweets, and a personal note.', 'Designed for warm birthday surprises with a premium unboxing moment.', 8500.00, 7900.00, 4700.00, 18, 5, 1, 'active', 'Birthday Glow Gift Box', 'Send a premium birthday gift box in Sri Lanka.'),
  (2, 'GV-LUX-2026', 'Luxe Celebration Hamper', 'luxe-celebration-hamper', 'A premium hamper for anniversaries, promotions, and milestone moments.', 'A modern luxury hamper arranged with treats, keepsakes, and elegant wrapping.', 14500.00, NULL, 8600.00, 9, 4, 1, 'active', 'Luxe Celebration Hamper', 'Luxury celebration hamper from GiftVibe.lk.'),
  (3, 'GV-FLOW-2026', 'Rose & Chocolate Moment', 'rose-chocolate-moment', 'Fresh roses paired with chocolates for a classic romantic gesture.', 'A dependable flowers-and-chocolates gift for meaningful delivery moments.', 6200.00, 5900.00, 3400.00, 22, 6, 0, 'active', 'Rose and Chocolate Gift', 'Order roses and chocolates in Sri Lanka.');

INSERT INTO product_categories (product_id, category_id) VALUES
  (1, 1),
  (2, 2),
  (3, 3);

INSERT INTO product_images (product_id, image_path, alt_text, sort_order, is_primary) VALUES
  (1, 'public/assets/images/hero_gift_box.jpg', 'Birthday Glow Gift Box with flowers and sweets', 1, 1),
  (2, 'public/assets/images/hero_gift_box.jpg', 'Luxury hamper gift box arranged for delivery', 1, 1),
  (3, 'public/assets/images/hero_gift_box.jpg', 'Rose and chocolate gift arrangement', 1, 1);
