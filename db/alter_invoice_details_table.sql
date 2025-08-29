--
-- Alter table `invoice_details` to add custom_message and photo_urls
--

ALTER TABLE `invoice_details`
ADD COLUMN `custom_message` TEXT NULL AFTER `price`,
ADD COLUMN `photo_urls` TEXT NULL AFTER `custom_message`;
