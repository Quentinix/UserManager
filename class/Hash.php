<?php
// phpcs:disable Generic.Files.LineLength

namespace Wave;

use Exception;

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
        $seed = explode("-", $this->getConfigSeed());
        $mdpHash = hash("sha256", $mdp);
        $seedRand = "";
        for ($i = 1; $i <= 128; $i++) {
            $seedRand .= mt_rand(0, 9);
        }
        $mdpHash = hash("sha256", $mdpHash . $seedRand);
        $mdpSplit = str_split($mdpHash);
        $this->hashEncodeAlpha(64, $seed, $mdpSplit);
        $mdpHash = $seedRand . implode("", $mdpSplit);
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
        $seed = explode("-", $this->getConfigSeed());
        $mdpHash = hash("sha256", $mdp);
        $seedRand = substr($mdpVerif, 0, 128);
        $mdpHash = hash("sha256", $mdpHash . $seedRand);
        $mdpSplit = str_split($mdpHash);
        $this->hashEncodeAlpha(64, $seed, $mdpSplit);
        $mdpHash = $seedRand . implode("", $mdpSplit);
        if ($mdpVerif === $mdpHash) {
            return true;
        }
            return false;
    }

    public function hashEncodeAlpha($number, $seed, &$mdpSplit)
    {
        for ($i = 0; $i < $number; $i++) {
            if ($mdpSplit[$i] == "a") {
                $mdpSplit[$i] = $seed[0];
            }
            if ($mdpSplit[$i] == "b") {
                $mdpSplit[$i] = $seed[1];
            }
            if ($mdpSplit[$i] == "c") {
                $mdpSplit[$i] = $seed[2];
            }
            if ($mdpSplit[$i] == "d") {
                $mdpSplit[$i] = $seed[3];
            }
            if ($mdpSplit[$i] == "e") {
                $mdpSplit[$i] = $seed[4];
            }
            if ($mdpSplit[$i] == "f") {
                $mdpSplit[$i] = $seed[5];
            }
        }
    }
}
