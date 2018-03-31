<?php
// phpcs:disable Generic.Files.LineLength

namespace ComposerConfig;

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
        $maxTry = $event->getIO()->ask("Nombre maximum de tentative de connexion(5) : ", 5);
        $sessionSelect = $event->getIO()->ask("L'utilisateur peut se connecter avec, 1:le nom d'utilisateur, 2:l'email 'puissance 2 prit en charge exemple : nom d'utilisateur et email: 1 + 2 = 3' (3) : ", 3);
        $sessionExpire = $event->getIO()->ask("La session du compte utilisateur expire en seconde(86400 '1 jour') : ", 86400);
        $recoveryExpire = $event->getIO()->ask("Le jeton de récupération du compte utilisateur expire en seconde(900 '15 minutes') : ", 900);
        $recoveryRetry = $event->getIO()->ask("L'utilisateur peut retenter la récupération du mot de passe en seconde(3600 '1 heure') : ", 3600);
        echo "Modification de la configuration de Config.php...";
        if (! file_exists("class/Config.php")) {
            copy("class/Config.dist.php", "class/Config.php");
        }
        $fichierConfig = file("class/Config.php");
        $fichierConfig[13] = '    private $configSqlHost = "' . $host . '";' . "\r\n";
        $fichierConfig[14] = '    private $configSqlUser = "' . $user . '";' . "\r\n";
        $fichierConfig[15] = '    private $configSqlPass = "' . $pass . '";' . "\r\n";
        $fichierConfig[16] = '    private $configSqlDb = "' . $db . '";' . "\r\n";
        $fichierConfig[21] = '    private $configSessionSelect = ' . $sessionSelect . ';' . "\r\n";
        $fichierConfig[22] = '    private $configSessionExpire = ' . $sessionExpire . ';' . "\r\n";
        $fichierConfig[23] = '    private $configRecoveryExpire = ' . $recoveryExpire . ';' . "\r\n";
        $fichierConfig[24] = '    private $configRecoveryRetry = ' . $recoveryRetry . ';' . "\r\n";
        $fichierConfig[25] = '    private $configMaxTry = "' . $maxTry . '";' . "\r\n";
        file_put_contents('class/Config.php', implode('', $fichierConfig));
        echo "Création des tables de la base de données...";
        $sqlConnect = mysqli_connect($host, $user, $pass, $db, $port);
        $sqlFile = file_get_contents("build/script/wave.sql");
        $sqlFile = str_replace(self::sqlReplaceConfig("search"), self::sqlReplaceConfig("replace"), $sqlFile);
        mysqli_multi_query($sqlConnect, $sqlFile);
        echo " OK !\r\n";
    }

    public static function travisConfig()
    {
        echo "Execution TravisConfig...\r\n";
        echo "Lecture Config.php...\r\n";
        if (! file_exists("class/Config.php")) {
            copy("class/Config.dist.php", "class/Config.php");
        }
        $fichierConfig = file("class/Config.php");
        echo "Modification de la configuration de Config.php...\r\n";
        $fichierConfig[13] = '    private $configSqlHost = "127.0.0.1";' . "\r\n";
        $fichierConfig[14] = '    private $configSqlUser = "root";' . "\r\n";
        $fichierConfig[15] = '    private $configSqlPass = "";' . "\r\n";
        $fichierConfig[16] = '    private $configSqlDb = "wave_travis";' . "\r\n";
        $fichierConfig[21] = '    private $configSessionSelect = 3;' . "\r\n";
        $fichierConfig[22] = '    private $configSessionExpire = 86400;' . "\r\n";
        $fichierConfig[23] = '    private $configRecoveryExpire = 900;' . "\r\n";
        $fichierConfig[24] = '    private $configRecoveryRetry = 3600' . "\r\n";
        $fichierConfig[25] = '    private $configMaxTry = 5;' . "\r\n";
        echo "Réécriture de la configuration de Config.php...\r\n";
        file_put_contents('class/Config.php', implode('', $fichierConfig));
        echo "Création des tables de la base de données...";
        $sqlConnect = mysqli_connect("localhost", "root", "", "wave_ci");
        $sqlFile = file_get_contents("build/script/wave.sql");
        $sqlFile = str_replace(self::sqlReplaceConfig("search"), self::sqlReplaceConfig("replace"), $sqlFile);
        mysqli_multi_query($sqlConnect, $sqlFile);
        echo " OK !\r\n";
        echo "Execution terminée !\r\n";
    }

    private static function sqlReplaceConfig($option)
    {
        if ($option == "search") {
            return [
                "[[sqlTableUser]]",
                "[[sqlTableSession]]",
                "[[sqlTableRecovery]]",
                "[[sqlTablePermLabel]]",
            ];
        } elseif ($option == "replace") {
            spl_autoload_register(function () {
                include "class/Config.php";
            });
            $config = new Config;
            return [
                $config->getConfigSqlTableUser,
                $config->getConfigSqlTableSession,
                $config->getConfigSqlTableRecovery,
                $config->getConfigSqlTablePermLabel,
            ];
        }
    }
}
