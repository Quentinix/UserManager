<?php

namespace UserManager;

use Exception;

require 'SqlConfig.php';

class UserManager extends SqlConfig {
	
	private $sqlConnect;

	function __construct() {
		$this->sqlConnect = mysqli_connect($this->getConfigSqlHost(), $this->getConfigSqlUser(), $this->getConfigSqlPass(), $this->getConfigSqlDb());
	}

	function __destruct() {
		mysqli_close($this->sqlConnect);
	}

	function version() {
		return "0.0.1";
	}

	function accountCreate($user, $pass, $email = NULL, $nom = NULL, $prenom = NULL, $adresse = NULL, $ville = NULL, $code_postal = NULL) {
		if ($user == "")
			throw new Exception("User n'est pas renseignée.");
		if ($pass == "")
			throw new Exception("Pass n'est pas renseignée.");
		$exeTimeBegin = time();
		$sqlResult = mysqli_query($this->sqlConnect, "SELECT * FROM " . $this->getConfigSqlTableUser() . " WHERE `user` LIKE '" . $user . "'");
		if (mysqli_errno($this->sqlConnect))
			throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
		if (mysqli_fetch_array($sqlResult) != NULL)
			return false;
		$passCrypt = $this->hashCreate($pass);
		mysqli_query($this->sqlConnect, "INSERT INTO `" . $this->getConfigSqlTableUser() . "` (`id`, `user`, `pass`, `email`, `nom`, `prenom`, `adresse`, `ville`, `code_postal`) VALUES (NULL, '" . $user . "', '" . $passCrypt . "', '" . $email . "', '" . $nom . "', '" . $prenom . "', '" . $adresse . "', '" . $ville . "', '" . $code_postal . "')");
		if (mysqli_errno($this->sqlConnect))
			throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
		while (true) {
			$sqlResult = mysqli_query($this->sqlConnect, "SELECT * FROM `" . $this->getConfigSqlTableUser() . "` WHERE `user` LIKE '" . $user . "'");
			if (mysqli_errno($this->sqlConnect))
				throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
			if (mysqli_fetch_array($sqlResult) != NULL)
				return true;
			sleep(1);
			if (ini_get("max_execution_time") > time() - $exeTimeBegin - 2)
				return false;
		}
	}

	function accountConnect($user, $pass) {
		if ($user == "")
			throw new Exception("User n'est pas renseignée.");
		if ($pass == "")
			throw new Exception("Pass n'est pas renseignée.");
		$exeTimeBegin = time();
		$sqlResult = mysqli_query($this->sqlConnect, "SELECT user, pass FROM `" . $this->getConfigSqlTableUser() . "` WHERE `user` LIKE '" . $user . "'");
		if (mysqli_errno($this->sqlConnect))
			throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
		while ($sqlRow = mysqli_fetch_array($sqlResult))
			$passVerif = $sqlRow["pass"];
		if (! isset($passVerif))
			return false;
		if ($this->hashVerif($pass, $passVerif)) {
			@session_start();
			session_regenerate_id();
			$expire = time() + $this->getConfigSessionExpire();
			mysqli_query($this->sqlConnect, "INSERT INTO `" . $this->getConfigSqlTableSession() . "` (`id`, `user`, `session_id`, `expire`) VALUES (NULL, '" . $user . "', '" . session_id() . "', '" . $expire . "')");
			if (mysqli_errno($this->sqlConnect))
				throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
			while (true) {
				$sqlResult = mysqli_query($this->sqlConnect, "SELECT * FROM `" . $this->getConfigSqlTableSession() . "` WHERE `user` LIKE '" . $user . "'");
				if (mysqli_errno($this->sqlConnect))
					throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
				if (mysqli_fetch_array($sqlResult) != NULL)
					return true;
				sleep(1);
				if (ini_get("max_execution_time") > time() - $exeTimeBegin - 2)
					return false;
			}
		} else {
			return false;
		}
	}

	function accountDisconnect() {
		@session_start();
		mysqli_query($this->sqlConnect, "DELETE FROM `" . $this->getConfigSqlTableSession() . "` WHERE `session_id` = '" . session_id() . "'");
		if (mysqli_errno($this->sqlConnect))
			throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
		session_regenerate_id();
		return true;
	}

	function accountMod($user = "", $nom = "", $prenom = "", $email = "", $cp = "", $ville = "", $adresse = "") {
		$verif = $this->accountVerif();
		if ($verif["connect"] == FALSE)
			return FALSE;
		$virgule = 0;
		if ($user != "") {
			$sqlResult = mysqli_query($this->sqlConnect, "SELECT * FROM " . $this->getConfigSqlTableUser() . " WHERE `user` LIKE '" . $user . "'");
			if (mysqli_errno($this->sqlConnect))
				throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
			if (mysqli_fetch_array($sqlResult) != NULL)
				return false;
			$virgule++;
		}
		if ($nom != "")
			$virgule++;
		if ($prenom != "")
			$virgule++;
		if ($email != "")
			$virgule++;
		if ($cp != "")
			$virgule++;
		if ($ville != "")
			$virgule++;
		if ($adresse != "")
			$virgule++;
		if ($virgule == 0)
			return FALSE;
		$sqlQuery = "UPDATE `" . $this->getConfigSqlTableUser() . "` SET ";
		if ($user != "") {
			$sqlQuery .= "`user` = '" . $user . "'";
			if ($virgule > 1) {
				$sqlQuery .= ", ";
				$virgule--;
			}
		}
		if ($nom != "") {
			$sqlQuery .= "`nom` = '" . $nom . "'";
			if ($virgule > 1) {
				$sqlQuery .= ", ";
				$virgule--;
			}
		}
		if ($prenom != "") {
			$sqlQuery .= "`prenom` = '" . $prenom . "'";
			if ($virgule > 1) {
				$sqlQuery .= ", ";
				$virgule--;
			}
		}
		if ($email != "") {
			$sqlQuery .= "`email` = '" . $email . "'";
			if ($virgule > 1) {
				$sqlQuery .= ", ";
				$virgule--;
			}
		}
		if ($cp != "") {
			$sqlQuery .= "`code_postal` = '" . $cp . "'";
			if ($virgule > 1) {
				$sqlQuery .= ", ";
				$virgule--;
			}
		}
		if ($ville != "") {
			$sqlQuery .= "`ville` = '" . $ville . "'";
			if ($virgule > 1) {
				$sqlQuery .= ", ";
				$virgule--;
			}
		}
		if ($adresse != "") {
			$sqlQuery .= "`adresse` = '" . $adresse . "'";
			if ($virgule > 1) {
				$sqlQuery .= ", ";
				$virgule--;
			}
		}
		$sqlQuery .= " WHERE `id` = " . $verif["sessionId"];
		mysqli_query($this->sqlConnect, $sqlQuery);
		if (mysqli_errno($this->sqlConnect))
			throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
		return true;
	}

	function accountModMdp($mdp) {
		$verif = accountVerif();
		if ($verif["connect"] == FALSE)
			return FALSE;
		$mdpHash = hashCreate($mdp);
		mysqli_query($this->sql_connect, "UPDATE `" . $this->getConfigSqlTableUser() . "` SET `pass` = '" . $mdpHash . "' WHERE `id` = " . $verif["id_utilisateur"]);
		if (mysqli_errno($this->sqlConnect))
			throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
		return true;
	}

	function accountVerif() {
		@session_start();
		$sqlResult = mysqli_query($this->sqlConnect, "SELECT " . $this->getConfigSqlTableUser() . ".user, expire, " . $this->getConfigSqlTableUser() . ".id, email, nom, prenom, adresse, ville, code_postal FROM `" . $this->getConfigSqlTableSession() . "` JOIN `" . $this->getConfigSqlTableUser() . "` ON " . $this->getConfigSqlTableSession() . ".user = " . $this->getConfigSqlTableUser() . ".user WHERE session_id = '" . session_id() . "' AND expire > " . time());
		if (mysqli_errno($this->sqlConnect))
			throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
		while ($sqlRow = mysqli_fetch_array($sqlResult)) {
			return array(
				"connect" => true,
				"user" => $sqlRow["user"],
				"expire" => $sqlRow["expire"],
				"sessionId" => $sqlRow["id"],
				"email" => $sqlRow["email"],
				"nom" => $sqlRow["nom"],
				"prenom" => $sqlRow["prenom"],
				"adresse" => $sqlRow["adresse"],
				"ville" => $sqlRow["ville"],
				"code_postal" => $sqlRow["code_postal"]
			);
		}
		return array(
			"connect" => false
		);
	}

	function accountClearSession() {
		mysqli_query($this->sql_connect, "DELETE FROM `" . $this->getConfigSqlTableSession() . "` WHERE `expire` < " . time());
		if (mysqli_errno($this->sqlConnect))
			throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
		return true;
	}

	function accountRecoveryCreate($email, $user) {
		if ($email == "")
			throw new Exception("Email n'est pas renseignée.");
		if ($user == "")
			throw new Exception("User n'est pas renseignée.");
		$sqlResult = mysqli_query($this->sqlConnect, "SELECT * FROM `" . $this->getConfigSqlTableUser() . "` WHERE `user` LIKE '" . $user . "' AND `email` LIKE '" . $email . "'");
		if (mysqli_errno($this->sqlConnect))
			throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
		if (mysqli_fetch_array($sqlResult) != NULL) {
			$uniqid = md5(uniqid()) . md5(uniqid());
			$expire = time() + $this->getConfigRecoveryExpire();
			mysqli_query($this->sqlConnect, "INSERT INTO `" . $this->getConfigSqlTableRecovery() . "` (`id`, `token`, `user`, `expire`) VALUES (NULL, '" . $uniqid . "', '" . $user . "', '" . $expire . "')");
			if (mysqli_errno($this->sqlConnect))
				throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
			return $uniqid;
		} else {
			return NULL;
		}
	}

	function accountRecoveryUse($token) {
		if ($token == "")
			throw new Exception("Token n'est pas renseignée.");
		$sqlResult = mysqli_query($this->sqlConnect, "SELECT * FROM `" . $this->getConfigSqlTableRecovery() . "` WHERE `token` LIKE '" . $token . "'");
		if (mysqli_errno($this->sqlConnect))
			throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
		if ($sqlRow = mysqli_fetch_array($sqlResult) != NULL)
			return $sqlRow["user"];
		else
			return NULL;
	}

	function hashCreate($mdp) {
		if ($mdp == "")
			throw new Exception("Mdp n'est pas renseignée.");
		$seed = explode("-", $this->getConfigSeed());
		$mdpHash = hash("sha256", $mdp);
		$seedRand = "";
		for ($i = 1; $i <= 128; $i++)
			$seedRand .= mt_rand(0, 9);
		$mdpHash = hash("sha256", $mdpHash . $seedRand);
		$mdpSplit = str_split($mdpHash);
		for ($i = 0; $i <= 63; $i++) {
			if ($mdpSplit[$i] == "a")
				$mdpSplit[$i] = $seed[0];
			if ($mdpSplit[$i] == "b")
				$mdpSplit[$i] = $seed[1];
			if ($mdpSplit[$i] == "c")
				$mdpSplit[$i] = $seed[2];
			if ($mdpSplit[$i] == "d")
				$mdpSplit[$i] = $seed[3];
			if ($mdpSplit[$i] == "e")
				$mdpSplit[$i] = $seed[4];
			if ($mdpSplit[$i] == "f")
				$mdpSplit[$i] = $seed[5];
		}
		$mdpHash = $seedRand . implode("", $mdpSplit);
		return $mdpHash;
	}

	function hashVerif($mdp, $mdpVerif) {
		if ($mdp == "")
			throw new Exception("Mdp n'est pas renseignée.");
		if ($mdpVerif == "")
			throw new Exception("MdpVerif n'est pas renseignée.");
		$seed = explode("-", $this->getConfigSeed());
		$mdpHash = hash("sha256", $mdp);
		$seedRand = substr($mdpVerif, 0, 128);
		$mdpHash = hash("sha256", $mdpHash . $seedRand);
		$mdpSplit = str_split($mdpHash);
		for ($i = 0; $i <= 63; $i++) {
			if ($mdpSplit[$i] == "a")
				$mdpSplit[$i] = $seed[0];
			if ($mdpSplit[$i] == "b")
				$mdpSplit[$i] = $seed[1];
			if ($mdpSplit[$i] == "c")
				$mdpSplit[$i] = $seed[2];
			if ($mdpSplit[$i] == "d")
				$mdpSplit[$i] = $seed[3];
			if ($mdpSplit[$i] == "e")
				$mdpSplit[$i] = $seed[4];
			if ($mdpSplit[$i] == "f")
				$mdpSplit[$i] = $seed[5];
		}
		$mdpHash = $seedRand . implode("", $mdpSplit);
		if ($mdpVerif === $mdpHash)
			return true;
		else
			return false;
	}

	function createMdp() {
		$lettreConsonne = array(
			"b",
			"c",
			"d",
			"f",
			"g",
			"h",
			"j",
			"k",
			"l",
			"m",
			"n",
			"p",
			"q",
			"r",
			"s",
			"t",
			"v",
			"w",
			"x",
			"z"
		);
		$lettreVoyelle = array(
			"a",
			"e",
			"i",
			"o",
			"u",
			"y"
		);
		$lettreSpecial = array(
			"&",
			"(",
			"-",
			"_",
			")",
			"=",
			",",
			";",
			":",
			"!",
			"$",
			"*"
		);
		$i = 0;
		for ($i = 0; $i < 4; $i++) {
			if ($i == 0) {
				$rand = array_rand($lettreConsonne);
				$return = strtoupper($lettreConsonne[$rand]);
				$rand = array_rand($lettreVoyelle);
				$return .= $lettreVoyelle[$rand];
			}
			if ($i == 1 || $i == 2) {
				$rand = array_rand($lettreConsonne);
				$return .= $lettreConsonne[$rand];
				$rand = array_rand($lettreVoyelle);
				$return .= $lettreVoyelle[$rand];
			}
			if ($i == 3) {
				$rand = array_rand($lettreSpecial);
				$return .= $lettreSpecial[$rand];
				$return .= rand(0, 9);
			}
			$i++;
		}
		return $return;
	}
}

?>
