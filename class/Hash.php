<?php
// phpcs:disable Generic.Files.LineLength

namespace Wave;

use Exception;

/**
 * Classe Hash
 *
 * @package  Wave
 * @author   Quentinix <git@quentinix.fr>
 */
class Hash extends Config
{
    /**
     * Permet le hashage d'une chaine de caractères et plus particulèrement du
     * mot de passe lors de la création ou de la modification du compte.
     *
     * @param string $mdp
     *
     * @throws Exception
     * @return string
     */
    public function hashCreate($mdp)
    {
        if ($mdp == "") {
            throw new Exception("Mdp n'est pas renseignée.");
        }
        $seedRand = "";
        for ($i = 1; $i <= 128; $i++) {
            $seedRand .= mt_rand(0, 9);
        }
        $mdpHash = $seedRand . hexdec(hash("sha256", $mdp . $seedRand));
        return $mdpHash;
    }

    /**
     * Permet la vérification entre le hashage et le mot de passe renseigné lors
     * de la connexion
     *
     * @param string $mdp
     * @param string $mdpVerif
     *
     * @throws Exception
     * @return boolean
     */
    public function hashVerif($mdp, $mdpVerif)
    {
        if ($mdp == "") {
            throw new Exception("Mdp n'est pas renseignée.");
        }
        if ($mdpVerif == "") {
            throw new Exception("MdpVerif n'est pas renseignée.");
        }
        $seedVerif = substr($mdpVerif, 0, 128);
        $mdpHash = $seedVerif . hexdec(hash("sha256", $mdp . $seedverif));
        if ($mdpVerif === $mdpHash) {
            return true;
        }
        return false;
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
