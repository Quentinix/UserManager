<?php
// phpcs:disable Generic.Files.LineLength

namespace Wave;

use Exception;

/**
 * Classe de la gestion des comptes
 *
 * @package  Wave
 * @author   Quentinix <git@quentinix.fr>
 */
class Account extends Config
{

    private $sqlConnect;
    
    /**
     * Appel de la connexion à la base de données
     */
    public function __construct() {
        $sqlClass = new Sql;
        $this->sqlConnect = $sqlClass->getSqlConnect();
    }

    /**
     * Permet la création d'un nouveau utilisateur
     * Retourne false si l'utilisateur existe déjà ou si la fonction ne trouve
     * pas suffisament de temps pour confirmer l'inscription de l'utilisateur
     * Retourne true si l'utilisateur a bien été créé
     *
     * @param string $user
     * @param string $pass
     * @param string $email
     * @param string $perso
     *
     * @throws Exception
     * @return boolean
     */
    public function accountCreate($user, $pass, $email = null, $perso = [])
    {
        if ($user == "") {
            throw new Exception("User n'est pas renseignée.");
        }
        if ($pass == "") {
            throw new Exception("Pass n'est pas renseignée.");
        }
        // $exeTimeBegin = time(); Lien avec le commentaire ligne 90
        $sqlResult = mysqli_query($this->sqlConnect, "SELECT * FROM " . $this->getConfigSqlTableUser() . " WHERE `user` LIKE '" . $user . "'");
        if (mysqli_errno($this->sqlConnect)) {
            throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
        }
        if (mysqli_fetch_array($sqlResult) != null) {
            return false;
        }
        $perso = json_encode($perso);
        $hash = new Hash;
        $passCrypt = $hash->hashCreate($pass);
        $user_norm = normalizer_normalize($user);
        mysqli_query($this->sqlConnect, "INSERT INTO `" . $this->getConfigSqlTableUser() . "` (`id`, `user`, `user_norm`, `pass`, `email`, `perso`) VALUES (NULL, '" . $user . "', '" . $user_norm . "', '" . $passCrypt . "', '" . $email . "', '" . $perso . "')");
        if (mysqli_errno($this->sqlConnect)) {
            throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
        }
        return true;
        // while (true) {
        //     $sqlResult = mysqli_query($this->sqlConnect, "SELECT * FROM `" . $this->getConfigSqlTableUser() . "` WHERE `user` LIKE '" . $user . "'");
        //     if (mysqli_errno($this->sqlConnect)) {
        //         throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
        //     }
        //     if (mysqli_fetch_array($sqlResult) != null) {
        //         return true;
        //     }
        //     sleep(1);
        //     if (ini_get("max_execution_time") > time() - $exeTimeBegin - 2) {
        //         return false;
        //     }
        // } Partie en test
    }

    /**
     * Permet la suppression d'un compte d'utilisateur
     * Retourne false si le compte a pas été supprimé
     * Retourne true si le compte a été supprimé
     *
     * @param String $user
     *
     * @throws Exception
     * @return boolean
     */
    public function accountDelete($user)
    {
        if ($user == "") {
            throw new Exception("User n'est pas renseignée.");
        }
        mysqli_query($this->sqlConnect, "DELETE FROM `" . $this->getConfigSqlTableUser() . "` WHERE `user` = '" . $user . "'");
        if (mysqli_errno($this->sqlConnect)) {
            throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
        }
        if (mysqli_affected_rows($this->sqlConnect) == 0) {
            return false;
        }
        return true;
    }

    /**
     *
     */
    public function accountUsersList()
    {
        $return = [];
        $sqlResult = mysqli_query($this->sqlConnect, "SELECT user FROM " . $this->getConfigSqlTableUser);
        while ($sqlRow = mysqli_fetch_array($sqlResult)) {
            $return[] = $sqlRow["user"];
        }
        return $return;
    }

    /**
     *
     */
    public function accountUserDetail($user)
    {
        $sqlResult = mysqli_query($this->sqlConnect, "SELECT user FROM " . $this->getConfigSqlTableUser . " WHERE `user` LIKE '" . $user . "'");
        while ($sqlRow = mysqli_fetch_array($sqlResult)) {
            $return["permission"] = $sqlRow["permission"];
            $return["email"] = json_decode($sqlRow["ip_access"]);
            $return["try"] = $sqlRow["try"];
            $return["perso"] = json_decode($sqlRow["try"]);
        }
    }

    /**
     * Permet la connexion de l'utilisateur
     * Retourne 2 si le nombre de tentative est atteint
     * Retourne 1 si le mot de passe est erroné ou si la fonction ne trouve
     * pas suffisament de temps pour confirmer la connexion de l'utilisateur
     * Retourne 0 si l'utilisateur est bien connecté
     *
     * @param string $user
     * @param string $pass
     *
     * @throws Exception
     * @return integer
     */
    public function accountConnect($user, $pass, $loginEver)
    {
        if ($user == "") {
            throw new Exception("User n'est pas renseignée.");
        }
        if ($pass == "") {
            throw new Exception("Pass n'est pas renseignée.");
        }
        // $exeTimeBegin = time(); Lien avec le commentaire ligne 171
        $user_norm = normalizer_normalize($user);
        $sqlResult = mysqli_query($this->sqlConnect, "SELECT id, user_norm, pass, try, ip_access FROM `" . $this->getConfigSqlTableUser() . "` WHERE `user` LIKE '" . $user_norm . "'");
        if (mysqli_errno($this->sqlConnect)) {
            throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
        }
        while ($sqlRow = mysqli_fetch_array($sqlResult)) {
            if ($sqlRow["try"] >= $this->getConfigMaxTry()) {
                return 2;
            }
            $passVerif = $sqlRow["pass"];
            $userId = $sqlRow["id"];
            $ip_access = json_decode($sqlRow["ip_access"]);
        }
        if (array_search($_SERVER["REMOTE_ADDR"], $ip_access) !== FALSE) {
            return 3;
        }
        if (! isset($passVerif)) {
            mysqli_query($this->sqlConnect, "UPDATE `" . $this->getConfig() . "` SET `try` = `try` + 1 WHERE `" . $this->getConfig() . "`.`user` LIKE " . $user . ";");
            if (mysqli_errno($this->sqlConnect)) {
                throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
            }
            return 1;
        }
        $hash = new Hash;
        if ($hash->hashVerif($pass, $passVerif)) {
            @session_start();
            session_regenerate_id();
            $expire = time() + $this->getConfigSessionExpire();
            mysqli_query($this->sqlConnect, "INSERT INTO `" . $this->getConfigSqlTableSession() . "` (`id`, `user_id`, `session_id`,`ip`, `expire`, `loginEver`) VALUES (NULL, '" . $userId . "', '" . session_id() . "', '" . $_SERVER["REMOTE_ADDR"] . "', '" . $expire . "', '" . $loginEver . "')");
            if (mysqli_errno($this->sqlConnect)) {
                throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
            }
            return 0;
            // while (true) {
            //     $sqlResult = mysqli_query($this->sqlConnect, "SELECT * FROM `" . $this->getConfigSqlTableSession() . "` WHERE `user_id` LIKE '" . $userId . "'");
            //     if (mysqli_errno($this->sqlConnect)) {
            //         throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
            //     }
            //     if (mysqli_fetch_array($sqlResult) != null) {
            //         return true;
            //     }
            //     sleep(1);
            //     if (ini_get("max_execution_time") > time() - $exeTimeBegin - 2) {
            //         return false;
            //     }
            // } partie en test
        }
        return 1;
    }

    /**
     * 
     */
    public function accountUnblock($user)
    {
        mysqli_query($this->sqlConnect, "UPDATE `" . $this->getConfigSqlTableUser . "` SET `try` = '0' WHERE `user` = " . $user);
    }

    /**
     * Permet déconnexion de l'utilisateur
     * Retourne toujours true si la requête SQL ne possède pas de problème
     *
     * @throws Exception
     * @return boolean
     */
    public function accountDisconnect()
    {
        @session_start();
        mysqli_query($this->sqlConnect, "DELETE FROM `" . $this->getConfigSqlTableSession() . "` WHERE `session_id` = '" . session_id() . "'");
        if (mysqli_errno($this->sqlConnect)) {
            throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
        }
        session_regenerate_id();
        return true;
    }

    /**
     * Permet de modifier les informations de l'utilisateur qui est connecté
     * Retourne false si la modification du nom d'utilisateur existe déjà
     * Retourne true si les modifications sont bien enregistrées
     *
     * @param string $user
     * @param string $email
     * @param array  $perso
     *
     * @throws Exception
     * @return boolean
     */
    public function accountMod($user, $email = NULL, $perso = [])
    {
        $sqlResult = mysqli_query($this->sqlConnect, "SELECT perso, email FROM " . $this->getConfigSqlTableUser() . " WHERE `user` LIKE '" . $user . "'");
        if (mysqli_errno($this->sqlConnect)) {
            throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
        }
        if ($sqlRow = mysqli_fetch_array($sqlResult) == NULL) {
            return false;
        } else {
            $persoBase = json_decode($sqlRow["perso"]);
            if ($email === NULL) {
                $email = $sqlRow["email"];
            }
        }
        foreach($perso as $key => $value) {
            $persoBase[$key] = $value;
        }
        $perso = json_encode($persoBase);
        $sqlQuery = "UPDATE `" . $this->getConfigSqlTableUser() . "` SET (email, perso) VALUE ('" . $email . "', '" . $perso . "') WHERE `user` LIKE " . $user;
        mysqli_query($this->sqlConnect, $sqlQuery);
        if (mysqli_errno($this->sqlConnect)) {
            throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
        }
        return true;
    }

    /**
     * Permet de modifier le mot de passe de l'utilisateur connecté
     * Retourne false si l'utilisateur n'est pas connecté
     * Retourne true si le mot de passe est bien enregistré
     *
     * @param string $mdp
     *
     * @throws Exception
     * @return boolean
     */
    public function accountModMdp($mdp)
    {
        $verif = $this->accountVerif();
        if ($verif["connect"] == false) {
            return false;
        }
        $hash = new Hash;
        $mdpHash = $hash->hashCreate($mdp);
        mysqli_query($this->sqlConnect, "UPDATE `" . $this->getConfigSqlTableUser() . "` SET `pass` = '" . $mdpHash . "' WHERE `id` = " . $verif["userId"]);
        if (mysqli_errno($this->sqlConnect)) {
            throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
        }
        return true;
    }

    /**
     * Permet de modifier le niveau de permission d'un utilisateur.
     * Retourne false si le niveau de permission n'est pas attribué.
     * Retourne true si le niveau de permission est assigné.
     *
     * @param string  $user
     * @param integer $permission
     *
     * @throws Exception
     * @return boolean
     */
    public function accountUpdatePerm($user, $permission)
    {
        if ($user == "") {
            throw new Exception("User n'est pas renseignée.");
        }
        if ($permission === "") {
            throw new Exception("Permission n'est pas renseignée.");
        }
        mysqli_query($this->sqlConnect, "UPDATE `" . $this->getConfigSqlTableUser() . "` SET `permission` = '" . $permission . "' WHERE `user` = '" . $user . "'");
        if (mysqli_errno($this->sqlConnect)) {
            throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
        }
        if (mysqli_affected_rows($this->sqlConnect) == 0) {
            return false;
        }
        return true;
    }

    /**
     * Permet de renvoyer un tableau des informations de l'utilisateur connecté
     *
     * @throws Exception
     * @return boolean[]|mixed[]
     */
    public function accountVerif()
    {
        @session_start();
        $sqlResult = mysqli_query($this->sqlConnect, "SELECT " . $this->getConfigSqlTableUser() . ".user, user_id, permission, expire, " . $this->getConfigSqlTableUser() . ".id, email, nom, prenom, adresse, ville, code_postal FROM `" . $this->getConfigSqlTableSession() . "` JOIN `" . $this->getConfigSqlTableUser() . "` ON " . $this->getConfigSqlTableSession() . ".user_id = " . $this->getConfigSqlTableUser() . ".id WHERE session_id = '" . session_id() . "' AND (expire > " . time() . " OR loginEver = 1) AND ip = '" . $_SERVER["REMOTE_ADDR"] . "'");
        if (mysqli_errno($this->sqlConnect)) {
            throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
        }
        while ($sqlRow = mysqli_fetch_array($sqlResult)) {
            return array(
                "connect" => true,
                "user" => $sqlRow["user"],
                "permission" => $sqlRow["permission"],
                "expire" => $sqlRow["expire"],
                "sessionId" => $sqlRow["id"],
                "userId" => $sqlRow["user_id"],
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

    /**
     * Permet de retourner une valeur boolean selon la permission de
     * l'utilisateur
     * Retourne false si l'utilisateur n'est pas connecté ou si il ne possède
     * pas suffisament de permission
     * Retourne true si l'utilisateur possède suffisament de permission
     *
     * @param integer $minPerm
     *
     * @return boolean
     */
    public function accountVerifPerm($minPerm)
    {
        $verif = $this->accountVerif();
        if ($verif["connect"] != true) {
            return false;
        } elseif ($verif["permission"] >= $minPerm) {
            return true;
        }
        return false;
    }

    /**
     *
     */
    public function accountVerifPermReverse($maxPerm)
    {
        $verif = $this->accountVerif();
        if ($verif["connect"] != true) {
            return false;
        } elseif ($verif["permission"] <= $maxPerm) {
            return true;
        }
        return false;
    }

    /**
     * Permet la suppression des sessions expirées dans la base de données
     * Retourne toujours true si la requête SQL est correctement executée
     *
     * @throws Exception
     * @return boolean
     */
    public function accountClearSession()
    {
        mysqli_query($this->sqlConnect, "DELETE FROM `" . $this->getConfigSqlTableSession() . "` WHERE `expire` < " . time());
        if (mysqli_errno($this->sqlConnect)) {
            throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
        }
        return true;
    }

    /**
     * Permet la création d'un jeton pour la récupération d'un compte
     * d'utilisateur
     *
     * @param string $email
     * @param string $user
     *
     * @throws Exception
     * @return string|NULL
     */
    public function accountRecoveryCreate($email, $user)
    {
        if ($email == "") {
            throw new Exception("Email n'est pas renseignée.");
        }
        if ($user == "") {
            throw new Exception("User n'est pas renseignée.");
        }
        $sqlResult = mysqli_query($this->sqlConnect, "SELECT id FROM `" . $this->getConfigSqlTableUser() . "` WHERE `user` LIKE '" . $user . "' AND `email` LIKE '" . $email . "'");
        if (mysqli_errno($this->sqlConnect)) {
            throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
        }
        while ($sqlRow = mysqli_fetch_array($sqlResult)) {
            $uniqid = md5(uniqid()) . md5(uniqid());
            $expire = time() + $this->getConfigRecoveryExpire();
            mysqli_query($this->sqlConnect, "INSERT INTO `" . $this->getConfigSqlTableRecovery() . "` (`id`, `token`, `user_id`, `expire`) VALUES (NULL, '" . $uniqid . "', '" . $sqlRow["id"] . "', '" . $expire . "')");
            if (mysqli_errno($this->sqlConnect)) {
                throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
            }
            return $uniqid;
        }
        return null;
    }

    /**
     * Permet la confirmation du propriétaire du compte d'utilisateur
     *
     * @param string $token
     *
     * @throws Exception
     * @return string|NULL
     */
    public function accountRecoveryUse($token)
    {
        if ($token == "") {
            throw new Exception("Token n'est pas renseignée.");
        }
        $sqlResult = mysqli_query($this->sqlConnect, "SELECT * FROM `" . $this->getConfigSqlTableRecovery() . "` WHERE `token` LIKE '" . $token . "'");
        if (mysqli_errno($this->sqlConnect)) {
            throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
        }
        if ($sqlRow = mysqli_fetch_array($sqlResult) != null) {
            mysqli_query($this->sqlConnect, "DELETE FROM `" . $this->getConfigSqlTableRecovery() . "` WHERE `id` = '" . $sqlRow["id"] . "'");
            if (mysqli_errno($this->sqlConnect)) {
                throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
            }
            return $sqlRow["user"];
        }
        return null;
    }

    /**
     * Permet l'ajout d'un label à un niveau de permission
     * Retourne false si un label existe déjà sur le niveau de permission choisi
     * Retourn true si le label est bien attribué au niveau de permission
     *
     * @param integer $level
     * @param string  $name
     *
     * @throws Exception
     * @return boolean
     */
}
