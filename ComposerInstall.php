<?php

namespace UserManager;

use Composer\Script\Event;
use phpDocumentor\Reflection\Types\Null_;

class ComposerInstall {

	public static function postInstallCmd(Event $event) {
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
		$seed = "";
		if (@file_exists(".configOK") == true) {
			$questionFail = true;
			while ($questionFail == true) {
				$questionFichierSeed = $event->GetIO()->ask("Une graine de génération aléatoire à été trouvée, la reprendre ?(OUI / non) : ");
				if ($questionFichierSeed === null)
					$questionFichierSeed = "oui";
				if ($questionFichierSeed == "oui") {
					$fichierConfigOKSeed = file(".configOK");
					$seed = $fichierConfigOKSeed[0];
					$questionFail = false;
				} elseif ($questionFichierSeed == "non") {
					$questionFail = false;
				}
			}
		}
		while (! $seedValide) {
			if ($seed !== @$fichierConfigOKSeed[0])
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
					$seed = "";
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

CREATE DATABASE IF NOT EXISTS `" . $db . "` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `" . $db . "`;

-- --------------------------------------------------------

--
-- Structure de la table `um_recovery`
--

DROP TABLE IF EXISTS `um_recovery`;
CREATE TABLE IF NOT EXISTS `um_recovery` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `token` varchar(100) NOT NULL,
  `user_id` int(255) NOT NULL,
  `expire` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `um_session`
--

DROP TABLE IF EXISTS `um_session`;
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

DROP TABLE IF EXISTS `um_user`;
CREATE TABLE IF NOT EXISTS `um_user` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `user` varchar(100) NOT NULL,
  `pass` longtext NOT NULL,
  `permission` int(255) NOT NULL DEFAULT 0,
  `email` varchar(100) DEFAULT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `adresse` longtext DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `code_postal` varchar(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `um_recovery`
--
ALTER TABLE `um_recovery`
  ADD CONSTRAINT `um_recovery_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `um_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `um_session`
--
ALTER TABLE `um_session`
  ADD CONSTRAINT `um_session_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `um_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

		");
		echo " OK !\r\n";
		echo "Ecriture des fichiers de configurations...";
		$fichierConfig = file("Config.php");
		$fichierConfig[6] = '	private $configSqlHost = "' . $host . '";' . "\r\n";
		$fichierConfig[7] = '	private $configSqlUser = "' . $user . '";' . "\r\n";
		$fichierConfig[8] = '	private $configSqlPass = "' . $pass . '";' . "\r\n";
		$fichierConfig[9] = '	private $configSqlDb = "' . $db . '";' . "\r\n";
		$fichierConfig[13] = '	private $configSessionExpire = ' . $sessionExpire . ';' . "\r\n";
		$fichierConfig[14] = '	private $configRecoveryExpire = ' . $recoveryExpire . ';' . "\r\n";
		$fichierConfig[15] = '	private $configSeed = "' . $seed . '";' . "\r\n";
		file_put_contents('Config.php', implode('', $fichierConfig));
		$fichierConfigOK = array();
		$fichierConfigOK[0] = $seed;
		file_put_contents('.configOK', implode('', $fichierConfigOK));
		echo " OK !\r\n";
	}
}
