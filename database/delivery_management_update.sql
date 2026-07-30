USE `dbof-giftvibelk`;

SET @sql = (
  SELECT IF(COUNT(*) = 0,
    'ALTER TABLE deliveries ADD COLUMN courier_service_name VARCHAR(160) NULL AFTER assigned_staff_id',
    'SELECT "deliveries.courier_service_name exists"')
  FROM information_schema.columns
  WHERE table_schema = 'dbof-giftvibelk' AND table_name = 'deliveries' AND column_name = 'courier_service_name'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(COUNT(*) = 0,
    'ALTER TABLE deliveries ADD COLUMN courier_tracking_number VARCHAR(120) NULL AFTER courier_service_name',
    'SELECT "deliveries.courier_tracking_number exists"')
  FROM information_schema.columns
  WHERE table_schema = 'dbof-giftvibelk' AND table_name = 'deliveries' AND column_name = 'courier_tracking_number'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(COUNT(*) = 0,
    'ALTER TABLE deliveries ADD COLUMN parcel_reference_number VARCHAR(120) NULL AFTER courier_tracking_number',
    'SELECT "deliveries.parcel_reference_number exists"')
  FROM information_schema.columns
  WHERE table_schema = 'dbof-giftvibelk' AND table_name = 'deliveries' AND column_name = 'parcel_reference_number'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(COUNT(*) = 0,
    'ALTER TABLE deliveries ADD COLUMN qr_code_path VARCHAR(255) NULL AFTER parcel_reference_number',
    'SELECT "deliveries.qr_code_path exists"')
  FROM information_schema.columns
  WHERE table_schema = 'dbof-giftvibelk' AND table_name = 'deliveries' AND column_name = 'qr_code_path'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(COUNT(*) = 0,
    'ALTER TABLE deliveries ADD COLUMN delivery_charge DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER qr_code_path',
    'SELECT "deliveries.delivery_charge exists"')
  FROM information_schema.columns
  WHERE table_schema = 'dbof-giftvibelk' AND table_name = 'deliveries' AND column_name = 'delivery_charge'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(COUNT(*) = 0,
    'ALTER TABLE deliveries ADD COLUMN dispatch_date DATE NULL AFTER delivery_charge',
    'SELECT "deliveries.dispatch_date exists"')
  FROM information_schema.columns
  WHERE table_schema = 'dbof-giftvibelk' AND table_name = 'deliveries' AND column_name = 'dispatch_date'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(COUNT(*) = 0,
    'ALTER TABLE deliveries ADD COLUMN expected_delivery_date DATE NULL AFTER dispatch_date',
    'SELECT "deliveries.expected_delivery_date exists"')
  FROM information_schema.columns
  WHERE table_schema = 'dbof-giftvibelk' AND table_name = 'deliveries' AND column_name = 'expected_delivery_date'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(COUNT(*) = 0,
    'ALTER TABLE deliveries ADD COLUMN delivered_date DATE NULL AFTER expected_delivery_date',
    'SELECT "deliveries.delivered_date exists"')
  FROM information_schema.columns
  WHERE table_schema = 'dbof-giftvibelk' AND table_name = 'deliveries' AND column_name = 'delivered_date'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

ALTER TABLE deliveries
  MODIFY delivery_status ENUM('pending','assigned','dispatched','picked_up','out_for_delivery','delivered','failed','returned') NOT NULL DEFAULT 'pending';

CREATE TABLE IF NOT EXISTS delivery_status_history (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS delivery_reminders (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
