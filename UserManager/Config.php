<?php
namespace UserManager;

class Config
{
	private static $configSqlHost = "localhost";
	private static $configSqlUser = "root";
	private static $configSqlPass = "";
	private static $configSqlDb = "usermanager_dev";
	private static $configSqlTableUser = "um_user";
	
	static function nameHost() {
		return self::$configSqlHost;
	}
	static function nameUser() {
		return self::$configSqlUser;
	}
	static function namePass() {
		return self::$configSqlPass;
	}
	static function nameDb() {
		return self::$configSqlDb;
	}
	static function tablenameUser() {
		return self::$configSqlTableUser;
	}

}

