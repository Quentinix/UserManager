<?php
// phpcs:disable Generic.Files.LineLength

namespace Wave;

use Exception;

/**
 * Classe de la connexion à la base de données
 *
 * @package  Wave
 * @author   Quentinix <git@quentinix.fr>
 */
class Sql extends Config
{
    private $sqlConnect;

    /**
     * Connexion à la base de données lors de l'appel de la class
     */
    public function __construct()
    {
        // if (@file_exists(".configOK") == false and @file_exists("vendor/quentinix/wave/.configOK") == false) {
        //     throw new Exception("La configuration de Wave n'est pas appliquée.");
        // } Solution reportée !
        $this->sqlConnect = mysqli_connect($this->getConfigSqlHost(), $this->getConfigSqlUser(), $this->getConfigSqlPass(), $this->getConfigSqlDb());
        if (mysqli_errno($this->sqlConnect)) {
            throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
        }
    }

    /**
     * Appel de la connexion SQL
     */
    public function GetSqlConnect()
    {
        return $this->sqlConnect;
    }
}
