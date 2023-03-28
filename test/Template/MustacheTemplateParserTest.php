<?php

namespace Kinikit\Core\Template;

use Kinikit\Core\DependencyInjection\Container;

include_once "autoloader.php";

/**
 * Test cases for the PHP template parser
 *
 * Class MustacheTemplateParserTest
 */
class MustacheTemplateParserTest extends \PHPUnit\Framework\TestCase {

    /**
     * @var MustacheTemplateParser
     */
    private $parser;

    public function setUp(): void {
        $this->parser = Container::instance()->get(MustacheTemplateParser::class);
    }

    public function testStaticTextIsReturnedIntact() {
        $model = [];
        $this->assertEquals("My little pony", $this->parser->parseTemplateText("My little pony", $model));

        $model = ["test" => "Hello"];
        $this->assertEquals("Jumperoo <h1>My little smiler</h1>", $this->parser->parseTemplateText("Jumperoo <h1>My little smiler</h1>", $model));
        $this->assertEquals("Bingo <h1>My little smiler</h1>", $this->parser->parseTemplateText("Bingo <h1>My little smiler</h1>", $model));


    }


    public function testPHPIsEvaluatedByMustacheParser() {

        $model = ["test" => "Hello", "age" => 33];

        $templateText = '<h1><?= $test; ?></h1> my age is <?= $age; ?>';
        $this->assertEquals("<h1>Hello</h1> my age is 33", $this->parser->parseTemplateText($templateText, $model));


        $templateText = '<h1><?= $test; ?></h1> <?php if ($age > 3) echo "bingo"; ?>';
        $this->assertEquals("<h1>Hello</h1> bingo", $this->parser->parseTemplateText($templateText, $model));

    }


    public function testMustacheIsEvaluatedByMustacheParser() {

        $model = ["test" => "Hello", "age" => 33];

        $templateText = '<h1>{{test}}</h1> You must have an age of {{#age}}{{.}}{{/age}}';
        $this->assertEquals("<h1>Hello</h1> You must have an age of 33", $this->parser->parseTemplateText($templateText, $model));
    }


    public function testIfIncludeBasePathNotSuppliedPartialsAreResolvedUsingModel(){
        $model = ["test" => "Hello", "age" => "33"];
        $templateText = "Before {{> test }} After {{> age}}";

        $this->assertEquals("Before Hello After 33", $this->parser->parseTemplateText($templateText, $model));
    }

    public function testIfIncludeBasePathSuppliedThisIsUsedToResolveNestedPartials() {

        $model = ["test" => "Hello", "age" => "33"];
        $templateText = "Before {{> test_include }} After";

        $this->assertEquals("Before Content Hello Age 33 After", $this->parser->parseTemplateText($templateText, $model, "Template/"));

    }

}
