<?php

namespace Kinikit\Core\Util;

include_once "autoloader.php";

/**
 * Number utils test
 */
class NumberUtilsTest extends \PHPUnit\Framework\TestCase {

    public function testCanFormatValuesAsMoney() {

        $this->assertEquals("&pound;99.00", NumberUtils::formatAsMoney(99));
        $this->assertEquals("&pound;10.99", NumberUtils::formatAsMoney(10.99));
        $this->assertEquals("&pound;100000.44", NumberUtils::formatAsMoney(100000.44));

        $this->assertEquals("£44.50", NumberUtils::formatAsMoney(44.50, "£"));
        $this->assertEquals("USD44.50", NumberUtils::formatAsMoney(44.50, "USD"));


    }

    public function testCanFormatValuesAsMoneyWithRoundingToInteger() {
        $this->assertEquals("&pound;11", NumberUtils::formatAsMoney("10.51", "&pound;", true));
        $this->assertEquals("&pound;10", NumberUtils::formatAsMoney("10.49", "&pound;", true));
    }


    public function testInvalidValuesCauseNullToBeReturned(){
        $this->assertNull(NumberUtils::formatAsMoney("ABC"));
    }

}