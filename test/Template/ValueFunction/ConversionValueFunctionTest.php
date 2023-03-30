<?php

namespace Kinikit\Core\Template\ValueFunction;

use PHPUnit\Framework\TestCase;

include_once "autoloader.php";

class ConversionValueFunctionTest extends TestCase {

    public function testFunctionIsResolvedForKnownFunctionNames() {

        $function = new ConversionValueFunction();
        $this->assertFalse($function->doesFunctionApply("imaginary"));
        $this->assertFalse($function->doesFunctionApply("test"));

        $this->assertTrue($function->doesFunctionApply("toJSON"));
        $this->assertTrue($function->doesFunctionApply("toNumber"));
        $this->assertTrue($function->doesFunctionApply("urlencode"));
        $this->assertTrue($function->doesFunctionApply("urlencodeparams"));
        $this->assertTrue($function->doesFunctionApply("htmlToText"));
    }


    public function testCanConvertToJSONFormat() {

        $function = new ConversionValueFunction();
        $this->assertEquals(json_encode([1, 2, 3]), $function->applyFunction("toJSON", [1, 2, 3], null));
        $this->assertEquals(json_encode("Mark"), $function->applyFunction("toJSON", "Mark", null));

    }

    public function testCanConvertToNumber() {

        $function = new ConversionValueFunction();
        $this->assertEquals(25, $function->applyFunction("toNumber", 25, null));
        $this->assertEquals(2500, $function->applyFunction("toNumber", "2,500", null));
        $this->assertNull($function->applyFunction("toNumber", "Bingo", null));
        $this->assertEquals(0, $function->applyFunction("toNumber 0", "HELLO", null));
        $this->assertEquals(5, $function->applyFunction("toNumber 5", null, null));
        $this->assertEquals(5, $function->applyFunction("toNumber 5", "", null));
    }

    public function testCanEncodeURLsCorrectly() {

        $function = new ConversionValueFunction();
        $this->assertEquals("https://google.com/somewherenice?a=Hello%20World&b=Goodbye%20Bob", $function->applyFunction("urlencode", "https://google.com/somewherenice?a=Hello World&b=Goodbye Bob", null));
        $this->assertEquals("https://google.com:25/somewherenice?a=Hello%20World&b=Goodbye%20Bob", $function->applyFunction("urlencode", "https://google.com:25/somewherenice?a=Hello World&b=Goodbye Bob", null));
        $this->assertEquals("/somewherenice?a=Hello%20World&b=Goodbye%20Bob", $function->applyFunction("urlencode", "/somewherenice?a=Hello World&b=Goodbye Bob", null));
        $this->assertEquals("//google.com/somewherenice?a=Hello%20World&b=Goodbye%20Bob", $function->applyFunction("urlencode", "//google.com/somewherenice?a=Hello World&b=Goodbye Bob", null));

    }

    public function testCanCorrectlyConvertHTMLToText() {

        $function = new ConversionValueFunction();
        $this->assertEquals("Lorem ipsum", $function->applyFunction("htmlToText", "<h1>Lorem ipsum</h1>", null));
        $this->assertEquals("Lorem ipsum", $function->applyFunction("htmlToText", "<div class='text-green-500'><h1>Lorem ipsum</h1></div>", null));
        $this->assertEquals("Lorem\\&ipsum\\?", $function->applyFunction("htmlToText '&?'", "<h1>Lorem&ipsum?</h1>", null));

    }

}