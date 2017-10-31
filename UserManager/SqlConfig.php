<?php
namespace UserManager;

class SqlConfig
{
	private static $configSqlHost = "localhost";
	private static $configSqlUser = "root";
	private static $configSqlPass = "";
	private static $configSqlDb = "usermanager_dev";
	private static $configSqlTableUser = "um_user";
	
	static function getHost() {
		return self::$configSqlHost;
	}
	static function getUser() {
		return self::$configSqlUser;
	}
	static function getPass() {
		return self::$configSqlPass;
	}
	static function getDb() {
		return self::$configSqlDb;
	}
	static function getTableUser() {
		return self::$configSqlTableUser;
	}

}

