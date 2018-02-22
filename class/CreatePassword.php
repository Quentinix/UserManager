<?php
// phpcs:disable Generic.Files.LineLength

namespace Wave;

use Exception;

/**
 * Classe création des mots de passe
 *
 * @package  Wave
 * @author   Quentinix <git@quentinix.fr>
 */
class CreatePassword extends Config
{
    /**
     * Permet la création d'un mot de passe facile à retenir
     *
     * @return string
     */
    public function createPassword()
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
