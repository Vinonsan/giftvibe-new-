USE `dbof-giftvibelk`;

CREATE TABLE IF NOT EXISTS expenses (
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
  CONSTRAINT fk_expenses_admin FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE expenses
  ADD COLUMN IF NOT EXISTS order_id BIGINT UNSIGNED NULL AFTER admin_id,
  ADD COLUMN IF NOT EXISTS paid_source VARCHAR(30) NOT NULL DEFAULT 'company_cash' AFTER order_id,
  ADD COLUMN IF NOT EXISTS paid_by_admin_id BIGINT UNSIGNED NULL AFTER paid_source,
  ADD COLUMN IF NOT EXISTS reimbursement_status VARCHAR(30) NULL AFTER paid_by_admin_id,
  ADD INDEX IF NOT EXISTS idx_expenses_order (order_id),
  ADD INDEX IF NOT EXISTS idx_expenses_paid_by_admin (paid_by_admin_id);
