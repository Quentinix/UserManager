<?php
// phpcs:disable Generic.Files.LineLength

namespace Wave;

use Composer\Script\Event;

/**
 * Class de l'installation de la class Wave
 *
 * @package  Wave
 * @author   Quentinix <git@quentinix.fr>
 */
class ComposerInstall
{
    // phpcs:disable PEAR.Commenting
    public static function config(Event $event)
    {
        $host = $event->getIO()->ask("IP MySQL(127.0.0.1) : ", "127.0.0.1");
        $user = $event->getIO()->ask("Nom d'utilisateur MySQL(root) : ", "root");
        $pass = $event->getIO()->ask("Mot de passe MySQL(NULL) : ");
        $db = $event->getIO()->ask("Base de données MySQL(wave) : ", "wave");
        $port = $event->getIO()->ask("Port MySQL(NULL) : ");
        $sessionExpire = $event->getIO()->ask("La session du compte utilisateur expire en seconde(86400 '1 jour') : ", 86400);
        $recoveryExpire = $event->getIO()->ask("Le jeton de récupération du compte utilisateur expire en seconde(900 '15 minutes') : ", 900);
        $seed = "";
        if (@file_exists(".configOK") or (@file_exists("../../../.configOK") and @file_exists("../../../composer.json"))) {
            $questionFichierSeed = $event->GetIO()->ask("Une graine de génération aléatoire à été trouvée, la reprendre ?(OUI / non) : ", "oui");
            if ($questionFichierSeed == "oui") {
                if (@file_exists(".configOK")) {
                    $fichierConfigOKSeed = file(".configOK");
                } else {
                    $fichierConfigOKSeed = file("../../../.configOK");
                }
                $seed = $fichierConfigOKSeed[0];
            }
        }
        while (true) {
            if ($seed !== @$fichierConfigOKSeed[0]) {
                $seed = $event->getIO()->ask("Graine de génération aléatoire pour mot de passe(exemple : 42068-40216-50795-54075-53207-42985, si vide : aléatoire) : ");
            }
            if ($seed === null) {
                $seed = self::generateSeed();
                break;
            } else {
                if (self::valideSeed($seed)) {
                    break;
                }
                $event->getIO()->write("Graine invalide !");
            }
        }
        echo "Création de la base de données et des tables...";
        $sqlConnect = mysqli_connect($host, $user, $pass, null, $port);
        mysqli_multi_query(
            $sqlConnect,
            "

SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = \"+00:00\";

CREATE DATABASE IF NOT EXISTS `" . $db . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `" . $db . "`;

CREATE TABLE IF NOT EXISTS `um_permlabel` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `level` int(255) NOT NULL,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `um_recovery` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `token` varchar(100) NOT NULL,
  `user_id` int(255) NOT NULL,
  `expire` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `um_session` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `user_id` int(255) NOT NULL,
  `session_id` varchar(100) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `expire` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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

ALTER TABLE `um_recovery`
  ADD CONSTRAINT `um_recovery_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `um_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `um_session`
  ADD CONSTRAINT `um_session_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `um_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

"
        );
        echo " OK !\r\n";
        echo "Ecriture des fichiers de configurations...";
        $fichierConfig = file("Config.php");
        $fichierConfig[13] = '    private $configSqlHost = "' . $host . '";' . "\r\n";
        $fichierConfig[14] = '    private $configSqlUser = "' . $user . '";' . "\r\n";
        $fichierConfig[15] = '    private $configSqlPass = "' . $pass . '";' . "\r\n";
        $fichierConfig[16] = '    private $configSqlDb = "' . $db . '";' . "\r\n";
        $fichierConfig[21] = '    private $configSessionExpire = ' . $sessionExpire . ';' . "\r\n";
        $fichierConfig[22] = '    private $configRecoveryExpire = ' . $recoveryExpire . ';' . "\r\n";
        $fichierConfig[23] = '    private $configSeed = "' . $seed . '";' . "\r\n";
        file_put_contents('Config.php', implode('', $fichierConfig));
        $fichierConfigOK = array();
        $fichierConfigOK[0] = $seed;
        file_put_contents('.configOK', implode('', $fichierConfigOK));
        if (file_exists("../../../composer.json")) {
            copy(".configOK", "../../../.configOK");
        }
        echo " OK !\r\n";
    }

    public function travisConfig()
    {
        echo "Execution TravisConfig...\r\n";
        echo "Lecture Config.php...\r\n";
        $fichierConfig = file("Config.php");
        echo "Modification de la configuration de Config.php...\r\n";
        $fichierConfig[13] = '    private $configSqlHost = "127.0.0.1";' . "\r\n";
        $fichierConfig[14] = '    private $configSqlUser = "root";' . "\r\n";
        $fichierConfig[15] = '    private $configSqlPass = "";' . "\r\n";
        $fichierConfig[16] = '    private $configSqlDb = "wave_travis";' . "\r\n";
        $fichierConfig[21] = '    private $configSessionExpire = 3600;' . "\r\n";
        $fichierConfig[22] = '    private $configRecoveryExpire = 3600;' . "\r\n";
        $fichierConfig[23] = '    private $configSeed = "54987-90400-93605-34136-24507-68510";' . "\r\n";
        echo "Réécriture de la configuration de Config.php...\r\n";
        file_put_contents('Config.php', implode('', $fichierConfig));
        echo "Sauvegarde de la graine...\r\n";
        $fichierConfigOK = array();
        $fichierConfigOK[0] = "54987-90400-93605-34136-24507-68510";
        file_put_contents('.configOK', implode('', $fichierConfigOK));
        echo " Execution terminée !\r\n";
    }

    public static function generateSeed()
    {
        $espaceSeed = false;
        $seed = "";
        for ($i = 1; $i <= 6; $i++) {
            if ($espaceSeed == false) {
                $espaceSeed = true;
            } else {
                $seed .= "-";
            }
            $seed .= mt_rand(10000, 99999);
        }
        return $seed;
    }

    public static function valideSeed($seed)
    {
        $seedVerif = explode("-", $seed);
        $verifCount = 0;
        for ($i = 0; $i < 7; $i++) {
            if (! isset($seedVerif[$i])) {
                break;
            }
            if ($seedVerif[$i] >= 10000 and $seedVerif[$i] <= 99999) {
                $verifCount++;
            }
        }
        if ($verifCount == 6) {
            return true;
        }
        return false;
    }
}
