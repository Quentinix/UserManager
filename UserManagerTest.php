<?php

require 'UserManager.php';

use UserManager\UserManager;
use PHPUnit\Framework\TestCase;

class UserManagerTest extends TestCase {

	// private $resultTestTokenRecovery;
	
	function testPHPUnit() {
		$this->assertSame(true, true);
		$_SERVER["REMOTE_ADDR"] = "127.0.0.100";
	}

	function testAccountCreateSansUserEtPass() {
		$this->expectExceptionMessage("User n'est pas renseignée.");
		$testClass = new UserManager();
		$testClass->accountCreate("", "");
	}

	function testAccountCreateSansUser() {
		$this->expectExceptionMessage("User n'est pas renseignée.");
		$testClass = new UserManager();
		$testClass->accountCreate("", "Mot de passe");
	}

	function testAccountCreateSansPass() {
		$this->expectExceptionMessage("Pass n'est pas renseignée.");
		$testClass = new UserManager();
		$testClass->accountCreate("Nom d'utilisateur", "");
	}

	function testAccountCreate() {
		$testClass = new UserManager();
		$this->assertSame(true, $testClass->accountCreate("PHPUnitUser", "MonMotDePasse"));
	}

	function testAccountUpdatePermSansUserEtPermission() {
		$this->expectExceptionMessage("User n'est pas renseignée.");
		$testClass = new UserManager();
		$testClass->accountUpdatePerm("", "");
	}

	function testAccountUpdatePermSansUser() {
		$this->expectExceptionMessage("User n'est pas renseignée.");
		$testClass = new UserManager();
		$testClass->accountUpdatePerm("", 42);
	}

	function testAccountUpdatePermSansPermission() {
		$this->expectExceptionMessage("Permission n'est pas renseignée.");
		$testClass = new UserManager();
		$testClass->accountUpdatePerm("Utilisateur", "");
	}

	function testAccountUpdatePermUtilisateurInconnue() {
		$testClass = new UserManager();
		$this->assertSame(false, $testClass->accountUpdatePerm("UtilisateurQuiExistePas", 42));
	}

	function testAccountUpdatePermUtilisateur() {
		$testClass = new UserManager();
		$this->assertSame(true, $testClass->accountUpdatePerm("PHPUnitUser", 42));
	}

	function testAccountCreateDoublon() {
		$testClass = new UserManager();
		$this->assertSame(false, $testClass->accountCreate("PHPUnitUser", "MonMotDePasse"));
	}

	function testAccountModSansConnexion() {
		$testClass = new UserManager();
		$this->assertSame(false, $testClass->accountMod("test"));
	}

	function testAccountRecoveryCreateSansEmailEtUser() {
		$this->expectExceptionMessage("Email n'est pas renseignée.");
		$testClass = new UserManager();
		$testClass->accountRecoveryCreate("", "");
	}

	function testAccountRecoveryCreateSansEmail() {
		$this->expectExceptionMessage("Email n'est pas renseignée.");
		$testClass = new UserManager();
		$testClass->accountRecoveryCreate("", "Utilisateur");
	}

	function testAccountRecoveryCreateSansUser() {
		$this->expectExceptionMessage("User n'est pas renseignée.");
		$testClass = new UserManager();
		$testClass->accountRecoveryCreate("MonemailpasBesoinArobase", "");
	}

	function testAccountRecoveryCreateSansConnexion() {
		$testClass = new UserManager();
		$this->assertSame(NULL, $testClass->accountRecoveryCreate("phpunit@testclass.net", "PHPUnitUser"));
	}

	function testAccountVerifSansConnexion() {
		$testClass = new UserManager();
		$this->assertArrayNotHasKey("user", $testClass->accountVerif());
	}

	function testAccountConnectSansUserEtPass() {
		$this->expectExceptionMessage("User n'est pas renseignée.");
		$testClass = new UserManager();
		$testClass->accountConnect("", "");
	}

	function testAccountConnectSansUser() {
		$this->expectExceptionMessage("User n'est pas renseignée.");
		$testClass = new UserManager();
		$testClass->accountConnect("", "Mot de passe");
	}

	function testAccountConnectSansPass() {
		$this->expectExceptionMessage("Pass n'est pas renseignée.");
		$testClass = new UserManager();
		$testClass->accountConnect("Nom d'utilisateur", "");
	}

	function testAccountConnectMauvaisMotDePasse() {
		$testClass = new UserManager();
		$this->assertSame(false, $testClass->accountConnect("PHPUnitUser", "PasLeBonMotDePasse"));
	}

	function testAccountVerifPermSansConnexionPasPermission() {
		$testClass = new UserManager();
		$this->assertSame(false, $testClass->accountVerifPerm(52));
	}

	function testAccountVerifPermSansConnexionAvecPermission() {
		$testClass = new UserManager();
		$this->assertSame(false, $testClass->accountVerifPerm(32));
	}

	function testAccountConnect() {
		$testClass = new UserManager();
		$this->assertSame(true, $testClass->accountConnect("PHPUnitUser", "MonMotDePasse"));
	}

	function testAccountVerifPermAvecConnexionSansPermission() {
		$testClass = new UserManager();
		$this->assertSame(false, $testClass->accountVerifPerm(52));
	}

	function testAccountVerifPermAvecConnexionAvecPermission() {
		$testClass = new UserManager();
		$this->assertSame(true, $testClass->accountVerifPerm(32));
	}

	function testAccountVerifAvecConnexion() {
		$testClass = new UserManager();
		$this->assertArrayHasKey("user", $testClass->accountVerif());
	}

	function testAccountVerifAvecConnexionMauvaisIP() {
		$testClass = new UserManager();
		$vraisIP = $_SERVER["REMOTE_ADDR"];
		$_SERVER["REMOTE_ADDR"] = "999.999.999.999";
		$this->assertArrayNotHasKeY("user", $testClass->accountVerif());
		$_SERVER["REMOTE_ADDR"] = $vraisIP;
	}

	function testAccountRecoveryCreateAvecConnexionMauvaisEmail() {
		$testClass = new UserManager();
		$this->assertSame(NULL, $testClass->accountRecoveryCreate("phpunit@testclass.net", "PHPUnitUser"));
	}

	function testAccountModAvecConnexion() {
		$testClass = new UserManager();
		$this->assertSame(true, $testClass->accountMod("PHPUnitUser2", "", "", "phpunit@testclass.net"));
	}

	function testAccountRecoveryUseSansToken() {
		$this->expectExceptionMessage("Token n'est pas renseignée.");
		$testClass = new UserManager();
		$testClass->accountRecoveryUse("");
	}

	function testAccountRecoveryCreateAvecConnexion() {
		$testClass = new UserManager();
		$resultTest = $testClass->accountRecoveryCreate("phpunit@testclass.net", "PHPUnitUser2");
		$this->assertNotSame(false, $resultTest);
		return $resultTest;
	}

	/**
	 * @depends testAccountRecoveryCreateAvecConnexion
	 */
	function testAccountRecoveryUse($resultTestAfter) {
		$testClass = new UserManager();
		$resultTest = $testClass->accountRecoveryUse($resultTestAfter);
		$this->assertNotSame(false, $resultTest);
		return $resultTest;
	}

	/**
	 * @depends testAccountRecoveryCreateAvecConnexion
	 */
	function testAccountRecoveryUseDoublon($resultTestAfter) {
		$testClass = new UserManager();
		$this->assertSame(null, $testClass->accountRecoveryUse($resultTestAfter));
	}

	function testAccountDisconnect() {
		$testClass = new UserManager();
		$this->assertSame(true, $testClass->accountDisconnect());
	}

	function testAccountDeleteSansUser() {
		$this->expectExceptionMessage("User n'est pas renseignée.");
		$testClass = new UserManager();
		$testClass->accountDelete("");
	}

	function testAccountDeleteUtilisateurQuiExistePas() {
		$testClass = new UserManager();
		$this->assertSame(false, $testClass->accountDelete("QuiExistePas"));
	}

	function testAccountDelete() {
		$testClass = new UserManager();
		$this->assertSame(true, $testClass->accountDelete("PHPUnitUser2"));
	}

	function testAccountDeleteDoublon() {
		$testClass = new UserManager();
		$this->assertSame(false, $testClass->accountDelete("PHPUnitUser2"));
	}

	function testAccountVerifPermApresDeconnexionSansPermission() {
		$testClass = new UserManager();
		$this->assertSame(false, $testClass->accountVerifPerm(52));
	}

	function testAccountVerifPermApresDeconnexionAvecPermission() {
		$testClass = new UserManager();
		$this->assertSame(false, $testClass->accountVerifPerm(32));
	}

	function testAccountVerifSansConnexionApresDeconnexion() {
		$testClass = new UserManager();
		$this->assertArrayNotHasKey("user", $testClass->accountVerif());
	}

	function testAccountModSansConnexionApresDeconnexion() {
		$testClass = new UserManager();
		$this->assertSame(false, $testClass->accountMod("test"));
	}

	function testAccountRecoveryCreateSansConnexionApresDeconnexion() {
		$testClass = new UserManager();
		$this->assertSame(NULL, $testClass->accountRecoveryCreate("phpunit@testclass.net", "PHPUnitUser"));
	}
}

