<?php

$config_mysqli_host = "localhost";
$config_mysqli_user = "root";
$config_mysqli_mdp = "";
$config_mysqli_db = "usermanager_type";

$config_mysqli_table_session = "um_base_session";
$config_mysqli_table_user = "um_user";
$config_mysqli_table_module = "um_module";

$config_account_expire = 1296000; //15 jours
$config_account_recupexpire = 600; //10 minutes

$config_hash_seed = "71150-48206-46298-40348-60741-96450";

$config_safety_domainename = array (
		1 => "127.0.0.1",
		2 => "localhost",
		3 => "quentinix.fr"
	);

?>