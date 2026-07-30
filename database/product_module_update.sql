USE `dbof-giftvibelk`;

SET @sql = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE product_variants ADD COLUMN color_name VARCHAR(120) NULL AFTER sku',
    'SELECT "color_name exists"'
  )
  FROM information_schema.columns
  WHERE table_schema = 'dbof-giftvibelk'
    AND table_name = 'product_variants'
    AND column_name = 'color_name'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE product_variants ADD COLUMN color_hex CHAR(7) NULL AFTER color_name',
    'SELECT "color_hex exists"'
  )
  FROM information_schema.columns
  WHERE table_schema = 'dbof-giftvibelk'
    AND table_name = 'product_variants'
    AND column_name = 'color_hex'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE product_variants ADD COLUMN image_path VARCHAR(255) NULL AFTER color_hex',
    'SELECT "image_path exists"'
  )
  FROM information_schema.columns
  WHERE table_schema = 'dbof-giftvibelk'
    AND table_name = 'product_variants'
    AND column_name = 'image_path'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
