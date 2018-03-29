<?php
// phpcs:disable Generic.Files.LineLength

namespace Wave;

use Exception;

/**
 * Classe de la gestion d'utilisateurs
 *
 * @package  Wave
 * @author   Quentinix <git@quentinix.fr>
 */
class Wave extends Config
{
    private $sqlConnect;

    /**
     * Déconnexion de la base de données
     */
    public function __destruct()
    {
        mysqli_close($this->sqlConnect);
    }

    /**
     * Retourne la version de la bibliothèque
     *
     * @return string
     */
    public function version()
    {
        if (file_exists("../composer.json")) {
            $composerFile = file_get_contents("../composer.json");
        } elseif (file_exists("vendor/quentinix/wave/composer.json")) {
            $composerFile = file_get_contents("vendor/quentinix/wave/composer.json");
        } else {
            return "composer.json introuvable !";
        }
        $composer = json_decode($composerFile);
        return $composer->version;
    }

    /**
     * Permet la création d'un mot de passe facile à retenir
     *
     * @return string
     */
    public function createMdp()
    {
        $lettreConsonne = array(
            "b", "c", "d", "f", "g", "h", "j", "k", "l", "m", "n", "p", "q", "r", "s", "t", "v", "w", "x", "z"
        );
        $lettreVoyelle = array(
            "a", "e", "i", "o", "u", "y"
        );
        $lettreSpecial = array(
            "&", "(", "-", "_", ")", "=", ",", ";", ":", "!", "$", "*"
        );
        $i = 0;
        for ($i = 0; $i < 4; $i++) {
            if ($i == 0) {
                $rand = array_rand($lettreConsonne);
                $return = strtoupper($lettreConsonne[$rand]);
                $rand = array_rand($lettreVoyelle);
                $return .= $lettreVoyelle[$rand];
            }
            if ($i == 1 || $i == 2) {
                $rand = array_rand($lettreConsonne);
                $return .= $lettreConsonne[$rand];
                $rand = array_rand($lettreVoyelle);
                $return .= $lettreVoyelle[$rand];
            }
            if ($i == 3) {
                $rand = array_rand($lettreSpecial);
                $return .= $lettreSpecial[$rand];
                $return .= rand(0, 9);
            }
            $i++;
        }
        return $return;
    }
}
