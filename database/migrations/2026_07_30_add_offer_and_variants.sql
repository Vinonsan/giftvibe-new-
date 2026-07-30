-- Add offer/discount fields to products table
ALTER TABLE products
  ADD COLUMN offer_type ENUM('none','fixed','percentage') NOT NULL DEFAULT 'none' AFTER cost_price,
  ADD COLUMN offer_value DECIMAL(12,2) NULL AFTER offer_type,
  ADD COLUMN unit VARCHAR(80) NULL AFTER offer_value;
