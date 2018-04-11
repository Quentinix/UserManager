<?php
// phpcs:disable Generic.Files.LineLength

namespace Wave;

use Exception;

/**
 * Classe de l'algorithme de hash
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
     * @param String $mdp
     *
     * @throws Exception
     * @return String
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
     * @param String $mdp
     * @param String $mdpVerif
     *
     * @throws Exception
     * @return Boolean
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
}
