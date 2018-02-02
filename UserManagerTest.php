<?php
// phpcs:disable Generic.Files.LineLength

namespace UserManager;

// require "UserManager.php";

use UserManager\UserManager;
use PHPUnit\Framework\TestCase;

/**
 * Class des tests PHPUnit de la class UserManager
 *
 * @package  UserManager
 * @author   Quentinix <git@quentinix.fr>
 */
class UserManagerTest extends TestCase
{
    // phpcs:disable PEAR.Commenting

    public function testPHPUnit()
    {
        $this->assertSame(true, true);
        $_SERVER["REMOTE_ADDR"] = "127.0.0.100";
    }

    public function testAccountCreateSansUserEtPass()
    {
        $this->expectExceptionMessage("User n'est pas renseignée.");
        $testClass = new UserManager();
        $testClass->accountCreate("", "");
    }

    public function testAccountCreateSansUser()
    {
        $this->expectExceptionMessage("User n'est pas renseignée.");
        $testClass = new UserManager();
        $testClass->accountCreate("", "Mot de passe");
    }

    public function testAccountCreateSansPass()
    {
        $this->expectExceptionMessage("Pass n'est pas renseignée.");
        $testClass = new UserManager();
        $testClass->accountCreate("Nom d'utilisateur", "");
    }

    public function testAccountCreate()
    {
        $testClass = new UserManager();
        $this->assertSame(true, $testClass->accountCreate("PHPUnitUser", "MonMotDePasse"));
    }

    public function testAccountUpdatePermSansUserEtPermission()
    {
        $this->expectExceptionMessage("User n'est pas renseignée.");
        $testClass = new UserManager();
        $testClass->accountUpdatePerm("", "");
    }

    public function testAccountUpdatePermSansUser()
    {
        $this->expectExceptionMessage("User n'est pas renseignée.");
        $testClass = new UserManager();
        $testClass->accountUpdatePerm("", 42);
    }

    public function testAccountUpdatePermSansPermission()
    {
        $this->expectExceptionMessage("Permission n'est pas renseignée.");
        $testClass = new UserManager();
        $testClass->accountUpdatePerm("Utilisateur", "");
    }

    public function testAccountUpdatePermUtilisateurInconnue()
    {
        $testClass = new UserManager();
        $this->assertSame(false, $testClass->accountUpdatePerm("UtilisateurQuiExistePas", 42));
    }

    public function testAccountUpdatePermUtilisateur()
    {
        $testClass = new UserManager();
        $this->assertSame(true, $testClass->accountUpdatePerm("PHPUnitUser", 42));
    }

    public function testAccountCreateDoublon()
    {
        $testClass = new UserManager();
        $this->assertSame(false, $testClass->accountCreate("PHPUnitUser", "MonMotDePasse"));
    }

    public function testAccountModSansConnexion()
    {
        $testClass = new UserManager();
        $this->assertSame(false, $testClass->accountMod("test"));
    }

    public function testAccountRecoveryCreateSansEmailEtUser()
    {
        $this->expectExceptionMessage("Email n'est pas renseignée.");
        $testClass = new UserManager();
        $testClass->accountRecoveryCreate("", "");
    }

    public function testAccountRecoveryCreateSansEmail()
    {
        $this->expectExceptionMessage("Email n'est pas renseignée.");
        $testClass = new UserManager();
        $testClass->accountRecoveryCreate("", "Utilisateur");
    }

    public function testAccountRecoveryCreateSansUser()
    {
        $this->expectExceptionMessage("User n'est pas renseignée.");
        $testClass = new UserManager();
        $testClass->accountRecoveryCreate("MonemailpasBesoinArobase", "");
    }

    public function testAccountRecoveryCreateSansConnexion()
    {
        $testClass = new UserManager();
        $this->assertSame(null, $testClass->accountRecoveryCreate("phpunit@testclass.net", "PHPUnitUser"));
    }

    public function testAccountVerifSansConnexion()
    {
        $testClass = new UserManager();
        $this->assertArrayNotHasKey("user", $testClass->accountVerif());
    }
    
    public function testAccountModMdpSansConnexion()
    {
        $testClass = new UserManager();
        $this->assertSame(false, $testClass->accountModMdp("AutreMotDePasse"));
    }

    public function testAccountConnectSansUserEtPass()
    {
        $this->expectExceptionMessage("User n'est pas renseignée.");
        $testClass = new UserManager();
        $testClass->accountConnect("", "");
    }

    public function testAccountConnectSansUser()
    {
        $this->expectExceptionMessage("User n'est pas renseignée.");
        $testClass = new UserManager();
        $testClass->accountConnect("", "Mot de passe");
    }

    public function testAccountConnectSansPass()
    {
        $this->expectExceptionMessage("Pass n'est pas renseignée.");
        $testClass = new UserManager();
        $testClass->accountConnect("Nom d'utilisateur", "");
    }

    public function testAccountConnectMauvaisMotDePasse()
    {
        $testClass = new UserManager();
        $this->assertSame(false, $testClass->accountConnect("PHPUnitUser", "PasLeBonMotDePasse"));
    }

    public function testAccountVerifPermSansConnexionPasPermission()
    {
        $testClass = new UserManager();
        $this->assertSame(false, $testClass->accountVerifPerm(52));
    }

    public function testAccountVerifPermSansConnexionAvecPermission()
    {
        $testClass = new UserManager();
        $this->assertSame(false, $testClass->accountVerifPerm(32));
    }

    public function testAccountConnect()
    {
        $testClass = new UserManager();
        $this->assertSame(true, $testClass->accountConnect("PHPUnitUser", "MonMotDePasse"));
    }

    public function testAccountModMdpUn()
    {
        $testClass = new UserManager();
        $this->assertSame(true, $testClass->accountModMdp("AutreMotDePasse"));
    }

    public function testAccountModMdpDeux()
    {
        $testClass = new UserManager();
        $this->assertSame(true, $testClass->accountModMdp("MonMotDePasse"));
    }

    public function testAccountVerifPermAvecConnexionSansPermission()
    {
        $testClass = new UserManager();
        $this->assertSame(false, $testClass->accountVerifPerm(52));
    }

    public function testAccountVerifPermAvecConnexionAvecPermission()
    {
        $testClass = new UserManager();
        $this->assertSame(true, $testClass->accountVerifPerm(32));
    }

    public function testAccountVerifAvecConnexion()
    {
        $testClass = new UserManager();
        $this->assertArrayHasKey("user", $testClass->accountVerif());
    }

    public function testAccountVerifAvecConnexionMauvaisIP()
    {
        $testClass = new UserManager();
        $vraisIP = $_SERVER["REMOTE_ADDR"];
        $_SERVER["REMOTE_ADDR"] = "999.999.999.999";
        $this->assertArrayNotHasKeY("user", $testClass->accountVerif());
        $_SERVER["REMOTE_ADDR"] = $vraisIP;
    }

    public function testAccountRecoveryCreateAvecConnexionMauvaisEmail()
    {
        $testClass = new UserManager();
        $this->assertSame(null, $testClass->accountRecoveryCreate("phpunit@testclass.net", "PHPUnitUser"));
    }

    public function testAccountModAvecConnexion()
    {
        $testClass = new UserManager();
        $this->assertSame(true, $testClass->accountMod("PHPUnitUser2", "", "", "phpunit@testclass.net"));
    }

    public function testAccountRecoveryUseSansToken()
    {
        $this->expectExceptionMessage("Token n'est pas renseignée.");
        $testClass = new UserManager();
        $testClass->accountRecoveryUse("");
    }

    public function testAccountRecoveryCreateAvecConnexion()
    {
        $testClass = new UserManager();
        $resultTest = $testClass->accountRecoveryCreate("phpunit@testclass.net", "PHPUnitUser2");
        $this->assertNotSame(false, $resultTest);
        return $resultTest;
    }

    /**
     * Dépendance de la fonction testAccountRecoveryCreateAvecConnexion
     *
     * @depends testAccountRecoveryCreateAvecConnexion
     */
    public function testAccountRecoveryUse($resultTestAfter)
    {
        $testClass = new UserManager();
        $resultTest = $testClass->accountRecoveryUse($resultTestAfter);
        $this->assertNotSame(false, $resultTest);
        return $resultTest;
    }

    /**
     * Dépendance de la fonction testAccountRecoveryCreateAvecConnexion
     *
     * @depends testAccountRecoveryCreateAvecConnexion
     */
    public function testAccountRecoveryUseDoublon($resultTestAfter)
    {
        $testClass = new UserManager();
        $this->assertSame(null, $testClass->accountRecoveryUse($resultTestAfter));
    }

    public function testAccountDisconnect()
    {
        $testClass = new UserManager();
        $this->assertSame(true, $testClass->accountDisconnect());
    }

    public function testAccountModMdpApresDeconnexion()
    {
        $testClass = new UserManager();
        $this->assertSame(false, $testClass->accountModMdp("AutreMotDePasse"));
    }

    public function testAccountDeleteSansUser()
    {
        $this->expectExceptionMessage("User n'est pas renseignée.");
        $testClass = new UserManager();
        $testClass->accountDelete("");
    }

    public function testAccountDeleteUtilisateurQuiExistePas()
    {
        $testClass = new UserManager();
        $this->assertSame(false, $testClass->accountDelete("QuiExistePas"));
    }

    public function testAccountDelete()
    {
        $testClass = new UserManager();
        $this->assertSame(true, $testClass->accountDelete("PHPUnitUser2"));
    }

    public function testAccountDeleteDoublon()
    {
        $testClass = new UserManager();
        $this->assertSame(false, $testClass->accountDelete("PHPUnitUser2"));
    }

    public function testAccountVerifPermApresDeconnexionSansPermission()
    {
        $testClass = new UserManager();
        $this->assertSame(false, $testClass->accountVerifPerm(52));
    }

    public function testAccountVerifPermApresDeconnexionAvecPermission()
    {
        $testClass = new UserManager();
        $this->assertSame(false, $testClass->accountVerifPerm(32));
    }

    public function testAccountVerifSansConnexionApresDeconnexion()
    {
        $testClass = new UserManager();
        $this->assertArrayNotHasKey("user", $testClass->accountVerif());
    }

    public function testAccountModSansConnexionApresDeconnexion()
    {
        $testClass = new UserManager();
        $this->assertSame(false, $testClass->accountMod("test"));
    }

    public function testAccountRecoveryCreateSansConnexionApresDeconnexion()
    {
        $testClass = new UserManager();
        $this->assertSame(null, $testClass->accountRecoveryCreate("phpunit@testclass.net", "PHPUnitUser"));
    }
    
    public function testPermissionAdd()
    {
        $testClass = new UserManager();
        $this->assertSame(true, $testClass->permissionAdd(3042, "Label du level ultimator !"));
    }
    
    public function testPermissionAddDoublon()
    {
        $testClass = new UserManager();
        $this->assertSame(false, $testClass->permissionAdd(3042, "XxxLabel du level ultimator !xxX"));
    }
    
    public function testPermissionGet()
    {
        $testClass = new UserManager();
        $this->assertSame("Label du level ultimator !", $testClass->permissionGet(3042));
    }
    
    public function testPermissionGetExistePas()
    {
        $testClass = new UserManager();
        $this->assertSame("", $testClass->permissionGet(3043));
    }
    
    public function testPermissionRemove()
    {
        $testClass = new UserManager();
        $this->assertSame(true, $testClass->permissionRemove(3042));
    }
    
    public function testPermissionGetApresSuppression()
    {
        $testClass = new UserManager();
        $this->assertSame("", $testClass->permissionGet(3042));
    }

    public function testAccountClearSession()
    {
        $testClass = new UserManager();
        $this->assertSame(true, $testClass->accountClearSession());
    }
}
