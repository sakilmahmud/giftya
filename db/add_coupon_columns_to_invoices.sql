--
-- Add coupon columns back to `invoices` table
--

ALTER TABLE `invoices`
ADD COLUMN `coupon_code` VARCHAR(255) NULL AFTER `total_amount`,
ADD COLUMN `coupon_discount` DECIMAL(10,2) NULL AFTER `coupon_code`;
