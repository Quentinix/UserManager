<?php
// phpcs:disable Generic.Files.LineLength

namespace Wave;

use Exception;

/**
 * Classe de la gestion des permissions d'adresses IP
 *
 * @package  Wave
 * @author   Quentinix <git@quentinix.fr>
 */
class IpAccess extends Config
{

    private $sqlConnect;
    
    /**
     * Appel de la connexion à la base de données
     */
    public function __construct()
    {
        $sqlClass = new Sql;
        $this->sqlConnect = $sqlClass->getSqlConnect();
    }

    /**
     * Permet l'ajout d'une adresse IP autorisée sur un compte utilisateur
     * Retourne true si l'adresse IP est bien ajoutée au compte utilisateur
     * Retourne false si le compte utilisateur n'existe pas ou si l'adresse IP n'est pas correcte
     * 
     * @param String $user
     * @param String $ip
     */
    public function addIpAccess($user, $ip) // TODO: regex sur l'ip pour vérification
    {
        $sqlResult = mysqli_query($this->sqlConnect, "SELECT ip_access FROM " . $this->getConfigSqlTableUser . " WHERE `user` LIKE '" . $user . "' LIMIT 1");
        if (mysqli_errno($this->sqlConnect)) {
            throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
        }
        while ($sqlRow = mysqli_fetch_array($sqlResult)) {
            $ipList = json_decode($sqlRow["ip_access"]);
        }
        if (! isset($iplist)) {
            return false;
        }
        $ipList[] = $ip;
        $ipList = json_encode($ipList);
        mysqli_query($this->sqlConnect, "UPDATE `" . $this->getConfigSqlTableUser . "` SET `ip_access` = '" . $ipList . "' WHERE `user` = " . $user);
        if (mysqli_errno($this->sqlConnect)) {
            throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
        }
        return true;
    }

    /**
     * Permet la suppression d'une adresse IP d'un compte utilisateur
     * Retourne true si l'adresse IP est bien supprimée du compte utilisateur
     * Retourne false si le compte utilisateur n'existe pas
     * 
     * @param String $user
     * @param String $ip
     */
    public function removeIpAccess($user, $ip)
    {
        $sqlResult = mysqli_query($this->sqlConnect, "SELECT ip_access FROM " . $this->getConfigSqlTableUser . " WHERE `user` LIKE '" . $user . "' LIMIT 1");
        if (mysqli_errno($this->sqlConnect)) {
            throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
        }
        while ($sqlRow = mysqli_fetch_array($sqlResult)) {
            $ipList = json_decode($sqlRow["ip_access"]);
        }
        $ipKey = array_search($ip, $ipList);
        if ($ipKey !== false) {
            unset($iplist[$ipKey]);
            $ipList = json_encode($ipList);
            mysqli_query($this->sqlConnect, "UPDATE `" . $this->getConfigSqlTableUser . "` SET `ip_access` = '" . $ipList . "' WHERE `user` = " . $user);
            if (mysqli_errno($this->sqlConnect)) {
                throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
            }
            return true;
        }
        return false;
    }
}
