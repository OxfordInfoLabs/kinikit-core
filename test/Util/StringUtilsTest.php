<?php

namespace Kinikit\Core\Util;

include_once "autoloader.php";

/**
 * Test cases for the string utils.
 *
 * @author mark
 *
 */
class StringUtilsTest extends \PHPUnit\Framework\TestCase {


    public function testCanGenerateRandomStringOfJustLowerCaseLetters() {


        $randomString = StringUtils::generateRandomString(4, false, false, false);
        $this->assertEquals(4, strlen($randomString));
        for ($i = 0; $i < 4; $i++) {
            $char = substr($randomString, $i, 1);
            $this->assertTrue(ord($char) >= 97 && ord($char) <= 122);
        }


        $randomString = StringUtils::generateRandomString(6, false, false, false);
        $this->assertEquals(6, strlen($randomString));
        for ($i = 0; $i < 6; $i++) {
            $char = substr($randomString, $i, 1);
            $this->assertTrue(ord($char) >= 97 && ord($char) <= 122);
        }

    }


    public function testCanGenerateRandomStringOfUpperOrLowerCaseLetters() {

        $randomString = StringUtils::generateRandomString(4, true, false, false);
        $this->assertEquals(4, strlen($randomString));
        for ($i = 0; $i < 4; $i++) {
            $char = substr($randomString, $i, 1);
            $this->assertTrue((ord($char) >= 65 && ord($char) <= 90) || (ord($char) >= 97 && ord($char) <= 122));
        }


        $randomString = StringUtils::generateRandomString(6, true, false, false);
        $this->assertEquals(6, strlen($randomString));
        for ($i = 0; $i < 6; $i++) {
            $char = substr($randomString, $i, 1);
            $this->assertTrue((ord($char) >= 65 && ord($char) <= 90) || (ord($char) >= 97 && ord($char) <= 122));
        }

    }


    public function testCanGenerateRandomStringOfNumberOrLowerCaseLetters() {


        $randomString = StringUtils::generateRandomString(4, false, true, false);
        $this->assertEquals(4, strlen($randomString));
        for ($i = 0; $i < 4; $i++) {
            $char = substr($randomString, $i, 1);
            $this->assertTrue((ord($char) >= 48 && ord($char) <= 57) || (ord($char) >= 97 && ord($char) <= 122));
        }


        $randomString = StringUtils::generateRandomString(6, false, true, false);
        $this->assertEquals(6, strlen($randomString));
        for ($i = 0; $i < 6; $i++) {
            $char = substr($randomString, $i, 1);
            $this->assertTrue((ord($char) >= 48 && ord($char) <= 57) || (ord($char) >= 97 && ord($char) <= 122));
        }

    }


}

?>
