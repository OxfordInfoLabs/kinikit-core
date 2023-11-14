<?php


namespace Kinikit\Core\Serialisation\JSON;


use Kinikit\Core\DependencyInjection\Container;
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

        $converter = Container::instance()->get(JSONToObjectConverter::class);
        $this->assertEquals($int, $converter->convert($int));
        $this->assertEquals(1, $converter->convert($boolean));
        $this->assertEquals("My Friend Emma", $converter->convert($string));

    }

    public function testArraysOfPrimitivesAreMappedCorrectly() {

        $json = '[1,3,5,7,9,11]';
        $json2 = '["Mark","Amy","Peter","Paul"]';

        $converter = Container::instance()->get(JSONToObjectConverter::class);
        $this->assertEquals(array(1, 3, 5, 7, 9, 11), $converter->convert($json));
        $this->assertEquals(array("Mark", "Amy", "Peter", "Paul"), $converter->convert($json2));

    }

    public function testAssociativeArraysOfPrimitivesAreMappedCorrectly() {

        $json = '{"a" :1, "b":5, "c":"penny"}';
        $json2 = '{"Bad":"Bunny", "5":"Flag", "Seven":"England"}';

        $converter = Container::instance()->get(JSONToObjectConverter::class);
        $this->assertEquals(array("a" => 1, "b" => 5, "c" => "penny"), $converter->convert($json));
        $this->assertEquals(array("Bad" => "Bunny", 5 => "Flag", "Seven" => "England"), $converter->convert($json2));

    }


    public function testDateObjectsSurviveSerialisationCorrectly() {

        $objectToJson = Container::instance()->get(ObjectToJSONConverter::class);
        $json = $objectToJson->convert(date_create_from_format("Y-m-d H:i:s", "2020-01-01 10:00:02", new \DateTimeZone("UTC")));


        $converter = Container::instance()->get(JSONToObjectConverter::class);
        $converted = $converter->convert($json, \DateTime::class);

        $this->assertInstanceOf(\DateTime::class, $converted);
        $this->assertEquals("2020-01-01 10:00:02", $converted->format("Y-m-d H:i:s"));

    }


}

?>
