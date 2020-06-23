<?php

namespace Kinikit\Core\Security\Hash;

include_once "autoloader.php";

class PHPPasswordHashProviderTest extends \PHPUnit\Framework\TestCase {

    public function testCanHashAndVerify() {

        $hashProvider = new PHPPasswordHashProvider();
        $hash = $hashProvider->generateHash("mickeymouse");

        $this->assertTrue(password_verify("mickeymouse", $hash));

        $this->assertFalse($hashProvider->verifyHash("donaldduck", $hash));
        $this->assertTrue($hashProvider->verifyHash("mickeymouse", $hash));

    }


}
