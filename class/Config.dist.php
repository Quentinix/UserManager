<?php

namespace Wave;

/**
 * Classe de la configuration
 *
 * @package  Wave
 * @author   Quentinix <git@quentinix.fr>
 */
class Config
{
    // phpcs:disable PEAR.Commenting
    private $configSqlHost = "127.0.0.1";
    private $configSqlUser = "root";
    private $configSqlPass = "";
    private $configSqlDb = "wave_dev";
    private $configSqlTableUser = "um_user";
    private $configSqlTableSession = "um_session";
    private $configSqlTableRecovery = "um_recovery";
    private $configSqlTablePermlabel = "um_permlabel";
    private $configSessionSelect = 3;
    private $configSessionExpire = 86400;
    private $configRecoveryExpire = 900;
    private $configRecoveryRetry = 3600;
    private $configMaxTry = 5;

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

    public function getConfigSessionSelect()
    {
        return $this->configSessionSelect;
    }

    public function getConfigSessionExpire()
    {
        return $this->configSessionExpire;
    }

    public function getConfigRecoveryExpire()
    {
        return $this->configRecoveryExpire;
    }

    public function getConfigRecoveryRetry()
    {
        return $this->configRecoveryRetry;
    }

    public function getConfigSqlTableRecovery()
    {
        return $this->configSqlTableRecovery;
    }

    public function getConfigMaxTry()
    {
        return $this->configMaxTry;
    }
}
