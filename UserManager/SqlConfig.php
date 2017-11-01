<?php

namespace UserManager;

class SqlConfig {
	
	private $configSqlHost = "localhost";
	private $configSqlUser = "root";
	private $configSqlPass = "";
	private $configSqlDb = "usermanager_dev";
	private $configSqlTableUser = "um_user";
	private $configSqlTableSession = "um_session";
	private $configSessionExpire = 86400;

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

}

?>
