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


    public function testCanConvertStringToCamelCase() {

        // Check already in camel case
        $this->assertEquals("happyHappyJoyJoy", StringUtils::convertToCamelCase("happyHappyJoyJoy"));

        // Check one with initial caps
        $this->assertEquals("happyHappyJoyJoy", StringUtils::convertToCamelCase("HappyHappyJoyJoy"));

        // Check ordinary spaced capitalised one
        $this->assertEquals("happyHappyJoyJoy", StringUtils::convertToCamelCase("Happy Happy Joy Joy"));

        // Check lower cased spaced one
        $this->assertEquals("happyHappyJoyJoy", StringUtils::convertToCamelCase("happy happy joy joy"));

        // Check for hyphenated formats
        $this->assertEquals("happyHappyJoyJoy", StringUtils::convertToCamelCase("happy_happy_joy_joy"));


    }

    public function testCanConvertStringFromCamelCase() {

        // Already not in camel case
        $this->assertEquals("Happy Happy Joy Joy", StringUtils::convertFromCamelCase("Happy Happy Joy Joy"));

        // Standard substitution
        $this->assertEquals("Happy Happy Joy Joy", StringUtils::convertFromCamelCase("happyHappyJoyJoy"));


        // Just first word converted
        $this->assertEquals("Happy Happy joyJoy", StringUtils::convertFromCamelCase("happyHappy joyJoy"));

        // Numbers also split
        $this->assertEquals("Happy 11", StringUtils::convertFromCamelCase("happy11"));

        // Underscore also used as delimeter
        $this->assertEquals("Happy Happy Joy Joy", StringUtils::convertFromCamelCase("happy_happy_joy_joy"));


    }


}

?>
