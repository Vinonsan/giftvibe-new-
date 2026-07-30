USE `dbof-giftvibelk`;

SET @sql = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE order_items ADD COLUMN cost_price DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER unit_price',
    'SELECT "order_items.cost_price exists"'
  )
  FROM information_schema.columns
  WHERE table_schema = 'dbof-giftvibelk'
    AND table_name = 'order_items'
    AND column_name = 'cost_price'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE payments ADD COLUMN receipt_path VARCHAR(255) NULL AFTER status',
    'SELECT "payments.receipt_path exists"'
  )
  FROM information_schema.columns
  WHERE table_schema = 'dbof-giftvibelk'
    AND table_name = 'payments'
    AND column_name = 'receipt_path'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE payments ADD COLUMN verified_by BIGINT UNSIGNED NULL AFTER receipt_path',
    'SELECT "payments.verified_by exists"'
  )
  FROM information_schema.columns
  WHERE table_schema = 'dbof-giftvibelk'
    AND table_name = 'payments'
    AND column_name = 'verified_by'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE payments ADD COLUMN verified_at TIMESTAMP NULL AFTER verified_by',
    'SELECT "payments.verified_at exists"'
  )
  FROM information_schema.columns
  WHERE table_schema = 'dbof-giftvibelk'
    AND table_name = 'payments'
    AND column_name = 'verified_at'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE payments ADD COLUMN verification_notes TEXT NULL AFTER verified_at',
    'SELECT "payments.verification_notes exists"'
  )
  FROM information_schema.columns
  WHERE table_schema = 'dbof-giftvibelk'
    AND table_name = 'payments'
    AND column_name = 'verification_notes'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
