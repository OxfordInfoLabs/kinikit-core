<?php

namespace Kinikit\Core\Template;

use Kinikit\Core\DependencyInjection\Container;

/**
 * Test cases for the PHP template parser
 *
 * Class TwigTemplateParserTest
 */
class TwigTemplateParserTest extends \PHPUnit\Framework\TestCase {

    /**
     * @var PHPTemplateParser
     */
    private $parser;

    public function setUp(): void {
        $this->parser = Container::instance()->get(TwigTemplateParser::class);
    }

    public function testStaticTextIsReturnedIntact() {
        $model = [];
        $this->assertEquals("My little pony", $this->parser->parseTemplateText("My little pony", $model));

        $model = ["test" => "Hello"];
        $this->assertEquals("Jumperoo <h1>My little smiler</h1>", $this->parser->parseTemplateText("Jumperoo <h1>My little smiler</h1>", $model));

    }


    public function testPHPIsEvaluatedByTwigParser() {

        $model = ["test" => "Hello", "age" => 33];

        $templateText = '<h1><?= $test; ?></h1> my age is <?= $age; ?>';
        $this->assertEquals("<h1>Hello</h1> my age is 33", $this->parser->parseTemplateText($templateText, $model));


        $templateText = '<h1><?= $test; ?></h1> <?php if ($age > 3) echo "bingo"; ?>';
        $this->assertEquals("<h1>Hello</h1> bingo", $this->parser->parseTemplateText($templateText, $model));

    }


    public function testTwigIsEvaluatedByTwigParser() {

        $model = ["test" => "Hello", "ages" => [33, 22, 44]];

        $templateText = '{{ test }} my ages are {% for age in ages %}{{ age }},{% endfor %}';
        $this->assertEquals("Hello my ages are 33,22,44,", $this->parser->parseTemplateText($templateText, $model));


    }


}
