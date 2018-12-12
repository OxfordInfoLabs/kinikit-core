<?php

namespace Kinikit\Core\Util\Serialisation\JSON;


use Kinikit\Core\Exception\ClassNotSerialisableException;
use Kinikit\Core\Object\NoneSerialisable;

include_once "autoloader.php";

/**
 * Test cases for the Object to JSON Converter
 *
 */
class ObjectToJSONConverterTest extends \PHPUnit\Framework\TestCase {
	
	public function testPrimitiveValuesSimplyPassThroughJSONConverterIntact() {
		
		$int = 15;
		$boolean = true;
		$string = "My Friend Emma";
		
		$converter = new ObjectToJSONConverter ();
		$this->assertEquals ( $int, $converter->convert ( $int ) );
		$this->assertEquals ( "true", $converter->convert ( $boolean ) );
		$this->assertEquals ( '"' . $string . '"', $converter->convert ( $string ) );
	
	}
	
	public function testArraysOfPrimitiveValuesGetMappedToJSONCorrectly() {
		
		$values = array (10, 20, "Marko", "Polo", 30, 50, 90 );
		
		$converter = new ObjectToJSONConverter ();
		$this->assertEquals ( '[10,20,"Marko","Polo",30,50,90]', $converter->convert ( $values ) );
	
	}
	
	// Check that simple objects get converted to JSON
	public function testSimplePOPOObjectsGetConvertedAccordinglyToStandardJSONWithClassnameInsertedAsProperty() {
		
		$object = new SimpleJSONObject ( "Marko", 23, "In the garden" );
		
		$converter = new ObjectToJSONConverter ();
		$json = $converter->convert ( $object );
		
		$this->assertEquals ( '{"myMember":"Marko","ourMember":"In the garden","yourMember":23}', $json );
	
	}
	
	public function testArraysOfSimplePOPOObjectsGetConvertedAccordinglyToStandardJSON() {
		
		$object1 = new SimpleJSONObject ( "Marko", 23, "In the garden" );
		$object2 = new SimpleJSONObject ( "Drongo", 45, "At Home" );
		$object3 = new SimpleJSONObject ( "Peter", 99, "Up the road" );
		
		$array = array ($object1, $object2, $object3 );
		
		$converter = new ObjectToJSONConverter ();
		$json = $converter->convert ( $array );
		
		$this->assertEquals ( '[{"myMember":"Marko","ourMember":"In the garden","yourMember":23},{"myMember":"Drongo","ourMember":"At Home","yourMember":45},{"myMember":"Peter","ourMember":"Up the road","yourMember":99}]', $json );
	
	}
	
	public function testAssociativeArraysOfSimplePOPOObjectGetConvertedAccordinglyToJSONObjects() {
		
		$object1 = new SimpleJSONObject ( "Marko", 23, "In the garden" );
		$object2 = new SimpleJSONObject ( "Drongo", 45, "At Home" );
		$object3 = new SimpleJSONObject ( "Peter", 99, "Up the road" );
		
		$array = array ("Mark" => $object1, "Amy" => $object2, "Philip" => $object3 );
		
		$converter = new ObjectToJSONConverter ();
		$json = $converter->convert ( $array );
		
		$this->assertEquals ( '{"Mark":{"myMember":"Marko","ourMember":"In the garden","yourMember":23},"Amy":{"myMember":"Drongo","ourMember":"At Home","yourMember":45},"Philip":{"myMember":"Peter","ourMember":"Up the road","yourMember":99}}', $json );
	
	}
	
	public function testObjectWithNestedObjectsAndArraysAreConvertedCorrectly() {
		
		$object = new SimpleJSONObject ( "Bad News", new SimpleJSONObject ( "Baby love", new SimpleJSONObject ( "School dinner", 12, 3 ), 35 ), array (5, 7, new SimpleJSONObject ( "Badger", 1, 1 ) ) );
		$converter = new ObjectToJSONConverter ();
		$json = $converter->convert ( $object );
		$this->assertEquals ( '{"myMember":"Bad News","ourMember":[5,7,{"myMember":"Badger","ourMember":1,"yourMember":1}],"yourMember":{"myMember":"Baby love","ourMember":35,"yourMember":{"myMember":"School dinner","ourMember":3,"yourMember":12}}}', $json );
	}
	
	public function testAttemptToConvertNoneSerialisableObjectThrowsException() {
        self::assertTrue(true);
		$object = new NoneSerialisable ();
		$converter = new ObjectToJSONConverter ();
		
		try {
			$json = $converter->convert ( $object );
			$this->fail ( "Should have thrown here" );
		} catch ( ClassNotSerialisableException $e ) {
			// Success
		}
	}

}

?>