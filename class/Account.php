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
     * @param string  $user
     * @param string  $pass
     * @param string  $email
     * @param string  $nom
     * @param string  $prenom
     * @param string  $adresse
     * @param string  $ville
     * @param integer $code_postal
     *
     * @throws Exception
     * @return boolean
     */
    public function accountCreate($user, $pass, $email = null, $nom = null, $prenom = null, $adresse = null, $ville = null, $code_postal = null)
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
        $hash = new Hash;
        $passCrypt = $hash->hashCreate($pass);
        mysqli_query($this->sqlConnect, "INSERT INTO `" . $this->getConfigSqlTableUser() . "` (`id`, `user`, `pass`, `email`, `nom`, `prenom`, `adresse`, `ville`, `code_postal`) VALUES (NULL, '" . $user . "', '" . $passCrypt . "', '" . $email . "', '" . $nom . "', '" . $prenom . "', '" . $adresse . "', '" . $ville . "', '" . $code_postal . "')");
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
     * Permet la connexion de l'utilisateur
     * Retourne false si le mot de passe est erroné ou si la fonction ne trouve
     * pas suffisament de temps pour confirmer la connexion de l'utilisateur
     * Retourne true si l'utilisateur est bien connecté
     *
     * @param string $user
     * @param string $pass
     *
     * @throws Exception
     * @return boolean
     */
    public function accountConnect($user, $pass)
    {
        if ($user == "") {
            throw new Exception("User n'est pas renseignée.");
        }
        if ($pass == "") {
            throw new Exception("Pass n'est pas renseignée.");
        }
        // $exeTimeBegin = time(); Lien avec le commentaire ligne 171
        $sqlResult = mysqli_query($this->sqlConnect, "SELECT id, user, pass FROM `" . $this->getConfigSqlTableUser() . "` WHERE `user` LIKE '" . $user . "'");
        if (mysqli_errno($this->sqlConnect)) {
            throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
        }
        while ($sqlRow = mysqli_fetch_array($sqlResult)) {
            $passVerif = $sqlRow["pass"];
            $userId = $sqlRow["id"];
        }
        if (! isset($passVerif)) {
            return false;
        }
        $hash = new Hash;
        if ($hash->hashVerif($pass, $passVerif)) {
            @session_start();
            session_regenerate_id();
            $expire = time() + $this->getConfigSessionExpire();
            mysqli_query($this->sqlConnect, "INSERT INTO `" . $this->getConfigSqlTableSession() . "` (`id`, `user_id`, `session_id`,`ip`, `expire`) VALUES (NULL, '" . $userId . "', '" . session_id() . "', '" . $_SERVER["REMOTE_ADDR"] . "', '" . $expire . "')");
            if (mysqli_errno($this->sqlConnect)) {
                throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
            }
            return true;
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
        return false;
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
     * @param string $nom
     * @param string $prenom
     * @param string $email
     * @param string $cp
     * @param string $ville
     * @param string $adresse
     *
     * @throws Exception
     * @return boolean
     */
    public function accountMod($user = "", $nom = "", $prenom = "", $email = "", $cp = "", $ville = "", $adresse = "")
    {
        $verif = $this->accountVerif();
        if ($verif["connect"] == false) {
            return false;
        }
        $virgule = 0;
        if ($user != "") {
            $sqlResult = mysqli_query($this->sqlConnect, "SELECT * FROM " . $this->getConfigSqlTableUser() . " WHERE `user` LIKE '" . $user . "'");
            if (mysqli_errno($this->sqlConnect)) {
                throw new Exception("Echec requête SQL : " . mysqli_errno($this->sqlConnect) . " : " . mysqli_error($this->sqlConnect));
            }
            if (mysqli_fetch_array($sqlResult) != null) {
                return false;
            }
            $virgule++;
        }
        if ($nom != "") {
            $virgule++;
        }
        if ($prenom != "") {
            $virgule++;
        }
        if ($email != "") {
            $virgule++;
        }
        if ($cp != "") {
            $virgule++;
        }
        if ($ville != "") {
            $virgule++;
        }
        if ($adresse != "") {
            $virgule++;
        }
        if ($virgule == 0) {
            return false;
        }
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
        $sqlResult = mysqli_query($this->sqlConnect, "SELECT " . $this->getConfigSqlTableUser() . ".user, user_id, permission, expire, " . $this->getConfigSqlTableUser() . ".id, email, nom, prenom, adresse, ville, code_postal FROM `" . $this->getConfigSqlTableSession() . "` JOIN `" . $this->getConfigSqlTableUser() . "` ON " . $this->getConfigSqlTableSession() . ".user_id = " . $this->getConfigSqlTableUser() . ".id WHERE session_id = '" . session_id() . "' AND expire > " . time() . " AND ip = '" . $_SERVER["REMOTE_ADDR"] . "'");
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