<?php

namespace UserManager;

class Config {
	
	private $configSqlHost = "localhost";
	private $configSqlUser = "root";
	private $configSqlPass = "";
	private $configSqlDb = "usermanager_dev";
	private $configSqlTableUser = "um_user";
	private $configSqlTableSession = "um_session";
	private $configSqlTableRecovery = "um_recovery";
	private $configSessionExpire = 86400;
	private $configRecoveryExpire = 900;
	private $configSeed = "42068-40216-50795-54075-53207-42985";

	public function getConfigSqlHost() {
		return $this->configSqlHost;
	}

	public function getConfigSqlUser() {
		return $this->configSqlUser;
	}

	public function getConfigSqlPass() {
		return $this->configSqlPass;
	}

	public function getConfigSqlDb() {
		return $this->configSqlDb;
	}

	public function getConfigSqlTableUser() {
		return $this->configSqlTableUser;
	}

	public function getConfigSqlTableSession() {
		return $this->configSqlTableSession;
	}

	public function getConfigSessionExpire() {
		return $this->configSessionExpire;
	}

	public function getConfigRecoveryExpire() {
		return $this->configRecoveryExpire;
	}

	public function getConfigSqlTableRecovery() {
		return $this->configSqlTableRecovery;
	}

	public function getConfigSeed() {
		return $this->configSeed;
	}

}

?>
