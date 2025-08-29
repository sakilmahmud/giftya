--
-- Database schema alterations for checkout and address management
--

-- Table structure for table `customer_addresses`
CREATE TABLE IF NOT EXISTS `customer_addresses` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NULL COMMENT 'Links to users table if logged in',
  `session_id` VARCHAR(255) NULL COMMENT 'For guest users',
  `full_name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `address_line_1` VARCHAR(255) NOT NULL,
  `address_line_2` VARCHAR(255) NULL,
  `city` VARCHAR(100) NOT NULL,
  `state` VARCHAR(100) NOT NULL,
  `pincode` VARCHAR(10) NOT NULL,
  `is_default` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `session_id` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Alter table `invoices`
-- Drop existing columns related to customer details and shipping address
ALTER TABLE `invoices`
DROP COLUMN `customer_email`,
DROP COLUMN `customer_phone`,
DROP COLUMN `shipping_address`,
DROP COLUMN `shipping_pincode`,
DROP COLUMN `payment_method`,
DROP COLUMN `upi_transaction_id`;

-- Add `customer_address_id` to `invoices`
ALTER TABLE `invoices`
ADD COLUMN `customer_address_id` INT(11) NULL AFTER `customer_name`;

-- Add foreign key constraint (optional, but good practice)
-- ALTER TABLE `invoices`
-- ADD CONSTRAINT `fk_customer_address` FOREIGN KEY (`customer_address_id`) REFERENCES `customer_addresses`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Alter `order_status` default value in `invoices`
ALTER TABLE `invoices`
MODIFY COLUMN `order_status` ENUM('pending_payment', 'processing', 'completed', 'cancelled', 'failed') NOT NULL DEFAULT 'pending_payment';

-- Alter table `transactions`
-- Add `pg_transaction_id` column
ALTER TABLE `transactions`
ADD COLUMN `pg_transaction_id` VARCHAR(255) NULL AFTER `descriptions`;
