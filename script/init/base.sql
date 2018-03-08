CREATE DATABASE IF NOT EXISTS `ccv2_db` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

USE `ccv2_db`;

CREATE TABLE IF NOT EXISTS `global_var` (
  `name` varchar(20) COLLATE utf8mb4_unicode_ci NULL,
  `val` varchar(2048) COLLATE utf8mb4_unicode_ci NULL,
  `expire_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`name`))
ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_unicode_ci;