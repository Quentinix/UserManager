<?php

namespace UserManager;

use Composer\Script\Event;

class ComposerInstall {

	public static function postPackageInstall(Event $event) {
		$event->getIO()->write("Configuration de User Manager.");
		$host = $event->getIO()->ask("IP MySQL(127.0.0.1) : ");
		if ($host === null)
			$host = "127.0.0.1";
		$user = $event->getIO()->ask("Nom d'utilisateur MySQL(root) : ");
		if ($user === null)
			$user = "root";
		$pass = $event->getIO()->ask("Mot de passe MySQL(NULL) : ");
		$db = $event->getIO()->ask("Base de données MySQL(usermanager) : ");
		if ($db === null)
			$db = "usermanager";
		$port = $event->getIO()->ask("Port MySQL(NULL) : ");
		$sessionExpire = $event->getIO()->ask("La session du compte utilisateur expire en seconde(86400 '1 jour') : ");
		if ($sessionExpire === null)
			$sessionExpire = "86400";
		$recoveryExpire = $event->getIO()->ask("Le jeton de récupération du compte utilisateur expire en seconde(900 '15 minutes') : ");
		if ($recoveryExpire === null)
			$recoveryExpire = "900";
		$seedValide = false;
		while (! $seedValide) {
			$seed = $event->getIO()->ask("Graine de génération aléatoire pour mot de passe(exemple : 42068-40216-50795-54075-53207-42985, si vide : aléatoire) : ");
			if ($seed === null) {
				$virguleSeed = false;
				for ($i = 1; $i <= 6; $i++) {
					if ($virguleSeed == false)
						$virguleSeed = true;
					else
						$seed .= "-";
					$seed .= mt_rand(10000, 99999);
				}
				$seedValide = true;
			} else {
				$seedVerif = explode("-", $seed);
				$verifCount = 0;
				for ($i = 0; $i < 7; $i++) {
					if (! isset($seedVerif[$i]))
						break;
					if ($seedVerif[$i] >= 10000 and $seedVerif[$i] <= 99999)
						$verifCount++;
				}
				if ($verifCount == 6)
					$seedValide = true;
				else {
					$event->getIO()->write("Graine invalide.");
				}
			}
		}
		$sqlConnect = mysqli_connect($host, $user, $pass, NULL, $port);
		echo "Création de la base de données et des tables...";
		mysqli_multi_query($sqlConnect, "

SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = \"+00:00\";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `usermanager_dev`
--
CREATE DATABASE IF NOT EXISTS `" . $db . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `" . $db . "`;

-- --------------------------------------------------------

--
-- Structure de la table `um_recovery`
--

CREATE TABLE IF NOT EXISTS `um_recovery` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `token` varchar(100) NOT NULL,
  `user_id` int(255) NOT NULL,
  `expire` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `um_session`
--

CREATE TABLE IF NOT EXISTS `um_session` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `user_id` int(255) NOT NULL,
  `session_id` varchar(100) NOT NULL,
  `expire` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `um_user`
--

CREATE TABLE IF NOT EXISTS `um_user` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `user` varchar(100) NOT NULL,
  `pass` longtext NOT NULL,
  `email` varchar(100) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `adresse` longtext NOT NULL,
  `ville` varchar(100) NOT NULL,
  `code_postal` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `um_recovery`
--
ALTER TABLE `um_recovery`
  ADD CONSTRAINT `um_recovery_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `um_user` (`id`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `um_session`
--
ALTER TABLE `um_session`
  ADD CONSTRAINT `um_session_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `um_user` (`id`) ON UPDATE CASCADE;
COMMIT;

		");
		echo " OK !\r\n";
		echo "Ecriture du fichier de configuration...";
		$fichierConfig = file("Config.php");
		$fichierConfig[6] = '	private $configSqlHost = "' . $host . '";' . "\r\n";
		$fichierConfig[7] = '	private $configSqlUser = "' . $user . '";' . "\r\n";
		$fichierConfig[8] = '	private $configSqlPass = "' . $pass . '";' . "\r\n";
		$fichierConfig[9] = '	private $configSqlDb = "' . $db . '";' . "\r\n";
		$fichierConfig[13] = '	private $configSessionExpire = ' . $sessionExpire . ';' . "\r\n";
		$fichierConfig[14] = '	private $configRecoveryExpire = ' . $recoveryExpire . ';' . "\r\n";
		$fichierConfig[15] = '	private $configSeed = "' . $seed . '";' . "\r\n";
		file_put_contents('Config.php', implode('', $fichierConfig));
		echo " OK !\r\n";
	}
}
