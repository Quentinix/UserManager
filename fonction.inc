<?php

$versionphpmini = "7.0.0";
if (version_compare(phpversion(), $versionphpmini, "<")) trigger_error("La version PHP de votre serveur n'est pas suffisament récent, la version de PHP ".$versionphpmini." est requise, la version installée est ".phpversion().".", E_USER_ERROR);

include __DIR__.'/config.inc';
//if(!array_search(@$_SERVER['HTTP_HOST'], $config_safety_domainename)) trigger_error("Domaine refusé !", E_USER_ERROR);

function fonction_version():string{
	return "17w16";
}

function fonction_account_create(bool $confirm, string $user, string $mdp):bool{
	if($confirm != TRUE) trigger_error("Il faut confirmer la fonction", E_USER_ERROR);
	if($user == "") trigger_error("User n'est pas renseignée.", E_USER_ERROR);
	if($mdp == "") trigger_error("Mot de passe n'est pas renseignée.", E_USER_ERROR);
	include(__DIR__."/config.inc");
	$mysqli_connect = mysqli_connect($config_mysqli_host, $config_mysqli_user, $config_mysqli_mdp, $config_mysqli_db);
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
	$mysqli_result = mysqli_query($mysqli_connect, "SELECT * FROM ".$config_mysqli_table_user." WHERE `user` LIKE '".$user."'");
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
	$existe = 0;
	while($mysqli_row=mysqli_fetch_array($mysqli_result)) $existe = 1;
	if($existe == 1){
		mysqli_close($mysqli_connect);
		return FALSE;
	}
	$mdpcrypt = fonction_hash_create($mdp);
	mysqli_query($mysqli_connect, "INSERT INTO `".$config_mysqli_table_user."` (`id`, `user`, `pass`) VALUES (NULL, '".$user."', '".$mdpcrypt."')");
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
	while (TRUE){
		mysqli_query($mysqli_connect, "SELECT * FROM `".$config_mysqli_table_user."` WHERE `user` LIKE '".$user."'");
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

function fonction_account_connect(bool $confirm, string $user, string $mdp):bool{
	if($confirm != TRUE) trigger_error("Il faut confirmer la fonction", E_USER_ERROR);
	if($user == "") trigger_error("User n'est pas renseignée.", E_USER_ERROR);
	if($mdp == "") trigger_error("Mot de passe n'est pas renseignée.", E_USER_ERROR);
	if(fonction_hash_verif(TRUE, $user, $mdp)) {
		@session_start();
		session_regenerate_id();
		include(__DIR__."/config.inc");
		$mysqli_connect = mysqli_connect($config_mysqli_host, $config_mysqli_user, $config_mysqli_mdp, $config_mysqli_db);
		if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
		$expire = time() + $config_account_expire;
		mysqli_query($mysqli_connect, "INSERT INTO `".$config_mysqli_table_session."` (`id`, `user`, `session_id`, `expire`) VALUES (NULL, '".$user."', '".session_id()."', '".$expire."')");
		if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
		mysqli_close($mysqli_connect);
		return TRUE;
	}else{
		return FALSE;
	}
}
/* 
 * Fonction : fonction_hash_verif, fonction_account_verif
 * Variable config : $config_mysqli_host, $config_mysqli_user, $config_mysqli_mdp, $config_mysqli_db, $config_account_expire, $config_mysqli_table_session
 */

function fonction_account_disconnect(bool $confirm):bool{
	if($confirm != TRUE) trigger_error("Il faut confirmer la fonction", E_USER_ERROR);
	session_start();
	session_regenerate_id();
	return TRUE;
}
/*
 * Fonction :
 * Variable config :
 */

/*
function fonction_account_mod(bool $confirm, string $user, string $nom, string $prenom, string $email, int $cp, string $ville, string $rue, int $numero, string $complement):bool{
	if($confirm != TRUE) trigger_error("Il faut confirmer la fonction", E_USER_ERROR);
	$verif = fonction_account_verif();
	if ($verif[connect] == FALSE) return FALSE;
	$virgule = 0;
	if($user != "") $virgule++;
	if($nom != "") $virgule++;
	if($prenom != "") $virgule++;
	if($email != "") $virgule++;
	if($cp != "") $virgule++;
	if($ville != "") $virgule++;
	if($rue != "") $virgule++;
	if($numero != "") $virgule++;
	if($complement != "") $virgule++;
	if($virgule == 0) return FALSE;
	$virgule--;
	$virgulev = $virgule;
	include(__DIR__."/config.inc");
	$mysqli_connect = mysqli_connect($config_mysqli_host, $config_mysqli_user, $config_mysqli_mdp, $config_mysqli_db);
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
	$query = "UPDATE `".$config_mysqli_table_user."` SET ";
	if($user != ""){$query .= "`email` = '".$email."'"; if ($virgule > 0) {$query .= ", "; $virgule--;}}
	if($nom  != ""){$query .= "`nom` = '".$nom."'"; if ($virgule > 0) {$query .= ", "; $virgule--;}}
	if($prenom != ""){$query .= "`prenom` = '".$prenom."'"; if ($virgule > 0) {$query .= ", "; $virgule--;}}
	if($email != ""){$query .= "`email` = '".$email."'"; if ($virgule > 0) {$query .= ", "; $virgule--;}}
	if($cp != ""){$query .= "`cp` = '".$cp."'"; if ($virgule > 0) {$query .= ", "; $virgule--;}}
	if($ville != ""){$query .= "`ville` = '".$ville."'"; if ($virgule > 0) {$query .= ", "; $virgule--;}}
	if($rue != ""){$query .= "`rue` = '".$rue."'"; if ($virgule > 0) {$query .= ", "; $virgule--;}}
	if($numero != ""){$query .= "`numero` = '".$numero."'"; if ($virgule > 0) {$query .= ", "; $virgule--;}}
	if($complement != ""){$query .= "`complement` = '".$complement."'";}
	$query .= " WHERE `id` = '".@$verif['id_utilisateur']."'";
	mysqli_query($mysqli_connect, $query);
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
	mysqli_close($mysqli_connect);
	return TRUE;
}
/*
 * Fonction : fonction_account_verif
 * Variable config : $config_mysqli_host, $config_mysqli_user, $config_mysqli_mdp, $config_mysqli_db, $config_mysqli_table_user
 */

function fonction_account_modmdp(bool $confirm, string $mdp):bool{
	if($confirm != TRUE) trigger_error("Il faut confirmer la fonction", E_USER_ERROR);
	if($mdp === "") trigger_error("Mot de passe n'est pas renseignée.", E_USER_ERROR);
	$verif = fonction_account_verif();
	if(@$verif[connect] == FALSE) return FALSE;
	$mdpcrypt = fonction_hash_create($mdp);
	include(__DIR__."/config.inc");
	$mysqli_connect = mysqli_connect($config_mysqli_host, $config_mysqli_user, $config_mysqli_mdp, $config_mysqli_db);
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
	mysqli_query($mysqli_connect, "UPDATE `".$config_mysqli_table_user."` SET `pass` = '".$mdpcrypt."' WHERE `id` = ".@$verif['id_utilisateur']);
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
	mysqli_close($mysqli_connect);
	return TRUE;
}
/*
 * Fonction : fonction_account_verif, fonction_hash_create
 * Variable config : $config_mysqli_host, $config_mysqli_user, $config_mysqli_mdp, $config_mysqli_db, $config_mysqli_table_user
 */

function fonction_account_verif():array{
	if(isset($_COOKIE["PHPSESSID"]) == FALSE) return array("connect" => FALSE);
	include(__DIR__."/config.inc");
	$mysqli_connect = mysqli_connect($config_mysqli_host, $config_mysqli_user, $config_mysqli_mdp, $config_mysqli_db);
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
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

function fonction_account_clearsession():bool{
	include(__DIR__."/config.inc");
	$mysqli_connect = mysqli_connect($config_mysqli_host, $config_mysqli_user, $config_mysqli_mdp, $config_mysqli_db);
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
	mysqli_query($mysqli_connect, "DELETE FROM `".$config_mysqli_table_session."` WHERE `expire` < ".time());
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
	mysqli_close($mysqli_connect);
	return TRUE;
}
/*
 * Fonction :
 * Variable config : $config_mysqli_host, $config_mysqli_user, $config_mysqli_mdp, $config_mysqli_db, $config_mysqli_table_session
 */

function fonction_accountrecup_create(bool $confirm, string $email, string $user):string{
	if($confirm != TRUE) trigger_error("Il faut confirmer la fonction", E_USER_ERROR);
	if($email == "") trigger_error("Email n'est pas renseignée.", E_USER_ERROR);
	if($user == "") trigger_error("User n'est pas renseignée.", E_USER_ERROR);
	include(__DIR__."/config.inc");
	$mysqli_connect = mysqli_connect($config_mysqli_host, $config_mysqli_user, $config_mysqli_mdp, $config_mysqli_db);
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
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

function fonction_accountrecup_use(bool $confirm, string $token):string{
	if($confirm != TRUE) trigger_error("Il faut confirmer la fonction", E_USER_ERROR);
	if($token == "") trigger_error("Token n'est pas renseignée.", E_USER_ERROR);
	include(__DIR__."/config.inc");
	$mysqli_connect = mysqli_connect($config_mysqli_host, $config_mysqli_user, $config_mysqli_mdp, $config_mysqli_db);
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
	$mysqli_result = mysqli_query($mysqli_connect, "SELECT * FROM `".$config_mysqli_table_recupcompte."` WHERE `token` LIKE '".$token."'");
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
	while ($mysqli_row = mysqli_fetch_assoc($mysqli_result)){
		mysqli_close($mysqli_connect);
		return @$mysqli_row[user];
	}
	mysqli_close($mysqli_connect);
	return NULL;
}

function fonction_return_list(int $nb):string{
	if ($nb === 1) return "Mauvais nom d'utilisateur ou mot de passe.";
	return "";
}

function fonction_admin_createtoken(bool $confirm):bool{
	if($confirm != TRUE) trigger_error("Il faut confirmer la fonction", E_USER_ERROR);
	include ("config.inc");
	$token = md5(uniqid()).md5(uniqid());
	$mysqli_connect = mysqli_connect($config_mysqli_host, $config_mysqli_user, $config_mysqli_mdp, $config_mysqli_db);
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
	$mysqli_result = mysqli_query($mysqli_connect, "INSERT INTO `".$config_mysqli_table_adminaccess."` (`id`, `token`, `ip`) VALUES (NULL, '".$token."', '".@$_SERVER[REMOTE_ADDR]."')");
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
	setcookie("admintoken", $token);
	mysqli_close($mysqli_connect);
	return TRUE;
}

function fonction_admin_usetoken(bool $confirm):bool{
	if($confirm != TRUE) trigger_error("Il faut confirmer la fonction", E_USER_ERROR);
	include ("config.inc");
	$mysqli_connect = mysqli_connect($config_mysqli_host, $config_mysqli_user, $config_mysqli_mdp, $config_mysqli_db);
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
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

function fonction_admin_connectuser(bool $confirm, string $user):bool{ // ATTENTION: Fonction sensible : Permet une connexion d'un utilisateur sans mot de passe.
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

function fonction_modassist_create(bool $confirm, string $user, bool $mod_info = FALSE, bool $mod_mdp = FALSE):int{
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

function fonction_modassist_use(bool $confirm, string $token, int $pin):bool{
	
}

function fonction_hash_create(string $pass):string{
	if($pass == "") trigger_error("Mot de passe n'est pas renseignée.", E_USER_ERROR);
	$chars = array(0,1,2,3,4,5,6,7,8,9,'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',0,1,2,3,4,5,6,7,8,9, 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
	$serial = '';
	$max = count($chars)-1;
	$n = mt_rand(9, 999);
	$t = mt_rand(1, 10);
	for($i=0;$i<$n;$i++){
		$serial .= (!($i % $t) && $i ? '-' : '').$chars[mt_rand(0, $max)];
	}
	$cryptnalea = str_split(md5($serial));
	$i = 0;
	$j = 1;
	while($i < 32){
		if($i >= 1){$j = $i-1;}
		if($cryptnalea[$i] == "a"){$cryptnalea[$i] = mt_rand(9999, 99999)+$cryptnalea[$j];}
		if($cryptnalea[$i] == "b"){$cryptnalea[$i] = mt_rand(9999, 99999)+$cryptnalea[$j];}
		if($cryptnalea[$i] == "c"){$cryptnalea[$i] = mt_rand(9999, 99999)+$cryptnalea[$j];}
		if($cryptnalea[$i] == "d"){$cryptnalea[$i] = mt_rand(9999, 99999)+$cryptnalea[$j];}
		if($cryptnalea[$i] == "e"){$cryptnalea[$i] = mt_rand(9999, 99999)+$cryptnalea[$j];}
		if($cryptnalea[$i] == "f"){$cryptnalea[$i] = mt_rand(9999, 99999)+$cryptnalea[$j];}
		@$keyalea .= $cryptnalea[$i];
		$i++;
	}
	$leng_key = strlen($keyalea);
	$leng_keyalea = $leng_key - 32;
	$keyalea_mod = substr($keyalea, 0, -$leng_keyalea);
	$pass .= $keyalea_mod;
	$cryptn = str_split(md5($pass));
	$i = 0;
	$j = 1;
	include(__DIR__."/config.inc");
	$seed = explode("-", $config_hash_seed);
	while($i < 32){
		if($i >= 1){$j = $i-1;}
		if($cryptn[$i] == "a"){$cryptn[$i] = $seed[0]+$cryptn[$j];}
		if($cryptn[$i] == "b"){$cryptn[$i] = $seed[1]+$cryptn[$j];}
		if($cryptn[$i] == "c"){$cryptn[$i] = $seed[2]+$cryptn[$j];}
		if($cryptn[$i] == "d"){$cryptn[$i] = $seed[3]+$cryptn[$j];}
		if($cryptn[$i] == "e"){$cryptn[$i] = $seed[4]+$cryptn[$j];}
		if($cryptn[$i] == "f"){$cryptn[$i] = $seed[5]+$cryptn[$j];}
		@$key_mod .= $cryptn[$i];
		$i++;
	}
	$key = $keyalea_mod;
	$key .= $key_mod;
	return $key;
}
/*
 * Fonction :
 * Variable config : $config_hash_seed
 */

function fonction_hash_verif(bool $confirm, string $user, string $pass):bool{ //Permet de comparer la graine de l'utilisateur de la base de données avec une chaine de caractères.
	if($confirm != TRUE) trigger_error("Il faut confirmer la fonction", E_USER_ERROR);
	if($user == "") trigger_error("User n'est pas renseignée.", E_USER_ERROR);
	if($pass == "") trigger_error("Mot de passe n'est pas renseignée.", E_USER_ERROR);
	include(__DIR__."/config.inc");
	$mysqli_connect = mysqli_connect($config_mysqli_host, $config_mysqli_user, $config_mysqli_mdp, $config_mysqli_db);
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
	$mysqli_result = mysqli_query($mysqli_connect, "SELECT * FROM `".$config_mysqli_table_user."` WHERE `user` LIKE '".$user."'");
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error($mysqli_connect), E_USER_ERROR);
	while($mysqli_row = mysqli_fetch_assoc($mysqli_result)){
		$keyalea = @$mysqli_row[pass];
		$testuser = @$mysqli_row[user];
		$leng_key = strlen($keyalea);
		$leng_keyalea = $leng_key - 32;
		$keyalea_mod = substr($keyalea, 0, -$leng_keyalea);
	}
	$pass .= @$keyalea_mod;
	$cryptn = str_split(md5($pass));
	$i = 0;
	$j = 1;
	$seed = explode("-", $config_hash_seed);
	while($i < 32){
		if($i >= 1){$j = $i-1;}
		if($cryptn[$i] == "a"){$cryptn[$i] = $seed[0]+$cryptn[$j];}
		if($cryptn[$i] == "b"){$cryptn[$i] = $seed[1]+$cryptn[$j];}
		if($cryptn[$i] == "c"){$cryptn[$i] = $seed[2]+$cryptn[$j];}
		if($cryptn[$i] == "d"){$cryptn[$i] = $seed[3]+$cryptn[$j];}
		if($cryptn[$i] == "e"){$cryptn[$i] = $seed[4]+$cryptn[$j];}
		if($cryptn[$i] == "f"){$cryptn[$i] = $seed[5]+$cryptn[$j];}
		@$key_mod .= $cryptn[$i];
		$i++;
	}
	$key = @$keyalea_mod;
	$key .= $key_mod;
	mysqli_close($mysqli_connect);
	if($key == @$keyalea)return TRUE;
	else return FALSE;
}
/*
 * Fonction :
 * Variable config : $config_mysqli_host, $config_mysqli_user, $config_mysqli_mdp, $config_mysqli_db, $config_mysqli_table_user, $config_hash_seed
 */

function fonction_create_mdp():string{
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

function fonction_mod_mysqlaccess_select($mod, $parametre){
	if (!isset($parametre)) return trigger_error("La fonction doit comporter deux valeurs obligatoires.", E_USER_ERROR);
	if (!isset($mod)) return trigger_error("La fonction doit comporter deux valeurs obligatoires.", E_USER_ERROR);
	include(__DIR__."/config.inc");
	$mysqli_connect = mysqli_connect($config_mysqli_host, $config_mysqli_user, $config_mysqli_mdp, $config_mysqli_db);
	$mysqli_result = mysqli_query($mysqli_connect, "SELECT * FROM ".$mod." WHERE `parametre` LIKE '".$parametre."'");
	while(@$mysqli_row=mysqli_fetch_assoc($mysqli_result)) return @$mysqli_row[valeur];
	mysqli_close($mysqli_connect);
	return "";
}
/*
 * Fonction :
 * Variable config : $config_mysqli_host, $config_mysqli_user, $config_mysqli_mdp, $config_mysqli_db
 */

function fonction_mod_mysqlaccess_write($mod, $parametre, $valeur){
	if (!isset($parametre)) return trigger_error("La fonction doit comporter trois valeurs obligatoires.", E_USER_ERROR);
	if (!isset($valeur)) return trigger_error("La fonction doit comporter trois valeurs obligatoires.", E_USER_ERROR);
	if (!isset($mod)) return trigger_error("La fonction doit comporter trois valeurs obligatoires.", E_USER_ERROR);
	include(__DIR__."/config.inc");
	$mysqli_connect = mysqli_connect($config_mysqli_host, $config_mysqli_user, $config_mysqli_mdp, $config_mysqli_db);
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error(), E_USER_ERROR);
	$mysqli_result = mysqli_query($mysqli_connect, "SELECT * FROM ".$mod." WHERE `parametre` LIKE '".$parametre."'");
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error(), E_USER_ERROR);
	$exist = FALSE;
	while($mysqli_row=mysqli_fetch_assoc($mysqli_result)) $exist = TRUE;
	if ($exist == TRUE) $mysqli_request = "UPDATE `".$mod."` SET `valeur` = '".$valeur."' WHERE `parametre` = '".$parametre."'";
	else $mysqli_request = "INSERT INTO `".$mod."` (`id`, `parametre`, `valeur`) VALUES (NULL, '".$parametre."', '".$valeur."')";
	mysqli_query($mysqli_connect, $mysqli_request);
	if(mysqli_errno($mysqli_connect)) trigger_error("Echec requête MySQL : ".mysqli_errno($mysqli_connect)." : ".mysqli_error(), E_USER_ERROR);
	mysqli_close($mysqli_connect);
	return TRUE;
}
/*
 * Fonction :
 * Variable config : $config_mysqli_host, $config_mysqli_user, $config_mysqli_mdp, $config_mysqli_db
 */

function fonction_mod_freemobile_sendsms($user, $pass, $message){
	$url = "https://smsapi.free-mobile.fr/sendmsg?user=".$user."&pass=".$pass."&msg=".urlencode($message);
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$result = curl_exec($ch);
	curl_close($ch);
	return TRUE;
}
/*
 * Fonction :
 * Variable config :
 */






























?>