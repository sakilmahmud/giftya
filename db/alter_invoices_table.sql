--
-- Alter table `invoices` to add new columns for checkout process
--

ALTER TABLE `invoices`
ADD COLUMN `customer_email` VARCHAR(255) NULL AFTER `customer_name`,
ADD COLUMN `customer_phone` VARCHAR(20) NULL AFTER `customer_email`,
ADD COLUMN `shipping_address` TEXT NULL AFTER `customer_phone`,
ADD COLUMN `shipping_pincode` VARCHAR(10) NULL AFTER `shipping_address`,
ADD COLUMN `payment_method` VARCHAR(50) NULL AFTER `total_amount`,
ADD COLUMN `upi_transaction_id` VARCHAR(255) NULL AFTER `payment_method`,
ADD COLUMN `order_status` ENUM('pending_payment', 'processing', 'completed', 'cancelled', 'failed') NOT NULL DEFAULT 'pending_payment' AFTER `upi_transaction_id`;
