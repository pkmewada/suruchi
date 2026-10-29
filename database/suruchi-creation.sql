CREATE DATABASE IF NOT EXISTS `suruchi_creation`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `suruchi_creation`;

CREATE TABLE IF NOT EXISTS `users` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(100) NOT NULL,
  `email`         VARCHAR(150) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `home_sections` (
  `section_key` VARCHAR(50) NOT NULL,
  `content`     LONGTEXT NOT NULL,
  `updated_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`section_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `users` (`name`, `email`, `password_hash`) VALUES
('Admin', 'admin@suruchi.com', '$2y$10$jM2omINImuTuRkeY6xrPAOKzRz1zn5JUL7kCETIWEEu3./yEDoJ6a')
ON DUPLICATE KEY UPDATE `email` = `email`;
