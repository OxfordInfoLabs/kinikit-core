<?php


namespace Kinikit\Core\Util\Serialisation\JSON;


use Kinikit\Core\Exception\ClassNotConstructableException;
use Kinikit\Core\Exception\ClassNotFoundException;
use Kinikit\Core\Exception\ClassNotSerialisableException;

include_once "autoloader.php";


/**
 * Test cases for the JSON to object converter
 *
 */
class JSONToObjectConverterTest extends \PHPUnit\Framework\TestCase {

    public function testPrimitiveValuesPassThroughConversionIntact() {

        $int = 15;
        $boolean = true;
        $string = '"My Friend Emma"';

        $converter = new JSONToObjectConverter ();
        $this->assertEquals($int, $converter->convert($int));
        $this->assertEquals(1, $converter->convert($boolean));
        $this->assertEquals("My Friend Emma", $converter->convert($string));

    }

    public function testArraysOfPrimitivesAreMappedCorrectly() {

        $json = '[1,3,5,7,9,11]';
        $json2 = '["Mark","Amy","Peter","Paul"]';

        $converter = new JSONToObjectConverter ();
        $this->assertEquals(array(1, 3, 5, 7, 9, 11), $converter->convert($json));
        $this->assertEquals(array("Mark", "Amy", "Peter", "Paul"), $converter->convert($json2));

    }

    public function testAssociativeArraysOfPrimitivesAreMappedCorrectly() {

        $json = '{"a" :1, "b":5, "c":"penny"}';
        $json2 = '{"Bad":"Bunny", "5":"Flag", "Seven":"England"}';

        $converter = new JSONToObjectConverter ();
        $this->assertEquals(array("a" => 1, "b" => 5, "c" => "penny"), $converter->convert($json));
        $this->assertEquals(array("Bad" => "Bunny", 5 => "Flag", "Seven" => "England"), $converter->convert($json2));

    }

  
}

?>