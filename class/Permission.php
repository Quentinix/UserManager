<?php
// phpcs:disable Generic.Files.LineLength

namespace Wave;

use Exception;

/**
 * Classe de la gestion des permissions
 *
 * @package  Wave
 * @author   Quentinix <git@quentinix.fr>
 */
class Permission extends Config
{
    /**
     * Permet l'ajout d'un label à un niveau de permission
     * Retourne false si un label existe déjà sur le niveau de permission choisi
     * Retourn true si le label est bien attribué au niveau de permission
     *
     * @param integer $level
     * @param string  $name
     *
     * @throws Exception
     * @return boolean
     */
    public function permissionAdd($level, $name)
    {
        $sqlResult = mysqli_query($this->sqlConnect, "SELECT * FROM `" . $this->getConfigSqlTablePermlabel() . "` WHERE `level` = '" . $level . "'");
        if (mysqli_errno($this->sqlConnect)) {
            throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
        }
        if (mysqli_fetch_array($sqlResult) != null) {
            return false;
        }
        mysqli_query($this->sqlConnect, "INSERT INTO `" . $this->getConfigSqlTablePermlabel() . "` (`id`, `level`, `name`) VALUES (NULL, '" . $level . "', '" . $name . "')");
        if (mysqli_errno($this->sqlConnect)) {
            throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
        }
        return true;
    }

    /**
     * Permet la suppression d'un label selon le niveau de permission
     * Retourne toujours true même si aucun label n'est attribué au niveau de permission
     *
     * @param integer $level
     *
     * @throws Exception
     * @return boolean
     */
    public function permissionRemove($level)
    {
        mysqli_query($this->sqlConnect, "DELETE FROM `" . $this->getConfigSqlTablePermlabel() . "` WHERE `level` = '" . $level . "'");
        if (mysqli_errno($this->sqlConnect)) {
            throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
        }
        return true;
    }

    /**
     * Permet de retourné le label selon le niveau de permission
     *
     * @param integer $level
     *
     * @throws Exception
     * @return string
     */
    public function permissionGet($level)
    {
        $sqlResult = mysqli_query($this->sqlConnect, "SELECT `name` FROM `" . $this->getConfigSqlTablePermlabel() . "` WHERE `level` = '" . $level . "'");
        if (mysqli_errno($this->sqlConnect)) {
            throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
        }
        while ($sqlRow = mysqli_fetch_array($sqlResult)) {
            return $sqlRow["name"];
        }
        return "";
    }
}
