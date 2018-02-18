<?php
// phpcs:disable Generic.Files.LineLength

namespace Wave;

// require "Wave.php";

use Wave\Wave;
use PHPUnit\Framework\TestCase;

/**
 * Class des tests PHPUnit de la class Wave
 *
 * @package  Wave
 * @author   Quentinix <git@quentinix.fr>
 */
class WaveTest extends TestCase
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
        $testClass = new Wave();
        $testClass->accountCreate("", "");
    }

    public function testAccountCreateSansUser()
    {
        $this->expectExceptionMessage("User n'est pas renseignée.");
        $testClass = new Wave();
        $testClass->accountCreate("", "Mot de passe");
    }

    public function testAccountCreateSansPass()
    {
        $this->expectExceptionMessage("Pass n'est pas renseignée.");
        $testClass = new Wave();
        $testClass->accountCreate("Nom d'utilisateur", "");
    }

    public function testAccountCreate()
    {
        $testClass = new Wave();
        $this->assertSame(true, $testClass->accountCreate("PHPUnitUser", "MonMotDePasse"));
    }

    public function testAccountUpdatePermSansUserEtPermission()
    {
        $this->expectExceptionMessage("User n'est pas renseignée.");
        $testClass = new Wave();
        $testClass->accountUpdatePerm("", "");
    }

    public function testAccountUpdatePermSansUser()
    {
        $this->expectExceptionMessage("User n'est pas renseignée.");
        $testClass = new Wave();
        $testClass->accountUpdatePerm("", 42);
    }

    public function testAccountUpdatePermSansPermission()
    {
        $this->expectExceptionMessage("Permission n'est pas renseignée.");
        $testClass = new Wave();
        $testClass->accountUpdatePerm("Utilisateur", "");
    }

    public function testAccountUpdatePermUtilisateurInconnue()
    {
        $testClass = new Wave();
        $this->assertSame(false, $testClass->accountUpdatePerm("UtilisateurQuiExistePas", 42));
    }

    public function testAccountUpdatePermUtilisateur()
    {
        $testClass = new Wave();
        $this->assertSame(true, $testClass->accountUpdatePerm("PHPUnitUser", 42));
    }

    public function testAccountCreateDoublon()
    {
        $testClass = new Wave();
        $this->assertSame(false, $testClass->accountCreate("PHPUnitUser", "MonMotDePasse"));
    }

    public function testAccountModSansConnexion()
    {
        $testClass = new Wave();
        $this->assertSame(false, $testClass->accountMod("test"));
    }

    public function testAccountRecoveryCreateSansEmailEtUser()
    {
        $this->expectExceptionMessage("Email n'est pas renseignée.");
        $testClass = new Wave();
        $testClass->accountRecoveryCreate("", "");
    }

    public function testAccountRecoveryCreateSansEmail()
    {
        $this->expectExceptionMessage("Email n'est pas renseignée.");
        $testClass = new Wave();
        $testClass->accountRecoveryCreate("", "Utilisateur");
    }

    public function testAccountRecoveryCreateSansUser()
    {
        $this->expectExceptionMessage("User n'est pas renseignée.");
        $testClass = new Wave();
        $testClass->accountRecoveryCreate("MonemailpasBesoinArobase", "");
    }

    public function testAccountRecoveryCreateSansConnexion()
    {
        $testClass = new Wave();
        $this->assertSame(null, $testClass->accountRecoveryCreate("phpunit@testclass.net", "PHPUnitUser"));
    }

    public function testAccountVerifSansConnexion()
    {
        $testClass = new Wave();
        $this->assertArrayNotHasKey("user", $testClass->accountVerif());
    }
    
    public function testAccountModMdpSansConnexion()
    {
        $testClass = new Wave();
        $this->assertSame(false, $testClass->accountModMdp("AutreMotDePasse"));
    }

    public function testAccountConnectSansUserEtPass()
    {
        $this->expectExceptionMessage("User n'est pas renseignée.");
        $testClass = new Wave();
        $testClass->accountConnect("", "");
    }

    public function testAccountConnectSansUser()
    {
        $this->expectExceptionMessage("User n'est pas renseignée.");
        $testClass = new Wave();
        $testClass->accountConnect("", "Mot de passe");
    }

    public function testAccountConnectSansPass()
    {
        $this->expectExceptionMessage("Pass n'est pas renseignée.");
        $testClass = new Wave();
        $testClass->accountConnect("Nom d'utilisateur", "");
    }

    public function testAccountConnectMauvaisMotDePasse()
    {
        $testClass = new Wave();
        $this->assertSame(false, $testClass->accountConnect("PHPUnitUser", "PasLeBonMotDePasse"));
    }

    public function testAccountVerifPermSansConnexionPasPermission()
    {
        $testClass = new Wave();
        $this->assertSame(false, $testClass->accountVerifPerm(52));
    }

    public function testAccountVerifPermSansConnexionAvecPermission()
    {
        $testClass = new Wave();
        $this->assertSame(false, $testClass->accountVerifPerm(32));
    }

    public function testAccountConnect()
    {
        $testClass = new Wave();
        $this->assertSame(true, $testClass->accountConnect("PHPUnitUser", "MonMotDePasse"));
    }

    public function testAccountModMdpUn()
    {
        $testClass = new Wave();
        $this->assertSame(true, $testClass->accountModMdp("AutreMotDePasse"));
    }

    public function testAccountModMdpDeux()
    {
        $testClass = new Wave();
        $this->assertSame(true, $testClass->accountModMdp("MonMotDePasse"));
    }

    public function testAccountVerifPermAvecConnexionSansPermission()
    {
        $testClass = new Wave();
        $this->assertSame(false, $testClass->accountVerifPerm(52));
    }

    public function testAccountVerifPermAvecConnexionAvecPermission()
    {
        $testClass = new Wave();
        $this->assertSame(true, $testClass->accountVerifPerm(32));
    }

    public function testAccountVerifAvecConnexion()
    {
        $testClass = new Wave();
        $this->assertArrayHasKey("user", $testClass->accountVerif());
    }

    public function testAccountVerifAvecConnexionMauvaisIP()
    {
        $testClass = new Wave();
        $vraisIP = $_SERVER["REMOTE_ADDR"];
        $_SERVER["REMOTE_ADDR"] = "999.999.999.999";
        $this->assertArrayNotHasKeY("user", $testClass->accountVerif());
        $_SERVER["REMOTE_ADDR"] = $vraisIP;
    }

    public function testAccountRecoveryCreateAvecConnexionMauvaisEmail()
    {
        $testClass = new Wave();
        $this->assertSame(null, $testClass->accountRecoveryCreate("phpunit@testclass.net", "PHPUnitUser"));
    }

    public function testAccountModAvecConnexion()
    {
        $testClass = new Wave();
        $this->assertSame(true, $testClass->accountMod("PHPUnitUser2", "", "", "phpunit@testclass.net"));
    }

    public function testAccountRecoveryUseSansToken()
    {
        $this->expectExceptionMessage("Token n'est pas renseignée.");
        $testClass = new Wave();
        $testClass->accountRecoveryUse("");
    }

    public function testAccountRecoveryCreateAvecConnexion()
    {
        $testClass = new Wave();
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
        $testClass = new Wave();
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
        $testClass = new Wave();
        $this->assertSame(null, $testClass->accountRecoveryUse($resultTestAfter));
    }

    public function testAccountDisconnect()
    {
        $testClass = new Wave();
        $this->assertSame(true, $testClass->accountDisconnect());
    }

    public function testAccountModMdpApresDeconnexion()
    {
        $testClass = new Wave();
        $this->assertSame(false, $testClass->accountModMdp("AutreMotDePasse"));
    }

    public function testAccountDeleteSansUser()
    {
        $this->expectExceptionMessage("User n'est pas renseignée.");
        $testClass = new Wave();
        $testClass->accountDelete("");
    }

    public function testAccountDeleteUtilisateurQuiExistePas()
    {
        $testClass = new Wave();
        $this->assertSame(false, $testClass->accountDelete("QuiExistePas"));
    }

    public function testAccountDelete()
    {
        $testClass = new Wave();
        $this->assertSame(true, $testClass->accountDelete("PHPUnitUser2"));
    }

    public function testAccountDeleteDoublon()
    {
        $testClass = new Wave();
        $this->assertSame(false, $testClass->accountDelete("PHPUnitUser2"));
    }

    public function testAccountVerifPermApresDeconnexionSansPermission()
    {
        $testClass = new Wave();
        $this->assertSame(false, $testClass->accountVerifPerm(52));
    }

    public function testAccountVerifPermApresDeconnexionAvecPermission()
    {
        $testClass = new Wave();
        $this->assertSame(false, $testClass->accountVerifPerm(32));
    }

    public function testAccountVerifSansConnexionApresDeconnexion()
    {
        $testClass = new Wave();
        $this->assertArrayNotHasKey("user", $testClass->accountVerif());
    }

    public function testAccountModSansConnexionApresDeconnexion()
    {
        $testClass = new Wave();
        $this->assertSame(false, $testClass->accountMod("test"));
    }

    public function testAccountRecoveryCreateSansConnexionApresDeconnexion()
    {
        $testClass = new Wave();
        $this->assertSame(null, $testClass->accountRecoveryCreate("phpunit@testclass.net", "PHPUnitUser"));
    }
    
    public function testPermissionAdd()
    {
        $testClass = new Wave();
        $this->assertSame(true, $testClass->permissionAdd(3042, "Label du level ultimator !"));
    }
    
    public function testPermissionAddDoublon()
    {
        $testClass = new Wave();
        $this->assertSame(false, $testClass->permissionAdd(3042, "XxxLabel du level ultimator !xxX"));
    }
    
    public function testPermissionGet()
    {
        $testClass = new Wave();
        $this->assertSame("Label du level ultimator !", $testClass->permissionGet(3042));
    }
    
    public function testPermissionGetExistePas()
    {
        $testClass = new Wave();
        $this->assertSame("", $testClass->permissionGet(3043));
    }
    
    public function testPermissionRemove()
    {
        $testClass = new Wave();
        $this->assertSame(true, $testClass->permissionRemove(3042));
    }
    
    public function testPermissionGetApresSuppression()
    {
        $testClass = new Wave();
        $this->assertSame("", $testClass->permissionGet(3042));
    }

    public function testAccountClearSession()
    {
        $testClass = new Wave();
        $this->assertSame(true, $testClass->accountClearSession());
    }
}
