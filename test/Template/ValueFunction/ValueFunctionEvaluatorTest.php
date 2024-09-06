<?php

namespace Kinikit\Core\Template\ValueFunction;

use PHPUnit\Framework\TestCase;

include_once "autoloader.php";

class ValueFunctionEvaluatorTest extends TestCase {


    /**
     * @var ValueFunctionEvaluator
     */
    private $evaluator;

    public function setUp(): void {
        $this->evaluator = new ValueFunctionEvaluator();
    }

    public function testCanResolveFieldValueForBuiltInEvaluators() {

        $this->assertEquals("cde", $this->evaluator->evaluateValueFunction("/.*(cde).*/", "abcdefg", ["test" => "abcdefg"]));
        $this->assertEquals("March", $this->evaluator->evaluateValueFunction("monthName", "2020-03-02", ["test" => "abcdefg"]));
    }

    public function testIfNoEvaluatorResolvedValueReturnedIntact() {
        $this->assertEquals("Bingo", $this->evaluator->evaluateValueFunction("test", "Bingo", ["test" => "abcdefg"]));
    }


    public function testCanResolveAllFieldValuesForPassedStringWithDelimiters() {

        $this->assertEquals("ell March Bingo", $this->evaluator->evaluateString("[[string | /.*(ell).*/]] [[date | monthName]] [[plain]]",
            ["string" => "Hello", "date" => "2020-03-02", "plain" => "Bingo"]));


        $this->assertEquals("ell March Bingo", $this->evaluator->evaluateString("{{string | /.*(ell).*/}} {{date | monthName}} {{plain}}",
            ["string" => "Hello", "date" => "2020-03-02", "plain" => "Bingo"], ["{{", "}}"]));

    }


    public function testCanResolveSpecialExpressionsInDelimiters() {

        $this->assertEquals(date("Y-m-d H:i:s"), $this->evaluator->evaluateString("[[NOW]]"));
        $this->assertEquals(date("d/m/Y"), $this->evaluator->evaluateString("[[NOW | dateConvert 'Y-m-d H:i:s' 'd/m/Y']]"));

        $now = new \DateTime();
        $now->sub(new \DateInterval("P2D"));
        $this->assertEquals($now->format("d/m/Y"), $this->evaluator->evaluateString("[[2_DAYS_AGO | dateConvert 'Y-m-d H:i:s' 'd/m/Y']]"));

        $now = new \DateTime();
        $now->sub(new \DateInterval("PT3H"));
        $this->assertEquals($now->format("d/m/Y H:i"), $this->evaluator->evaluateString("[[3_HOURS_AGO | dateConvert 'Y-m-d H:i:s' 'd/m/Y H:i']]"));

        $now = new \DateTime();
        $now->sub(new \DateInterval("PT4M"));
        $this->assertEquals($now->format("d/m/Y H:i"), $this->evaluator->evaluateString("[[4_MINUTES_AGO | dateConvert 'Y-m-d H:i:s' 'd/m/Y H:i']]"));

        $now = new \DateTime();
        $now->sub(new \DateInterval("PT25S"));
        $this->assertEquals($now->format("d/m/Y H:i"), $this->evaluator->evaluateString("[[25_SECONDS_AGO | dateConvert 'Y-m-d H:i:s' 'd/m/Y H:i']]"));

        $now = new \DateTime();
        $now->sub(new \DateInterval("P4M"));
        $this->assertEquals($now->format("d/m/Y H:i"), $this->evaluator->evaluateString("[[4_MONTHS_AGO | dateConvert 'Y-m-d H:i:s' 'd/m/Y H:i']]"));

        $now = new \DateTime();
        $now->sub(new \DateInterval("P25Y"));
        $this->assertEquals($now->format("d/m/Y H:i"), $this->evaluator->evaluateString("[[25_YEARS_AGO | dateConvert 'Y-m-d H:i:s' 'd/m/Y H:i']]"));
    }


    public function testCanResolveExpressionsWhichDoNotEvaluateToStrings() {

        $this->assertEquals([
            ["key" => "name", "value" => "Bob"],
            ["key" => "age", "value" => 53]
        ], $this->evaluator->evaluateString("[[test | keyValueArray]]", ["test" => ["name" => "Bob", "age" => "53"]]));

    }

    public function testCanResolveExpressionWhereValueIsNumeric() {

        $this->assertEquals(3, $this->evaluator->evaluateString("[[2 | add 1]]"));

    }

    public function testCanResolveExpressionWhereValueIsString() {

        $this->assertEquals('Bob', $this->evaluator->evaluateString("[['Bobby' | substring 0 3]]"));

    }

    public function testCanResolveExpressionWhereValueIsBoolean() {
        // trust no one!!! Use assertSame where possible
        $this->assertEquals([false], [null]);

        $true = $this->evaluator->evaluateString("[[dnsSec]]", ["registered" => true, "dnsSec" => true]);
        $false = $this->evaluator->evaluateString("[[dnsSec]]", ["registered" => true, "dnsSec" => false]);
        $this->assertSame(false, $false);
        $this->assertSame(true, $true);
    }
}
