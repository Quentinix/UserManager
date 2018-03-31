SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `[[sqlTablePermlabel]]` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `level` int(255) NOT NULL,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `[[sqlTableRecovery]]` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `token` varchar(100) NOT NULL,
  `user_id` int(255) NOT NULL,
  `expire` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `[[sqlTableSession]]` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `user_id` int(255) NOT NULL,
  `session_id` varchar(100) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `expire` int(11) NOT NULL,
  `loginEver` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `[[sqlTableUser]]` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `user` varchar(100) NOT NULL,
  `user_norm` varchar(255) NOT NULL,
  `pass` longtext NOT NULL,
  `permission` int(255) NOT NULL DEFAULT 0,
  `email` varchar(100) DEFAULT NULL,
  `ip_access` longtext NOT NULL DEFAULT '[]',
  `try` int(255) NOT NULL DEFAULT 0,
  `recovery_time` int(255) NOT NULL DEFAULT 0,
  `perso` longtext NOT NULL DEFAULT '[]',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `[[sqlTableRecovery]]`
  ADD CONSTRAINT `[[sqlTableRecovery]]_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `[[sqlTableUser]]` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `[[sqlTableSession]]`
  ADD CONSTRAINT `[[sqlTableSession]]_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `[[sqlTableUser]]` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;