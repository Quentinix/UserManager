<?php
use UserManager\SqlConfig;
require 'UserManager/SqlConfig.php';

class UserManager extends SqlConfig {
	
	private $sqlConfig;
	private $sqlConnect;

	function __construct() {
		$this->sqlConnect = mysqli_connect($this->getHost(), $this->getUser(), $this->getPass(), $this->getDb());
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
		$sqlResult = mysqli_query($this->sqlConnect, "SELECT * FROM " . $this->getTableUser() . " WHERE `user` LIKE '" . $user . "'");
		if (mysqli_errno($this->sqlConnect))
			throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
		if (mysqli_fetch_array($sqlResult) != NULL)
			return false;
		$passCrypt = $this->hashCrypt($pass);
		mysqli_query($this->sqlConnect, "INSERT INTO `" . $this->getTableUser() . "` (`id`, `user`, `pass`, `email`, `nom`, `prenom`, `adresse`, `ville`, `code_postal`) VALUES (NULL, '" . $user . "', '" . $pass_crypt . "', '" . $email . "', '" . $nom . "', '" . $prenom . "', '" . $adresse . "', '" . $ville . "', '" . $code_postal . "')");
		if (mysqli_errno($this->sqlConnect))
			throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
		while (true) {
			$sqlResult = mysqli_query($this->sqlConnect, "SELECT * FROM `" . $this->getTableUser() . "` WHERE `user` LIKE '" . $user . "'");
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
		$sqlResult = mysqli_query($this->sql_connect, "SELECT user, pass FROM `" . $this->getTableUser() . "` WHERE `user` LIKE '" . $user . "'");
		if (mysqli_errno($this->sqlConnect))
			throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
		if ($sqlRow = mysqli_fetch_array($sqlResult) != NULL)
			$passVerif = $sqlRow["pass"];
		else
			return false;
		if ($this->hashVerif($pass, $passVerif)) {
			session_start();
			session_regenerate_id();
			$expire = time() + $this->getSessionExpire();
			mysqli_query($this->sql_connect, "INSERT INTO `" . $this->getTableSession() . "` (`id`, `user`, `session_id`, `expire`) VALUES (NULL, '" . $user . "', '" . session_id() . "', '" . $expire . "')");
			if (mysqli_errno($this->sqlConnect))
				throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
			while (true) {
				$sqlResult = mysqli_query($this->sql_connect, "SELECT * FROM `" . $this->getTableSession() . "` WHERE `user` LIKE '" . $user . "'");
				if (mysqli_errno($this->sqlConnect))
					throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
				if (mysqli_fetch_array($sqlResult) != Null)
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
		session_start();
		mysqli_query($this->sql_connect, "DELETE FROM `" . $this->getConfigSqlTableSession() . "` WHERE `session_id` = " . session_id());
		if (mysqli_errno($this->sqlConnect))
			throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
		session_regenerate_id();
		return true;
	}

	function accountMod($user, $nom, $prenom, $email, $cp, $ville, $adresse) {
		$verif = accountVerif();
		if ($verif["connect"] == FALSE)
			return FALSE;
		$virgule = 0;
		if ($user != "")
			$virgule++;
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
			if ($virgule > 0) {
				$sqlQuery .= ", ";
				$virgule--;
			}
		}
		if ($nom != "") {
			$sqlQuery .= "`nom` = '" . $nom . "'";
			if ($virgule > 0) {
				$sqlQuery .= ", ";
				$virgule--;
			}
		}
		if ($prenom != "") {
			$sqlQuery .= "`prenom` = '" . $prenom . "'";
			if ($virgule > 0) {
				$sqlQuery .= ", ";
				$virgule--;
			}
		}
		if ($email != "") {
			$sqlQuery .= "`email` = '" . $email . "'";
			if ($virgule > 0) {
				$sqlQuery .= ", ";
				$virgule--;
			}
		}
		if ($cp != "") {
			$sqlQuery .= "`code_postal` = '" . $cp . "'";
			if ($virgule > 0) {
				$sqlQuery .= ", ";
				$virgule--;
			}
		}
		if ($ville != "") {
			$sqlQuery .= "`ville` = '" . $ville . "'";
			if ($virgule > 0) {
				$sqlQuery .= ", ";
				$virgule--;
			}
		}
		if ($adresse != "") {
			$sqlQuery .= "`adresse` = '" . $adresse . "'";
			if ($virgule > 0) {
				$sqlQuery .= ", ";
				$virgule--;
			}
		}
		$sqlQuery .= " WHERE `id` = '" . $verif["id_utilisateur"] . "'";
		mysqli_query($this->sql_connect, $sqlQuery);
		if (mysqli_errno($this->sqlConnect))
			throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
		return true;
	}

	function accountModMdp(string $mdp) {
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
		session_start();
		$sqlResult = mysqli_query($this->sql_connect, "SELECT " . $this->getConfigSqlTableUser() . ".user, expire, email, nom, prenom, adresse, ville, code_postal FROM `" . $this->getConfigSqlTableSession() . "` JOIN `" . $this->getConfigSqlTableUser() . "` ON " . $this->getConfigSqlTableSession() . ".user = " . $this->getConfigSqlTableUser() . ".user WHERE session_id = '" . session_id() . "' AND expire > " . time());
		if (mysqli_errno($this->sqlConnect))
			throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
		if ($sqlRow = mysqli_fetch_array($sqlResult) != NULL) {
			return array(
				"connect" => true,
				"user" => $sqlRow["user"],
				"expire" => $sqlRow["expire"],
				"email" => $sqlRow["email"],
				"nom" => $sqlRow["nom"],
				"prenom" => $sqlRow["prenom"],
				"adresse" => $sqlRow["adresse"],
				"ville" => $sqlRow["ville"],
				"code_postal" => $sqlRow["code_postal"]
			);
		} else {
			return array(
				"connect" => false
			);
		}
	}

	function accountClearSession() {
		mysqli_query($this->sql_connect, "DELETE FROM `" . $this->getConfigSqlTableSession() . "` WHERE `expire` < " . time());
		if (mysqli_errno($this->sqlConnect))
			throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
		return true;
	}

}

?>
