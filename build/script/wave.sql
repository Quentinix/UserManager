SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

DROP TABLE IF EXISTS `um_permlabel`;
CREATE TABLE IF NOT EXISTS `um_permlabel` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `level` int(255) NOT NULL,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `um_recovery`;
CREATE TABLE IF NOT EXISTS `um_recovery` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `token` varchar(100) NOT NULL,
  `user_id` int(255) NOT NULL,
  `expire` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `um_session`;
CREATE TABLE IF NOT EXISTS `um_session` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `user_id` int(255) NOT NULL,
  `session_id` varchar(100) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `expire` int(11) NOT NULL,
  `loginEver` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `um_user`;
CREATE TABLE IF NOT EXISTS `um_user` (
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

ALTER TABLE `um_recovery`
  ADD CONSTRAINT `um_recovery_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `um_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `um_session`
  ADD CONSTRAINT `um_session_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `um_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;