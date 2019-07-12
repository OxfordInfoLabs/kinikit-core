<?php

namespace Kinikit\Core\Template;

/**
 * Test cases for the PHP template parser
 *
 * Class PHPTemplateParserTest
 */
class PHPTemplateParserTest extends \PHPUnit\Framework\TestCase {

    /**
     * @var PHPTemplateParser
     */
    private $parser;

    public function setUp(): void {
        $this->parser = new PHPTemplateParser();
    }

    public function testStaticTextIsReturnedIntact() {
        $model = [];
        $this->assertEquals("My little pony", $this->parser->parseTemplateText("My little pony", $model));

        $model = ["test" => "Hello"];
        $this->assertEquals("Jumperoo <h1>My little smiler</h1>", $this->parser->parseTemplateText("Jumperoo <h1>My little smiler</h1>", $model));

    }


    public function testPHPIsEvaluatedAsNormalUsingPassedParams() {

        $model = ["test" => "Hello", "age" => 33];

        $templateText = '<h1><?= $test; ?></h1> my age is <?= $age; ?>';
        $this->assertEquals("<h1>Hello</h1> my age is 33", $this->parser->parseTemplateText($templateText, $model));


        $templateText = '<h1><?= $test; ?></h1> <?php if ($age > 3) echo "bingo"; ?>';
        $this->assertEquals("<h1>Hello</h1> bingo", $this->parser->parseTemplateText($templateText, $model));

    }





}
