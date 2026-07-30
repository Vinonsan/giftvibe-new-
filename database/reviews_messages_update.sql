USE `dbof-giftvibelk`;

CREATE TABLE IF NOT EXISTS custom_gift_requests (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS custom_gift_request_images (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  request_id BIGINT UNSIGNED NOT NULL,
  image_path VARCHAR(255) NOT NULL,
  original_name VARCHAR(190) NULL,
  mime_type VARCHAR(120) NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_custom_gift_request_images_request FOREIGN KEY (request_id) REFERENCES custom_gift_requests(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS message_replies (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admin_notifications (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
