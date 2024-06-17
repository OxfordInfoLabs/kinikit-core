<?php

namespace Kinikit\Core\Template\ValueFunction;

use PHPUnit\Framework\TestCase;

include_once "autoloader.php";

class LogicValueFunctionTest extends TestCase {


    public function testIfNotFunctionEvaluatesCorrectly() {

        $function = new LogicValueFunction();
        $this->assertTrue($function->doesFunctionApply("ifNot"));

        // Check pass through if text set
        $this->assertEquals("hello", $function->applyFunction("ifNot text", "hello", ["text" => "Buffalo"]));

        // Check reset of no text set
        $this->assertEquals("Buffalo", $function->applyFunction("ifNot text 'hello' new", "", ["text" => "Buffalo"]));

        // Check nested one
        $this->assertEquals("Buffalo", $function->applyFunction("ifNot text.nested", "", ["text" => ["nested" => "Buffalo"]]));

    }

    public function testTernaryExpressionsAreEvaluatedCorrectly() {

        $function = new LogicValueFunction();
        $this->assertEquals("Yes", $function->applyFunction("ternary 'Yes' 'No'", true, null));
        $this->assertEquals("No", $function->applyFunction("ternary 'Yes' 'No'", false, null));

        $function = new LogicValueFunction();
        $this->assertEquals("Bong", $function->applyFunction("ternary 'Bong' 'Bung'", 1, null));
        $this->assertEquals("Bung", $function->applyFunction("ternary 'Bong' 'Bung'", 0, null));

    }

    public function testEqualsExpressionsAreEvaluatedCorrectly() {

        $function = new LogicValueFunction();
        $this->assertTrue($function->doesFunctionApply("equals"));

        $this->assertEquals(true, $function->applyFunction("equals 5", 5, null));
        $this->assertEquals(false, $function->applyFunction("equals bong", "bing", null));
        $this->assertEquals(true, $function->applyFunction("equals this", "this", null));

    }

    public function testNotEqualsExpressionsAreEvaluatedCorrectly() {

        $function = new LogicValueFunction();
        $this->assertTrue($function->doesFunctionApply("notequals"));

        $this->assertEquals(true, $function->applyFunction("notequals word", 5, null));
        $this->assertEquals(false, $function->applyFunction("notequals bong", "bong", null));
        $this->assertEquals(true, $function->applyFunction("notequals 7", "seven", null));

    }

    public function testGreaterThanExpressionsAreEvaluatedCorrectly() {

        $function = new LogicValueFunction();
        $this->assertTrue($function->doesFunctionApply("gt"));

        $this->assertEquals(false, $function->applyFunction("gt 5", 5, null));
        $this->assertEquals(true, $function->applyFunction("gt 4", 7, null));
        $this->assertEquals(true, $function->applyFunction("gt John", "Smith", null));

    }

    public function testGreaterThanEqualsExpressionsAreEvaluatedCorrectly() {

        $function = new LogicValueFunction();
        $this->assertTrue($function->doesFunctionApply("gte"));

        $this->assertEquals(true, $function->applyFunction("gte 5", 5, null));
        $this->assertEquals(true, $function->applyFunction("gte bing", "bong", null));
        $this->assertEquals(false, $function->applyFunction("gte 58", 45, null));

    }

    public function testLessThanExpressionsAreEvaluatedCorrectly() {

        $function = new LogicValueFunction();
        $this->assertTrue($function->doesFunctionApply("lt"));

        $this->assertEquals(false, $function->applyFunction("lt 5", 5, null));
        $this->assertEquals(false, $function->applyFunction("lt 4", 7, null));
        $this->assertEquals(true, $function->applyFunction("lt Smith", "John", null));

    }

    public function testLessThanEqualsExpressionsAreEvaluatedCorrectly() {

        $function = new LogicValueFunction();
        $this->assertTrue($function->doesFunctionApply("lte"));

        $this->assertEquals(true, $function->applyFunction("lte 5", 5, null));
        $this->assertEquals(true, $function->applyFunction("lte bong", "bing", null));
        $this->assertEquals(false, $function->applyFunction("lte 22", 85, null));

    }

    public function testCanEnsureValueIsNumeric() {

        $function = new LogicValueFunction();
        $this->assertTrue($function->doesFunctionApply("ensureNumeric"));

        $this->assertEquals("24601", $function->applyFunction("ensureNumeric", 24601, null));
        $this->assertEquals(null, $function->applyFunction("ensureNumeric", "number", null));
    }

    public function testBetweenValuesExpressionsEvaluatedCorrectly() {

        $function = new LogicValueFunction();
        $this->assertTrue($function->doesFunctionApply("between"));

        $this->assertEquals(true, $function->applyFunction("between 1 4", 3, null));
        $this->assertEquals(false, $function->applyFunction("between 5 9", 11, null));
        $this->assertEquals(true, $function->applyFunction("between 10 14", 10, null));
        $this->assertEquals(true, $function->applyFunction("between 15 19", 19, null));

    }

    public function testCanEvaluateAndExpressions() {

        $function = new LogicValueFunction();
        $this->assertTrue($function->doesFunctionApply("and"));

        $this->assertEquals(true, $function->applyFunction("and true", true, null));
        $this->assertEquals(false, $function->applyFunction("and false", true, null));
        $this->assertEquals(false, $function->applyFunction("and true", false, null));
        $this->assertEquals(false, $function->applyFunction("and false", false, null));

    }

    public function testCanEvaluateOrExpressions() {

        $function = new LogicValueFunction();
        $this->assertTrue($function->doesFunctionApply("and"));

        $this->assertEquals(true, $function->applyFunction("or true", true, null));
        $this->assertEquals(true, $function->applyFunction("or false", true, null));
        $this->assertEquals(true, $function->applyFunction("or true", false, null));
        $this->assertEquals(false, $function->applyFunction("or false", false, null));

    }

    public function testCanEvaluateAndNotExpressions() {

        $function = new LogicValueFunction();
        $this->assertTrue($function->doesFunctionApply("and"));

        $this->assertEquals(false, $function->applyFunction("andNot true", true, null));
        $this->assertEquals(true, $function->applyFunction("andNot false", true, null));
        $this->assertEquals(false, $function->applyFunction("andNot true", false, null));
        $this->assertEquals(false, $function->applyFunction("andNot false", false, null));

    }

    public function testCanEvaluateOrNotExpressions() {

        $function = new LogicValueFunction();
        $this->assertTrue($function->doesFunctionApply("and"));

        $this->assertEquals(true, $function->applyFunction("orNot true", true, null));
        $this->assertEquals(true, $function->applyFunction("orNot false", true, null));
        $this->assertEquals(false, $function->applyFunction("orNot true", false, null));
        $this->assertEquals(true, $function->applyFunction("orNot false", false, null));

    }

    public function testCanEvaluateCaseExpression() {

        $function = new LogicValueFunction();
        $this->assertTrue($function->doesFunctionApply("case"));

        $this->assertEquals("first", $function->applyFunction("case 'one' 'first' 'two' 'second'", "one", null));
        $this->assertEquals("second", $function->applyFunction("case 'one' 'first' 'two' 'second'", "two", null));
        $this->assertEquals("default", $function->applyFunction("case 'one' 'first' 'two' 'second' 'default", "three", null));
        $this->assertEquals("default", $function->applyFunction("case 'default'", "someValue", null));

        $model = ["created_date"=>new \DateTime("2024-04-04")];
        $this->assertEquals(null, $function->applyFunction("case 'N/A' null created_date", "N/A", $model));
        $this->assertEquals(new \DateTime("2024-04-04"), $function->applyFunction("case 'N/A' null created_date", $model['created_date'], $model));
    }

}