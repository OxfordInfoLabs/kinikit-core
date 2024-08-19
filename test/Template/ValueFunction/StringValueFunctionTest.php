<?php

namespace Kinikit\Core\Template\ValueFunction;

use PHPUnit\Framework\TestCase;

include_once "autoloader.php";

class StringValueFunctionTest extends TestCase {

    public function testCanReturnSubstringGivenIndexes() {
        $function = new StringValueFunction();
        $this->assertTrue($function->doesFunctionApply("substring"));

        $string = "This is a test string!";
        $this->assertEquals("test string!", $function->applyFunction("substring 10", $string, null));
        $this->assertEquals(" is a", $function->applyFunction("substring 4 5", $string, null));
        $this->assertEquals("Thi", $function->applyFunction("substring 0 3", $string, null));
        $this->assertEquals("string!", $function->applyFunction("substring -7", $string, null));
    }

    public function testCanConcatenateStrings() {
        $function = new StringValueFunction();
        $this->assertTrue($function->doesFunctionApply("concat"));

        $string1 = "First";
        $string2 = "Second";
        $string3 = "Third";

        $this->assertEquals("FirstSecond", $function->applyFunction("concat '$string2'", $string1, null));
        $this->assertEquals("FirstThird", $function->applyFunction("concat '$string3'", $string1, null));
        $this->assertEquals("SecondFirst", $function->applyFunction("concat '$string1'", $string2, null));
        $this->assertEquals("FirstSecondThird", $function->applyFunction("concat '$string2' '$string3'", $string1, null));
    }

    public function testCanConvertToUTF8OrNullIfNot() {
        $function = new StringValueFunction();
        $this->assertTrue($function->doesFunctionApply("toUTF8"));

        $string1 = "hello world";
        $string2 = "🖤.eth";
        $string3 = "Hello\xF0\x9F\x92\xB8\xF0\x9F.eth";
        $string4 = "I'm\xF0\x93\x81\x80\xF0\x93.eth";

        $this->assertEquals($string1, $function->applyFunction("toUTF8", $string1, null));
        $this->assertNull($function->applyFunction("toUTF8", $string2, null));
        $this->assertNull($function->applyFunction("toUTF8", $string3, null));
        $this->assertNull($function->applyFunction("toUTF8", $string4, null));

    }

    public function testCanTrimStrings() {
        $function = new StringValueFunction();
        $this->assertTrue($function->doesFunctionApply("trim"));

        $string1 = "Test...";
        $string2 = "$%test//";

        $this->assertEquals("Test", $function->applyFunction("trim '.'", $string1, null));
        $this->assertEquals("est...", $function->applyFunction("trim 'T'", $string1, null));
        $this->assertEquals("est", $function->applyFunction("trim 'T.'", $string1, null));
        $this->assertEquals("test", $function->applyFunction("trim '/$%'", $string2, null));
        $this->assertEquals($string2, $function->applyFunction("trim s", $string2, null));

    }

    public function testCanExplodeStringToArray() {
        $function = new StringValueFunction();
        $this->assertTrue($function->doesFunctionApply("explode"));

        $string1 = "1,2,3,4";
        $string2 = "first second third";

        $this->assertEquals(["1", "2", "3", "4"], $function->applyFunction("explode ','", $string1, null));
        $this->assertEquals(["first", "second", "third"], $function->applyFunction("explode ' '", $string2, null));
        $this->assertEquals(["1,2,3,4"], $function->applyFunction("explode ' '", $string1, null));
    }

    public function testCanDoRegexReplace() {
        $function = new StringValueFunction();
        $this->assertTrue($function->doesFunctionApply("replace"));

        // Non reg-ex replace
        $this->assertEquals("Hello dave", $function->applyFunction("replace 'World' 'dave'", "Hello World", null));
        $this->assertEquals("Hello dave amongst many daves", $function->applyFunction("replace 'World' 'dave'", "Hello World amongst many Worlds", null));

        // Reg-ex replace
        $this->assertEquals("This is Bingo indeed Bingo", $function->applyFunction("replace '/[0-9]+/' 'Bingo'", "This is 12345 indeed 45678", null));
        $this->assertEquals(" truncated", $function->applyFunction("replace '/^[a-zA-Z]+/' ''", "Iam truncated", null));

    }

    public function testCanFindStringInString() {
        $function = new StringValueFunction();
        $this->assertTrue($function->doesFunctionApply("contains"));

        $this->assertEquals(true, $function->applyFunction("contains 'l'", "hello", null));
        $this->assertEquals(true, $function->applyFunction("contains 'eve'", "Steve", null));
        $this->assertEquals(true, $function->applyFunction("contains ':'", "2001:23:43::56/32", null));
        $this->assertEquals(true, $function->applyFunction("contains '.'", "192.168.0.0/24", null));

        $this->assertEquals(false, $function->applyFunction("contains 'w'", "test", null));
        $this->assertEquals(false, $function->applyFunction("contains '4'", "123", null));

    }

    public function testCanConvertStringToUpper() {
        $function = new StringValueFunction();
        $this->assertTrue($function->doesFunctionApply("uppercase"));

        $this->assertEquals("TEST", $function->applyFunction("uppercase", "test", null));
        $this->assertEquals("EXAMPLE STRING", $function->applyFunction("uppercase", "example string", null));
        $this->assertEquals("I'M A SHOUTY MAN!", $function->applyFunction("uppercase", "I'm a shouty man!", null));
        $this->assertEquals("ARRGHHHH", $function->applyFunction("uppercase", "arrghhhh", null));
        $this->assertEquals("OH NO!", $function->applyFunction("uppercase", "oh no!", null));

    }

    public function testCanConvertStringToLower() {
        $function = new StringValueFunction();
        $this->assertTrue($function->doesFunctionApply("lowercase"));

        $this->assertEquals("test", $function->applyFunction("lowercase", "TEST", null));
        $this->assertEquals("shhhhhh", $function->applyFunction("lowercase", "SHhHHhh", null));
        $this->assertEquals("it's so quiet....", $function->applyFunction("lowercase", "IT'S SO quiet....", null));
        $this->assertEquals("hello!", $function->applyFunction("lowercase", "HELLO!", null));
        $this->assertEquals("low low low", $function->applyFunction("lowercase", "LOW low LOW", null));

    }

    public function testCanAppendAndPrependToStrings() {
        $function = new StringValueFunction();
        $this->assertTrue($function->doesFunctionApply("append"));
        $this->assertTrue($function->doesFunctionApply("prepend"));

        $this->assertEquals("abcde", $function->applyFunction("append de", "abc", null));
        $this->assertEquals("abcde", $function->applyFunction("append c de", "ab", null));
        $this->assertEquals("abcde", $function->applyFunction("append b c d e", "a", null));

        $this->assertEquals("deabc", $function->applyFunction("prepend de", "abc", null));
        $this->assertEquals("decab", $function->applyFunction("prepend c de", "ab", null));
        $this->assertEquals("edcba", $function->applyFunction("prepend b c d e", "a", null));

    }

    public function testCanEvaluateSplitStringExpressionsCorrectly() {
        $function = new StringValueFunction();
        $this->assertTrue($function->doesFunctionApply("split"));

        $string1 = "1,2,3,4";
        $string2 = "first second third";

        $this->assertEquals(["1", "2", "3", "4"], $function->applyFunction("split ','", $string1, null));
        $this->assertEquals(["first", "second", "third"], $function->applyFunction("split ' '", $string2, null));
        $this->assertEquals(["1,2,3,4"], $function->applyFunction("split ' '", $string1, null));

    }

    public function testCanEvaluateInitialCapsExpressionsCorrectly() {
        $function = new StringValueFunction();
        $this->assertTrue($function->doesFunctionApply("initialCaps"));

        $this->assertEquals("Hello", $function->applyFunction("initialCaps", "hElLo", null));
        $this->assertEquals("Look", $function->applyFunction("initialCaps", "LOOK", null));
        $this->assertEquals("I", $function->applyFunction("initialCaps", "i", null));

    }

    public function testCanEvaluateWordCountInStringCorrectly() {
        $function = new StringValueFunction();
        $this->assertTrue($function->doesFunctionApply("words"));

        $this->assertEquals(["Live", "long", "and", "prosper"], $function->applyFunction("words", "Live long and prosper", null));
        $this->assertEquals(["So", "long", "and", "thanks", "for", "all", "the", "fish"], $function->applyFunction("words", "So long and thanks for all the fish", null));
        $this->assertEquals(["Beware", "the", "Jabberwock", "my", "son"], $function->applyFunction("words", "Beware the Jabberwock my son", null));

    }

    public function testCanHashAStringCorrectly() {
        $function = new StringValueFunction();
        $this->assertTrue($function->doesFunctionApply("hash"));

        $this->assertEquals(hash("sha512", "string"), $function->applyFunction("hash", "string", null));

    }

    public function testCanHashStringWithMD5Correctly() {
        $function = new StringValueFunction();
        $this->assertTrue($function->doesFunctionApply("md5"));

        $this->assertEquals(md5("string"), $function->applyFunction("md5", "string", null));

    }

    public function testCanCorrectlyEvaluateFirstAndLastCharacterOfStrings() {
        $function = new StringValueFunction();
        $this->assertTrue($function->doesFunctionApply("startsWith"));
        $this->assertTrue($function->doesFunctionApply("endsWith"));

        $this->assertEquals("h", $function->applyFunction("startsWith", "hiya", null));
        $this->assertEquals("O", $function->applyFunction("startsWith", "Oops!", null));
        $this->assertEquals("L", $function->applyFunction("startsWith", "Long string", null));

        $this->assertEquals("a", $function->applyFunction("endsWith", "hiya", null));
        $this->assertEquals("!", $function->applyFunction("endsWith", "Oops!", null));
        $this->assertEquals("g", $function->applyFunction("endsWith", "Long string", null));

    }

    public function testCanConvertHTMLToTextCorrectly() {
        $function = new StringValueFunction();
        $this->assertTrue($function->doesFunctionApply("htmlToText"));

        $this->assertEquals("hello", $function->applyFunction("htmlToText", "<p>hello</p>", null));
        $this->assertEquals("there", $function->applyFunction("htmlToText", "<a href='https://google.com'>there</a>", null));
        $this->assertEquals("tests", $function->applyFunction("htmlToText", "<div><p>tests</p></div><button type=\"submit\"></button>", null));
    }

    public function testCanLeftPad() {
        $function = new StringValueFunction();
        $this->assertTrue($function->doesFunctionApply("leftPad"));

        $this->assertEquals("00001", $function->applyFunction("leftPad '0' 5", 1, null));
        $this->assertEquals("aardvark", $function->applyFunction("leftPad 'a' 8", "rdvark", null));
        $this->assertEquals(null, $function->applyFunction("leftPad 'a' 2", null, null));
        $this->assertEquals("\\n", $function->applyFunction("leftPad '\' 2", "n", null));
    }

}
