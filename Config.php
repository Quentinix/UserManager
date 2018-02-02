<?php

namespace UserManager;

/**
 * Class de la configuration de la class UserManager
 *
 * @package  UserManager
 * @author   Quentinix <git@quentinix.fr>
 */
class Config
{
    // phpcs:disable PEAR.Commenting
    private $configSqlHost = "127.0.0.1";
    private $configSqlUser = "root";
    private $configSqlPass = "";
    private $configSqlDb = "usermanager";
    private $configSqlTableUser = "um_user";
    private $configSqlTableSession = "um_session";
    private $configSqlTableRecovery = "um_recovery";
    private $configSqlTablePermlabel = "um_permlabel";
    private $configSessionExpire = 86400;
    private $configRecoveryExpire = 900;
    private $configSeed = "54987-90400-93605-34136-24507-68510";

    public function getConfigSqlHost()
    {
        return $this->configSqlHost;
    }

    public function getConfigSqlUser()
    {
        return $this->configSqlUser;
    }

    public function getConfigSqlPass()
    {
        return $this->configSqlPass;
    }

    public function getConfigSqlDb()
    {
        return $this->configSqlDb;
    }

    public function getConfigSqlTableUser()
    {
        return $this->configSqlTableUser;
    }

    public function getConfigSqlTableSession()
    {
        return $this->configSqlTableSession;
    }

    public function getConfigSqlTablePermlabel()
    {
        return $this->configSqlTablePermlabel;
    }

    public function getConfigSessionExpire()
    {
        return $this->configSessionExpire;
    }

    public function getConfigRecoveryExpire()
    {
        return $this->configRecoveryExpire;
    }

    public function getConfigSqlTableRecovery()
    {
        return $this->configSqlTableRecovery;
    }

    public function getConfigSeed()
    {
        return $this->configSeed;
    }
}
