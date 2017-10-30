<?php

$config_sql_host = "localhost";
$config_sql_user = "root";
$config_sql_pass = "";
$config_sql_db = "usermanager_type";

$config_sql_table = array (
	"session"		=> "um_session",
	"user"			=> "um_user",
	"module"		=> "um_module",
	"adminaccess"	=> "um_adminaccess",
);

$config_account_expire = 1296000; //15 jours
$config_account_recupexpire = 600; //10 minutes

$config_hash_seed = "71150-48206-46298-40348-60741-96450";

$config_safety_domainename = array (
		1 => "127.0.0.1",
		2 => "localhost",
		3 => "quentinix.fr"
	);

?>