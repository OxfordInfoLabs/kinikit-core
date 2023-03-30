<?php

namespace Kinikit\Core\Template\ValueFunction;

use PHPUnit\Framework\TestCase;

class MathsValueFunctionTest extends TestCase {

    public function testAddAndSubtractFunctionsEvaluateCorrectlyForMemberAndLiteralValues() {

        $function = new MathsValueFunction();
        $this->assertEquals(40, $function->applyFunction("add 10", 30, []));
        $this->assertEquals(40, $function->applyFunction("add hello", 20, ["hello" => 20]));
        $this->assertEquals(-4.23, $function->applyFunction("add float", -5, ["float" => 0.77]));

        $this->assertEquals(20, $function->applyFunction("subtract 10", 30, []));
        $this->assertEquals(0, $function->applyFunction("subtract hello", 20, ["hello" => 20]));
        $this->assertEquals(1.8, round($function->applyFunction("subtract float", 1.4, ["float" => -0.4]), 1));

    }

    public function testMultiplyAndDivideFunctionsEvaluateCorrectlyForMemberAndLiteralValues() {

        $function = new MathsValueFunction();
        $this->assertEquals(143, $function->applyFunction("multiply 11", 13, []));
        $this->assertEquals(-24, $function->applyFunction("multiply -6", 4, []));
        $this->assertEquals(3, $function->applyFunction("multiply hello", 0.5, ["hello" => 6]));
        $this->assertEquals(-3.2, $function->applyFunction("multiply float", 2, ["float" => -1.6]));

        $this->assertEquals(5, $function->applyFunction("divide 2", 10, []));
        $this->assertEquals(2.5, $function->applyFunction("divide 4", 10, []));
        $this->assertEquals(-11 / 13, $function->applyFunction("divide hello", 11, ["hello" => -13]));
        $this->assertEquals(2, $function->applyFunction("divide float", 1.4, ["float" => 0.7]));
    }

    public function testModuloAndFloorAndCeilFunctionsEvaluateCorrectlyForMemberAndLiteralValues() {

        $function = new MathsValueFunction();
        $this->assertEquals(3, $function->applyFunction("modulo 4", 11, []));
        $this->assertEquals(2, $function->applyFunction("modulo 5", 12, []));
        $this->assertEquals(2, $function->applyFunction("modulo hello", 12, ["hello" => 10]));
        $this->assertEquals(0, $function->applyFunction("modulo goodbye", 4, ["goodbye" => 1]));

        $this->assertEquals(11, $function->applyFunction("floor", 11.6, []));
        $this->assertEquals(-12, $function->applyFunction("floor", -11.6, []));

        $this->assertEquals(12, $function->applyFunction("ceil", 11.6, []));
        $this->assertEquals(-11, $function->applyFunction("ceil", -11.6, []));
    }

    public function testRoundFunctionEvaluatedCorrectly() {

        $function = new MathsValueFunction();
        $this->assertTrue($function->doesFunctionApply("round"));

        $this->assertEquals(3, $function->applyFunction("round", 3.14, null));
        $this->assertEquals(10, $function->applyFunction("round", 9.7, null));
        $this->assertEquals(6, $function->applyFunction("round", 6.28, null));

    }

    public function testCanRoundValueToFixedNumberOfDecimalPlaces() {

        $function = new MathsValueFunction();
        $this->assertTrue($function->doesFunctionApply("decimalplaces"));

        $this->assertEquals(3.14, $function->applyFunction("decimalplaces 2", 3.14159, null));
        $this->assertEquals(2.7, $function->applyFunction("decimalplaces 1", 2.718, null));
    }

    public function testCanCommaSeparateThousands() {

        $function = new MathsValueFunction();
        $this->assertTrue($function->doesFunctionApply("commaseparatedthousands"));

        $this->assertEquals("1,000", $function->applyFunction("commaseparatedthousands", 1000, null));
        $this->assertEquals("5,400", $function->applyFunction("commaseparatedthousands", 5400, null));
        $this->assertEquals("321,654,987", $function->applyFunction("commaseparatedthousands", 321654987, null));

    }
}