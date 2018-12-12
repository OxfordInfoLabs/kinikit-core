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

    public function testNonWildcardPatternsOnlyMatchIfStringEqualsPattern() {

        // Precise matches should match
        $this->assertTrue(StringUtils::matchesWildcardPattern("precise", "precise"));
        $this->assertTrue(StringUtils::matchesWildcardPattern("bob", "bob"));
        $this->assertTrue(StringUtils::matchesWildcardPattern("mary", "mary"));

        // Imprecise matches for non wildcard patterns should not match
        $this->assertFalse(StringUtils::matchesWildcardPattern("precise", "imprecise"));
        $this->assertFalse(StringUtils::matchesWildcardPattern("precise", "precise2"));
        $this->assertFalse(StringUtils::matchesWildcardPattern("precise", "prec"));

    }

    public function testWildcardPatternsAreEvaluatedCorrectlyAsExpected() {

        $this->assertTrue(StringUtils::matchesWildcardPattern("precise", "prec*"));
        $this->assertTrue(StringUtils::matchesWildcardPattern("precise", "*ise"));
        $this->assertTrue(StringUtils::matchesWildcardPattern("precise", "*ec*"));

        // Check *s are optional characters
        $this->assertTrue(StringUtils::matchesWildcardPattern("precise", "precise*"));
        $this->assertTrue(StringUtils::matchesWildcardPattern("precise", "*precise"));
        $this->assertTrue(StringUtils::matchesWildcardPattern("precise", "*precise*"));

        // Now check for some failures
        $this->assertFalse(StringUtils::matchesWildcardPattern("precise", "*pprecise"));
        $this->assertFalse(StringUtils::matchesWildcardPattern("precise", "precise2*"));
        $this->assertFalse(StringUtils::matchesWildcardPattern("precise", "prec*2*"));

    }


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