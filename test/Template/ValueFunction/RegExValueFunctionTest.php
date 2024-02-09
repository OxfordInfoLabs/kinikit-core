<?php


namespace Kinikit\Core\Template\ValueFunction;

use PHPUnit\Framework\TestCase;

include_once "autoloader.php";

class RegExValueFunctionTest extends TestCase {

    public function testRegExExpressionsStartingAndEndingWithDelimitersAreAcceptedAndProcessedUsingFullOrCaptureExpression() {

        $function = new RegExValueFunction();
        $this->assertFalse($function->doesFunctionApply("test"));
        $this->assertFalse($function->doesFunctionApply("regex()"));
        $this->assertFalse($function->doesFunctionApply("/onesided"));
        $this->assertTrue($function->doesFunctionApply("/valid/"));


        $this->assertEquals("cde", $function->applyFunction("/c.*?e/", "abcdefg", []));
        $this->assertEquals("01", $function->applyFunction("/^.{3}(.{2})/", "03/01/2022", []));
    }

    public function testNullValuesAreHandledCorrectly() {

        $function = new RegExValueFunction();

        $functionString = "/^.{3}(.{2})/";
        $value = "03/01/2022";

        $this->assertEquals("", $function->applyFunction($functionString, null, []));
        $this->assertEquals($value, $function->applyFunction(null, $value, []));
        $this->assertNull($function->applyFunction(null, null, []));

    }


}