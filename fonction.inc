<?php

$versionphpmini = "7.0.0";
if (version_compare(phpversion(), $versionphpmini, "<")) trigger_error("La version PHP de votre serveur n'est pas suffisament récent, la version de PHP ".$versionphpmini." est requise, la version installée est ".phpversion().".", E_USER_ERROR);

include __DIR__.'/config.inc';
//if(!array_search(@$_SERVER['HTTP_HOST'], $config_safety_domainename)) trigger_error("Domaine refusé !", E_USER_ERROR); TODO: Trouver une autre protection

function userManager_version():string{
	return "17w38test";
}

function userManager_account_create(bool $confirm, string $user, string $pass, string $email = NULL, string $nom = NULL, string $prenom = NULL, string $adresse = NULL, string $ville = NULL, string $code_postal = NULL):bool{
	if($confirm != TRUE) trigger_error("Il faut confirmer la fonction", E_USER_ERROR);
	if($user == "") trigger_error("User n'est pas renseignée.", E_USER_ERROR);
	if($pass == "") trigger_error("Mot de passe n'est pas renseignée.", E_USER_ERROR);
	include(__DIR__."/config.inc");
	$mysqli_connect = userManager_mysqli_connect();
	$mysqli_result = mysqli_query($mysqli_connect, "SELECT * FROM ".$config_mysqli_table_user." WHERE `user` LIKE '".$user."'");
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
	$existe = 0;
	while($mysqli_row=mysqli_fetch_array($mysqli_result)) $existe = 1;
	if($existe == 1){
		mysqli_close($mysqli_connect);
		return FALSE;
	}
	$pass_crypt = userManager_hash_create($pass);
	mysqli_query($mysqli_connect, "INSERT INTO `um_user` (`id`, `user`, `pass`, `email`, `nom`, `prenom`, `adresse`, `ville`, `code_postal`) VALUES (NULL, '".$user."', '".$pass_crypt."', '".$email."', '".$nom."', '".$prenom."', '".$adresse."', '".$ville."', '".$code_postal."')");
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
	while (TRUE){
		$mysqli_result = mysqli_query($mysqli_connect, "SELECT * FROM `".$config_mysqli_table_user."` WHERE `user` LIKE '".$user."'");
		if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
		while ($mysqli_row = mysqli_fetch_assoc($mysqli_result)){
			mysqli_close($mysqli_connect);
			return TRUE;
		}
		sleep(1);
		// TODO : securité anti time out.
	}
}
/* Fonction : fonction_hash_create
 * Variable config : $config_mysqli_host, $config_mysqli_user, $config_mysqli_mdp, $config_mysqli_db, $config_mysqli_table_user
 */

function userManager_account_connect(bool $confirm, string $user, string $pass):bool{
	if($confirm != TRUE) trigger_error("Il faut confirmer la fonction", E_USER_ERROR);
	if($user == "") trigger_error("User n'est pas renseignée.", E_USER_ERROR);
	if($pass == "") trigger_error("Mot de passe n'est pas renseignée.", E_USER_ERROR);
	$mysqli_connect = userManager_mysqli_connect();
	include(__DIR__."/config.inc");
	$mysqli_result = mysqli_query($mysqli_connect, "SELECT user, pass FROM `" . $config_mysqli_table_user . "` WHERE `user` LIKE '" . $user . "'");
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
	$userok = FALSE;
	while ($mysqli_row = mysqli_fetch_assoc($mysqli_result)){
		$pass_verif = $mysqli_row["pass"];
		$userok = TRUE;
	}
	if ($userok == FALSE)
		return FALSE;
	if(userManager_hash_verif(TRUE, $pass, $pass_verif)) {
		@session_start();
		session_regenerate_id();
		$mysqli_connect = mysqli_connect($config_mysqli_host, $config_mysqli_user, $config_mysqli_mdp, $config_mysqli_db);
		if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
		$expire = time() + $config_account_expire;
		mysqli_query($mysqli_connect, "INSERT INTO `".$config_mysqli_table_session."` (`id`, `user`, `session_id`, `expire`) VALUES (NULL, '".$user."', '".session_id()."', '".$expire."')");
		if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
		mysqli_close($mysqli_connect);
		while (TRUE){
			$mysqli_connect = userManager_mysqli_connect();
			$mysqli_result = mysqli_query($mysqli_connect, "SELECT * FROM `".$config_mysqli_table_session."` WHERE `user` LIKE '".$user."'");
			if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
			while ($mysqli_row = mysqli_fetch_assoc($mysqli_result)){
				mysqli_close($mysqli_connect);
				return TRUE;
			}
			sleep(1);
			// TODO: sécurité anti time out
		}
	}else{
		return FALSE;
	}
}
/* 
 * Fonction : fonction_hash_verif, fonction_account_verif
 * Variable config : $config_mysqli_host, $config_mysqli_user, $config_mysqli_mdp, $config_mysqli_db, $config_account_expire, $config_mysqli_table_session
 */

function userManager_account_disconnect(bool $confirm):bool{
	if($confirm != TRUE) trigger_error("Il faut confirmer la fonction", E_USER_ERROR);
	session_start();
	session_regenerate_id();
	return TRUE;
}
/*
 * Fonction :
 * Variable config :
 */

function userManager_account_mod(bool $confirm, string $user = "", string $nom = "", string $prenom = "", string $email = "", string $cp = "", string $ville = "", string $adresse = ""):bool{
	if($confirm != TRUE) trigger_error("Il faut confirmer la fonction", E_USER_ERROR);
	$verif = userManager_account_verif();
	if ($verif["connect"] == FALSE) return FALSE;
	$virgule = 0;
	if($user != "") $virgule++;
	if($nom != "") $virgule++;
	if($prenom != "") $virgule++;
	if($email != "") $virgule++;
	if($cp != "") $virgule++;
	if($ville != "") $virgule++;
	if($adresse != "") $virgule++;
	if($virgule == 0) return FALSE;
	$virgule--;
	include(__DIR__."/config.inc");
	$mysqli_connect = userManager_mysqli_connect();
	$query = "UPDATE `".$config_mysqli_table_user."` SET ";
	if($user != ""){
		$query .= "`user` = '".$user."'"; 
		if ($virgule > 0) {
			$query .= ", ";
			$virgule--;
		}
	}
	if($nom  != ""){
		$query .= "`nom` = '".$nom."'"; 
		if ($virgule > 0) {
			$query .= ", ";
			$virgule--;
		}
	}
	if($prenom != ""){
		$query .= "`prenom` = '".$prenom."'";
		 if ($virgule > 0) {
		 	$query .= ", ";
		 	$virgule--;
		 }
		}
	if($email != ""){
		$query .= "`email` = '".$email."'"; 
		if ($virgule > 0) {
			$query .= ", ";
			$virgule--;
		}
	}
	if($cp != ""){
		$query .= "`code_postal` = '".$cp."'";
		if ($virgule > 0) {
			$query .= ", ";
			$virgule--;
		}
	}
	if($ville != ""){
		$query .= "`ville` = '".$ville."'";
		if ($virgule > 0) {
			$query .= ", ";
			$virgule--;
		}
	}
	if($adresse != ""){
		$query .= "`adresse` = '".$adresse."'";
		if ($virgule > 0) {
			$query .= ", ";
			$virgule--;
		}
	}
	$query .= " WHERE `id` = '".$verif["id_utilisateur"]."'";
	mysqli_query($mysqli_connect, $query);
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
	mysqli_close($mysqli_connect);
	return TRUE;
}
/*
 * Fonction : fonction_account_verif
 * Variable config : $config_mysqli_host, $config_mysqli_user, $config_mysqli_mdp, $config_mysqli_db, $config_mysqli_table_user
 */

function userManager_account_modmdp(bool $confirm, string $mdp):bool{
	if($confirm != TRUE) trigger_error("Il faut confirmer la fonction", E_USER_ERROR);
	if($mdp === "") trigger_error("Mot de passe n'est pas renseignée.", E_USER_ERROR);
	$verif = userManager_account_verif();
	if(@$verif[connect] == FALSE) return FALSE;
	$mdpcrypt = userManager_hash_create($mdp);
	include(__DIR__."/config.inc");
	$mysqli_connect = userManager_mysqli_connect();
	mysqli_query($mysqli_connect, "UPDATE `".$config_mysqli_table_user."` SET `pass` = '".$mdpcrypt."' WHERE `id` = ".@$verif['id_utilisateur']);
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
	mysqli_close($mysqli_connect);
	return TRUE;
}
/*
 * Fonction : fonction_account_verif, fonction_hash_create
 * Variable config : $config_mysqli_host, $config_mysqli_user, $config_mysqli_mdp, $config_mysqli_db, $config_mysqli_table_user
 */

function userManager_account_verif():array{
	if(isset($_COOKIE["PHPSESSID"]) == FALSE) return array("connect" => FALSE);
	include(__DIR__."/config.inc");
	$mysqli_connect = userManager_mysqli_connect();
	$mysqli_result = mysqli_query($mysqli_connect, "SELECT * FROM ".$config_mysqli_table_session." WHERE `session_id` LIKE '".$_COOKIE["PHPSESSID"]."'");
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
	$confirmation = 0;
	while($mysqli_row=mysqli_fetch_assoc($mysqli_result)){
		if(@$mysqli_row[expire] > time()){
			$confirmation = 1;
			$user = @$mysqli_row[user];
			$expire = @$mysqli_row[expire];
			$session_mysql = @$mysqli_row[session_id];
		}
	}
	if($confirmation == 0){
		$return = array(
				"connect" => FALSE,
		);
		mysqli_close($mysqli_connect);
		return $return;
	}
	$mysqli_result = mysqli_query($mysqli_connect, "SELECT * FROM ".$config_mysqli_table_user." WHERE `user` LIKE '".$user."'");
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
	while($mysqli_row=mysqli_fetch_assoc($mysqli_result)){
		$return = array(
				"connect" => TRUE,
				"session_mysql" => $session_mysql,
				"id_utilisateur" => @$mysqli_row['id'],
				"session_expire" => $expire,
				"user" => @$mysqli_row['user'],
		);
	}
	mysqli_close($mysqli_connect);
	return $return;
}
/*
 * Fonction :
 * Variable config : $config_mysqli_host, $config_mysqli_user, $config_mysqli_mdp, $config_mysqli_db, $config_mysqli_table_session
 */

function userManager_account_clearsession():bool{
	include(__DIR__."/config.inc");
	$mysqli_connect = userManager_mysqli_connect();
	mysqli_query($mysqli_connect, "DELETE FROM `".$config_mysqli_table_session."` WHERE `expire` < ".time());
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
	mysqli_close($mysqli_connect);
	return TRUE;
}
/*
 * Fonction :
 * Variable config : $config_mysqli_host, $config_mysqli_user, $config_mysqli_mdp, $config_mysqli_db, $config_mysqli_table_session
 */

function userManager_mysqli_connect(){
	include "config.inc";
	$mysqli_connect = mysqli_connect($config_mysqli_host, $config_mysqli_user, $config_mysqli_mdp, $config_mysqli_db);
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
	return $mysqli_connect;
}
/*
 * Fonction :
 * Variable config : $config_mysqli_host, $config_mysqli_user, $config_mysqli_mdp, $config_mysqli_db
 */

function userManager_accountrecup_create(bool $confirm, string $email, string $user):string{
	if($confirm != TRUE) trigger_error("Il faut confirmer la fonction", E_USER_ERROR);
	if($email == "") trigger_error("Email n'est pas renseignée.", E_USER_ERROR);
	if($user == "") trigger_error("User n'est pas renseignée.", E_USER_ERROR);
	include(__DIR__."/config.inc");
	$mysqli_connect = userManager_mysqli_connect();
	$mysqli_result = mysqli_query($mysqli_connect, "SELECT * FROM `".$config_mysqli_table_user."` WHERE `user` LIKE '".$user."' AND `email` LIKE '".$email."'");
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
	$result = 0;
	while ($mysqli_row = mysqli_fetch_assoc($mysqli_result)) $user_exist = TRUE;
	if ($user_exist == TRUE) {
		$uniqid = md5(uniqid()).md5(uniqid());
		$expire = time() + $config_account_recupexpire;
		$mysqli_result = mysqli_query($mysqli_connect, "INSERT INTO `".$config_mysqli_table_recupcompte."` (`id`, `token`, `user`, `expire`) VALUES (NULL, '".$uniqid."', '".$user."', '".$expire."')");
		if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
		mysqli_close($mysqli_connect);
		return $uniqid;
	}
	mysqli_close($mysqli_connect);
	return NULL;
}

function userManager_accountrecup_use(bool $confirm, string $token):string{
	if($confirm != TRUE) trigger_error("Il faut confirmer la fonction", E_USER_ERROR);
	if($token == "") trigger_error("Token n'est pas renseignée.", E_USER_ERROR);
	include(__DIR__."/config.inc");
	$mysqli_connect = userManager_mysqli_connect();
	$mysqli_result = mysqli_query($mysqli_connect, "SELECT * FROM `".$config_mysqli_table_recupcompte."` WHERE `token` LIKE '".$token."'");
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
	while ($mysqli_row = mysqli_fetch_assoc($mysqli_result)){
		mysqli_close($mysqli_connect);
		return @$mysqli_row[user];
	}
	mysqli_close($mysqli_connect);
	return NULL;
}

function userManager_return_list(int $nb):string{
	if ($nb === 1) return "Mauvais nom d'utilisateur ou mot de passe.";
	return NULL;
}

/* TODO: en attente d'une solution

function userManager_admin_createtoken(bool $confirm):bool{
	if($confirm != TRUE) trigger_error("Il faut confirmer la fonction", E_USER_ERROR);
	include ("config.inc");
	$token = md5(uniqid()).md5(uniqid());
	$mysqli_connect = userManager_mysqli_connect();
	$mysqli_result = mysqli_query($mysqli_connect, "INSERT INTO `".$config_mysqli_table_adminaccess."` (`id`, `token`, `ip`) VALUES (NULL, '".$token."', '".@$_SERVER[REMOTE_ADDR]."')");
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
	setcookie("admintoken", $token);
	mysqli_close($mysqli_connect);
	return TRUE;
}

function userManager_admin_usetoken(bool $confirm):bool{
	if($confirm != TRUE) trigger_error("Il faut confirmer la fonction", E_USER_ERROR);
	include ("config.inc");
	$mysqli_connect = userManager_mysqli_connect();
	$mysqli_result = mysqli_query($mysqli_connect, "SELECT * FROM `".$config_mysqli_table_adminaccess."` WHERE `token` LIKE '".@$_COOKIE[admintoken]."'");
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($m1ysqli_connect), E_USER_ERROR);
	while ($mysqli_row = mysqli_fetch_assoc($mysqli_result)){
		if (@$_SERVER['REMOTE_ADDR'] == @$mysqli_row[ip]) {
			setcookie("admintoken", NULL);
			mysqli_close($mysqli_connect);
			return TRUE;
		}
	}
	mysqli_close($mysqli_connect);
	return FALSE;
}

*/

function userManager_admin_connectuser(bool $confirm, string $user):bool{ // ATTENTION: Fonction sensible : Permet une connexion d'un utilisateur sans mot de passe.
	if($confirm != TRUE) trigger_error("Il faut confirmer la fonction", E_USER_ERROR);
	if($user == "") trigger_error("User n'est pas renseignée.", E_USER_ERROR);
	if (fonction_admin_usetoken(TRUE)){
		include ("config.inc");
		$mysqli_connect = mysqli_connect($config_mysqli_host, $config_mysqli_user, $config_mysqli_mdp, $config_mysqli_db);
		if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
		$mysqli_result = mysqli_query($mysqli_connect, "SELECT * FROM `".$config_mysqli_table_user."` WHERE `user` LIKE '".$user."'");
		if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
		mysqli_close($mysqli_connect);
		return TRUE;
	}
	mysqli_close($mysqli_connect);
	return FALSE;
}

/*

function userManager_modassist_create(bool $confirm, string $user, bool $mod_info = FALSE, bool $mod_mdp = FALSE):int{
	if($confirm != TRUE) trigger_error("Il faut confirmer la fonction", E_USER_ERROR);
	if($user == "") trigger_error("User n'est pas renseignée.", E_USER_ERROR);
	if(!$mod_info AND !$mod_mdp) trigger_error("Il faut au moins une option à modifier.", E_USER_ERROR);
	include ("config.inc");
	$mysqli_result = mysqli_query($mysqli_connect, "SELECT * FROM `$config_mysqli_table_user` WHERE `user` LIKE '".$user."'");
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
	$exist = FALSE;
	while ($mysqli_row = mysqli_fetch_assoc($mysqli_result)) $exist = TRUE;
	if ($exist){
		$token = md5(uniqid()).md5(uniqid());
		$mysqli_connect = mysqli_connect($config_mysqli_host, $config_mysqli_user, $config_mysqli_mdp, $config_mysqli_db);
		$pin = rand(0, 9).rand(0, 9).rand(0, 9).rand(0, 9);
		if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
		$mysqli_result = mysqli_query($mysqli_connect, "INSERT INTO `".$config_mysqli_table_modassist."` (`id`, `user`, `token`, `pin`, `info`, `mdp`) VALUES (NULL, '".$user."', '".$token."', '".$pin."', '".$mod_info."', '".$mod_mdp."')");
		if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
		mysqli_close($mysqli_connect);
		return $pin;
	}
	mysqli_close($mysqli_connect);
	return NULL;
}

function userManager_modassist_use(bool $confirm, string $token, int $pin):bool{
	
}

*/

function userManager_hash_create(string $pass):string{
	if($pass == "") trigger_error("Mot de passe n'est pas renseignée.", E_USER_ERROR);
	include 'config.inc';
	$uniqseed = explode("-", $config_hash_seed);
	$pass_hash = hash("sha256", $pass);
	$seed = "";
	for ($i=1; $i <= 128; $i++) 
		$seed .= mt_rand(0, 9);
	$pass_hash = hash("sha256", $pass_hash . $seed);
	$pass_split = str_split($pass_hash);
	for ($i=0; $i <= 63; $i++) { 
		if ($pass_split[$i] == "a")
			$pass_split[$i] = $uniqseed[0];
		if ($pass_split[$i] == "b")
			$pass_split[$i] = $uniqseed[1];
		if ($pass_split[$i] == "c")
			$pass_split[$i] = $uniqseed[2];
		if ($pass_split[$i] == "d")
			$pass_split[$i] = $uniqseed[3];
		if ($pass_split[$i] == "e")
			$pass_split[$i] = $uniqseed[4];
		if ($pass_split[$i] == "f")
			$pass_split[$i] = $uniqseed[5];
	}
	$pass_hash = $seed . implode("", $pass_split);
	return $pass_hash;
}

function userManager_hash_verif(bool $confirm, string $pass, string $pass_verif){
	if($confirm != TRUE) trigger_error("Il faut confirmer la fonction.", E_USER_ERROR);
	if($pass == "") trigger_error("Mot de passe n'est pas renseignée.", E_USER_ERROR);
	if($pass_verif == "") trigger_error("Mot de passe à vérifier n'est pas renseignée.", E_USER_ERROR);
	include 'config.inc';
	$uniqseed = explode("-", $config_hash_seed);
	$pass_hash = hash("sha256", $pass);
	$seed = substr($pass_verif, 0, 128);
	$pass_hash = hash("sha256", $pass_hash . $seed);
	$pass_split = str_split($pass_hash);
	for ($i=0; $i <= 63; $i++) { 
		if ($pass_split[$i] == "a")
			$pass_split[$i] = $uniqseed[0];
		if ($pass_split[$i] == "b")
			$pass_split[$i] = $uniqseed[1];
		if ($pass_split[$i] == "c")
			$pass_split[$i] = $uniqseed[2];
		if ($pass_split[$i] == "d")
			$pass_split[$i] = $uniqseed[3];
		if ($pass_split[$i] == "e")
			$pass_split[$i] = $uniqseed[4];
		if ($pass_split[$i] == "f")
			$pass_split[$i] = $uniqseed[5];
	}
	$pass_hash = $seed . implode("", $pass_split);
	if ($pass_verif === $pass_hash) return TRUE;
	return FALSE;
}

function userManager_create_mdp():string{
	$lettre_consonne = array("b","c","d","f","g","h","j","k","l","m","n","p","q","r","s","t","v","w","x","z");
	$lettre_voyelle = array("a","e","i","o","u","y");
	$lettre_special = array("&","(","-","_",")","=",",",";",":","!","$","*");
	$i = 0;
	while($i < 4){
		if($i == 0){
			$rand = array_rand($lettre_consonne);
			$return = strtoupper($lettre_consonne[$rand]);
			$rand = array_rand($lettre_voyelle);
			$return .= $lettre_voyelle[$rand];
		}
		if($i == 1 || $i == 2){
			$rand = array_rand($lettre_consonne);
			$return .= $lettre_consonne[$rand];
			$rand = array_rand($lettre_voyelle);
			$return .= $lettre_voyelle[$rand];
		}
		if($i == 3){
			$rand = array_rand($lettre_special);
			$return .= $lettre_special[$rand];
			$return .= rand(0, 9);
		}
		$i++;
	}
	return $return;
}
/*
 * Fonction :
 * Variable config :
 */

?>