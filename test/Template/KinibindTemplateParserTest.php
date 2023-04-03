<?php

namespace Kinikit\Core\Template;

use PHPUnit\Framework\TestCase;

include_once "autoloader.php";

class KinibindTemplateParserTest extends TestCase {

    /**
     * @var KinibindTemplateParser
     */
    private $parser;

    public function setUp(): void {
        $this->parser = new KinibindTemplateParser();
    }

    public function testCanProcessPlainText() {
        $output = $this->parser->parseTemplateText("Sample plaintext.");
        $this->assertEquals("Sample plaintext.", $output);
    }

    public function testCanProcessSimpleFullHTML() {
        $output = $this->parser->parseTemplateText("<html><body>Sample Text.</body></html>");
        $this->assertStringContainsString("<html><body>Sample Text.</body></html>", $output);
    }

    public function testCanProcessHTMLFragment() {
        $output = $this->parser->parseTemplateText("<h1>Sample Text.</h1>");
        $this->assertEquals("<h1>Sample Text.</h1>", $output);
    }


    public function testCanProcessSimpleTextDirective() {
        $model = ["text" => "Hello world"];

        $output = $this->parser->parseTemplateText('<h1 k-text="text"></h1>', $model);
        $this->assertEquals("<h1>Hello world</h1>", $output);
    }

    public function testCanProcessSimpleHTMLDirective() {
        $model = ["html" => "<p>Hello world</p>"];

        $output = $this->parser->parseTemplateText('<h1 k-html="html"></h1>', $model);
        $this->assertEquals("<h1><p>Hello world</p></h1>", $output);
    }

    public function testCanPassThroughAlternativePrefix() {
        $model = ["text" => "Hello world"];

        $customParser = new KinibindTemplateParser("d");
        $output = $customParser->parseTemplateText('<h1 d-text="text"></h1>', $model);
        $this->assertEquals("<h1>Hello world</h1>", $output);
    }

    public function testCanProcessSimpleIfStatement() {
        $model = ["bob" => 20];

        $output = $this->parser->parseTemplateText('<span k-if="bob | gt 2">Pass</span><span k-if="bob | lt 2">Fail</span>', $model);
        $this->assertEquals("<span>Pass</span>", $output);
    }

    public function testCanProcessSimpleEachStatement() {
        $model = ["items" => [
            [
                "title" => "Bob"
            ],
            [
                "title" => "Mary"
            ],
            [
                "title" => "Nigel"
            ]
        ]];

        $output = $this->parser->parseTemplateText('<p k-each-item="items"><span>{{item.title}}</span></p>', $model);
        $this->assertEquals("<p><span>Bob</span></p><p><span>Mary</span></p><p><span>Nigel</span></p>", $output);
    }

    public function testIndexModelVariableImplementedForEach() {
        $model = ["items" => [
            [
                "title" => "Bob",
                "children" => [
                    "Peter",
                    "Susan",
                    "Edmund",
                    "Lucy"
                ]
            ],
            [
                "title" => "Mary",
                "children" => [
                    "Ron",
                    "Ginny"
                ]
            ],
            [
                "title" => "Nigel",
                "children" => [
                    "Julian",
                    "Dick",
                    "Anne",
                    "George",
                    "Timmy"
                ]
            ]
        ]];

        $output = $this->parser->parseTemplateText('<p k-each-item="items"><span>{{item.title}} {{$index}}</span><span k-each-child="item.children">{{child}} {{$index}}</span></p>', $model);
        $this->assertEquals("<p><span>Bob 0</span><span>Peter 0</span><span>Susan 1</span><span>Edmund 2</span><span>Lucy 3</span></p><p><span>Mary 1</span><span>Ron 0</span><span>Ginny 1</span></p><p><span>Nigel 2</span><span>Julian 0</span><span>Dick 1</span><span>Anne 2</span><span>George 3</span><span>Timmy 4</span></p>", $output);
    }


    public function testCanProcessSimpleSetStatement() {
        $model = [];

        $output = $this->parser->parseTemplateText('<span k-set-name="\'Bob\'">{{name}}</span>', $model);
        $this->assertEquals('<span>Bob</span>', $output);
        $this->assertEquals(["name" => "Bob"], $model);
    }

    public function testCanProcessABlockTag() {
        $model = ["name" => "Bob"];

        $output = $this->parser->parseTemplateText('<p><block k-if="name | contains \'b\'">{{name}}</block></p>', $model);
        $this->assertEquals('<p>Bob</p>', $output);

        $output = $this->parser->parseTemplateText('My world of fun and <block k-if="1">games</block>', $model);
        $this->assertEquals("My world of fun and games", $output);

    }

    public function testCanProcessClassDirective() {
        $model = ["name" => "Bob"];

        $output = $this->parser->parseTemplateText('<p k-class-red="name | contains \'b\'">Hello</p>', $model);
        $this->assertEquals('<p class="red">Hello</p>', $output);

        $output = $this->parser->parseTemplateText('<p k-class-red="name | contains \'b\'" class="red blue green">Hello</p>', $model);
        $this->assertEquals('<p class="red blue green">Hello</p>', $output);

        $output = $this->parser->parseTemplateText('<p k-class-red="name | contains \'z\'" class="red blue green">Hello</p>', $model);
        $this->assertEquals('<p class="blue green">Hello</p>', $output);

    }

    public function testCanProcessAnyAttributeDirective() {
        $model = ["name" => "Bob"];

        $output = $this->parser->parseTemplateText('<p k-pineapple="\'Peter\'" pineapple="bongo">Hello</p>', $model);
        $this->assertEquals('<p pineapple="Peter">Hello</p>', $output);

        $output = $this->parser->parseTemplateText('<p k-class="\'peter\'" class="red green blue">Hello</p>', $model);
        $this->assertEquals('<p class="peter">Hello</p>', $output);

    }

    public function testCanProcessMoreComplexExample() {
        $model = ["list" => [
            [
                "name" => "Bob",
                "age" => 56
            ], [
                "name" => "Mary",
                "age" => 40
            ], [
                "name" => "Steve",
                "age" => 51
            ]
        ], "hello" => "world", "someHTML" => "<h3>I've arrived via k-html</h3>"];

        $input = file_get_contents(__DIR__ . "/kinibind-template-HTML-test.html");
        $expectedOutput = file_get_contents(__DIR__ . "/kinibind-template-HTML-expected.html");
        $output = $this->parser->parseTemplateText($input, $model);
        $this->assertEquals($expectedOutput, $output);
    }

}