--
-- Table structure for table `used_coupons`
--

CREATE TABLE IF NOT EXISTS `used_coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT 'Links to users table',
  `session_id` varchar(255) DEFAULT NULL COMMENT 'For guest users',
  `coupon_code` varchar(255) NOT NULL,
  `used_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_coupon` (`user_id`,`coupon_code`),
  UNIQUE KEY `session_coupon` (`session_id`,`coupon_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
