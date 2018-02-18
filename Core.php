<?php
// phpcs:disable Generic.Files.LineLength

namespace Wave;

/**
 * Classe Core
 *
 * @package  Wave
 * @author   Quentinix <git@quentinix.fr>
 */
class Core
{
    public function __construct()
    {
        $configs = scandir("./config/");
        foreach ($configs as $config) {
            if ($config === ".." or $config === ".") {
                continue;
            }
            require_once "./config/" . $config;
        }
        $classs = scandir("./class/");
        foreach ($classs as $class) {
            if ($class === ".." or $class === ".") {
                continue;
            }
            require_once "./class/" . $class;
        }
    }
}
