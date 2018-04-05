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
     * Retourne la version de la bibliothèque
     *
     * @return string
     */
    public function version()
    {
        if (file_exists("composer.json")) {
            $composerFile = file_get_contents("composer.json");
        } elseif (file_exists("vendor/quentinix/wave/composer.json")) {
            $composerFile = file_get_contents("vendor/quentinix/wave/composer.json");
        } else {
            return "composer.json introuvable !";
        }
        $composer = json_decode($composerFile);
        return $composer->version;
    }
}
