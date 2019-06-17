<?php

namespace Kinikit\Core\Util\Serialisation\XML;

use Kinikit\Core\Exception\ClassNotConstructableException;
use Kinikit\Core\Exception\ClassNotFoundException;
use Kinikit\Core\Exception\ClassNotSerialisableException;

include_once "autoloader.php";

/**
 * Test case for the XML to Object Converter.
 *
 */
class XMLToObjectConverterTest extends \PHPUnit\Framework\TestCase {

    private $converter;
    private $objectToXMLConverter;

    public function setUp(): void {
        $this->converter = new XMLToObjectConverter ();
        $this->objectToXMLConverter = new ObjectToXMLConverter ();
    }

    public function testIfTextNodePassedToConverterAppropriatePrimitiveIsReturned() {

        $stringTextElement = $this->getFirstElementFromXMLString("<Param>Monkey man</Param>");
        $trueBooleanElement = $this->getFirstElementFromXMLString("<Param>True</Param>");
        $falseBooleanElement = $this->getFirstElementFromXMLString("<Param>False</Param>");
        $numericElement = $this->getFirstElementFromXMLString("<Param>0.5646</Param>");

        $this->assertEquals("Monkey man", $this->converter->convertDOM($stringTextElement));
        $this->assertEquals(true, $this->converter->convertDOM($trueBooleanElement));
        $this->assertEquals(false, $this->converter->convertDOM($falseBooleanElement));
        $this->assertEquals(0.5646, $this->converter->convertDOM($numericElement));

    }

    public function testIfArrayOfPrimitivesPassedToConverterAppropriateArrayIsReturned() {

        // Try one
        $arrayXML = "<Array><Value>Marko</Value><Value>TRUE</Value><Value>1.67</Value></Array>";
        $arrayElement = $this->getDocumentElementFromXMLString($arrayXML);
        $expectedValue = array("Marko", true, 1.67);
        $this->assertEquals($expectedValue, $this->converter->convertDOM($arrayElement));

        // Try another one
        $arrayXML = "<Array><Value>Jumping in the house of God</Value><Value>1.2</Value></Array>";
        $arrayElement = $this->getDocumentElementFromXMLString($arrayXML);
        $expectedValue = array("Jumping in the house of God", 1.2);
        $this->assertEquals($expectedValue, $this->converter->convertDOM($arrayElement));

    }

    public function testIfKeyedArrayPassedToConverterAssociativeArrayReturned() {

        $arrayXML = '<Array><Value key="Dad">John</Value><Value key="Mum">Diane</Value><Value key="Son">Mark</Value></Array>';
        $arrayElement = $this->getDocumentElementFromXMLString($arrayXML);
        $expectedValue = array("Dad" => "John", "Mum" => "Diane", "Son" => "Mark");
        $this->assertEquals($expectedValue, $this->converter->convertDOM($arrayElement));

    }

    public function testIfNoneExistentClassNamePassedToConverterForMappingErrorIsThrown() {
        self::assertTrue(true);
        $badClassXML = "<MyMonkey></MyMonkey>";
        $element = $this->getDocumentElementFromXMLString($badClassXML);

        try {
            $this->converter->convertDOM($element);
            $this->fail("Should have thrown an exception here");

        } catch (ClassNotFoundException $e) {
            // Success
        }

    }

    public function testIfClassPassedToConverterHasNoBlankConstructorThrowException() {
        self::assertTrue(true);
        $badClassXML = '<TestBadConstructorObject phpNameSpace="Kinikit\Core\Util\Serialisation\XML"></TestBadConstructorObject>';
        $element = $this->getDocumentElementFromXMLString($badClassXML);

        try {
            $this->converter->convertDOM($element);
            $this->fail("Should have thrown an exception here");

        } catch (ClassNotConstructableException $e) {
            // Success
        }

    }

    public function testIfNodeForNonSerialisableSimpleObjectPassedClassNotSerialisableExceptionIsRaised() {
        self::assertTrue(true);
        $testObject1XML = '<TestObject0 phpNameSpace="Kinikit\Core\Util\Serialisation\XML"><school>Arbury Primary School</school><teacher>Mr Hessey</teacher></TestObject0>';
        $element = $this->getDocumentElementFromXMLString($testObject1XML);

        try {
            $this->converter->convertDOM($element);
            $this->fail("Should have thrown here");
        } catch (ClassNotSerialisableException $e) {
            // Success
        }
    }

    public function testIfNodeForStandardObjectWithPublicAccessorsPassedToConverterItIsMapped() {

        $testObject1XML = '<TestObject1 phpNameSpace="Kinikit\Core\Util\Serialisation\XML"><Name>Mr Man</Name><Phone>01865 787879</Phone><Age>34</Age><Notes><Array><Value>pierre</Value><Value>shopping</Value></Array></Notes></TestObject1>';
        $element = $this->getDocumentElementFromXMLString($testObject1XML);
        $expectedValue = new TestObject1 ("Mr Man", "01865 787879", 34, array("pierre", "shopping"));

        $this->assertEquals($expectedValue, $this->converter->convertDOM($element));
    }

    public function testNodeForProtectedObjectsGetMappedOnSubmissionToConverter() {
        $testObject2XML = '<TestObject2 phpNameSpace="Kinikit\Core\Util\Serialisation\XML"><Street>Oxford Road</Street><City>London</City><County>Middlesex</County></TestObject2>';
        $element = $this->getDocumentElementFromXMLString($testObject2XML);
        $expectedValue = new TestObject2 ("Oxford Road", "London", "Middlesex");

        $this->assertEquals($expectedValue, $this->converter->convertDOM($element));

        $testObject3XML = '<TestObject3 phpNameSpace="Kinikit\Core\Util\Serialisation\XML"><profession>Accountancy</profession><title>Auditor</title><salary>56000</salary></TestObject3>';
        $element = $this->getDocumentElementFromXMLString($testObject3XML);
        $expectedValue = new TestObject3 ("Accountancy", "Auditor", 56000);

        $this->assertEquals($expectedValue, $this->converter->convertDOM($element));

    }

    public function testComplexNodeGetsMappedOnSubmissionToConverter() {

        $details = new TestObject1 ("marko", "01865 989899", 13, array("monkey", "gorilla", "ape"));
        $address1 = new TestObject2 ("3 the street", "oxford", "oxon");
        $address2 = new TestObject2 ("3 the lane", "warwick", "war");
        $jobDetails = new TestObject3 ("Law", "Solicitor", 97000);

        $complexXML = '<TestComplexObject phpNameSpace="Kinikit\Core\Util\Serialisation\XML"><details>' . $this->objectToXMLConverter->convert($details) . "</details>" . "<addresses><Array>" . $this->objectToXMLConverter->convert($address1) . $this->objectToXMLConverter->convert($address2) . "</Array></addresses>" . "<jobDetails>" . $this->objectToXMLConverter->convert($jobDetails) . "</jobDetails></TestComplexObject>";
        $element = $this->getDocumentElementFromXMLString($complexXML);

        $expectedValue = new TestComplexObject ($details, array($address1, $address2), $jobDetails);

        $this->assertEquals($expectedValue, $this->converter->convertDOM($element));

    }

    public function testAttributesMayBeUsedInsteadAndTheseGetMappedToPropertiesOnClassesAsWell() {

        $testObject1XML = '<TestObject1 phpNameSpace="Kinikit\Core\Util\Serialisation\XML" age="33" phone="01865 778787" name="Mark"  />';

        $element = $this->getDocumentElementFromXMLString($testObject1XML);
        $expectedValue = new TestObject1 ("Mark", "01865 778787", 33);

        $this->assertEquals($expectedValue, $this->converter->convertDOM($element));
    }

    public function testIfArrayTagOmittedForArrayWithSingleValueEntryThisIsMappedToSingleValue() {

        $testObject1XML = '<TestObject1 phpNameSpace="Kinikit\Core\Util\Serialisation\XML"><name>Mr Man</name><phone>01865 787879</phone><age>34</age><notes><Value>pierre</Value></notes></TestObject1>';
        $element = $this->getDocumentElementFromXMLString($testObject1XML);
        $expectedValue = new TestObject1 ("Mr Man", "01865 787879", 34, "pierre");

        $this->assertEquals($expectedValue, $this->converter->convertDOM($element));

    }

    public function testIfArrayTagOmittedForArrayWithMultipleEntriesThisIsMappedCorrectly() {

        $testObject1XML = '<TestObject1 phpNameSpace="Kinikit\Core\Util\Serialisation\XML"><name>Mr Man</name><phone>01865 787879</phone><age>34</age><notes><Value>pierre</Value><Value>marko</Value><Value>Pierre</Value></notes></TestObject1>';
        $element = $this->getDocumentElementFromXMLString($testObject1XML);
        $expectedValue = new TestObject1 ("Mr Man", "01865 787879", 34, array("pierre", "marko", "Pierre"));
        $this->assertEquals($expectedValue, $this->converter->convertDOM($element));

    }

    public function testCanPassStringDirectlyToConvertXMLString() {

        $testObject1XML = '<TestObject1 phpNameSpace="Kinikit\Core\Util\Serialisation\XML"><name>Mr Man</name><phone>01865 787879</phone><age>34</age><notes><Value>pierre</Value><Value>marko</Value><Value>Pierre</Value></notes></TestObject1>';
        $expectedValue = new TestObject1 ("Mr Man", "01865 787879", 34, array("pierre", "marko", "Pierre"));

        $this->assertEquals($expectedValue, $this->converter->convert($testObject1XML));

    }

    public function testIfKeyAttributeUsedForObjectsInArraysTheseAreMappedToAnAssociativeArrayAlso() {

        $testXML = '<Array><TestObject2 phpNameSpace="Kinikit\Core\Util\Serialisation\XML" key="oxford"><Street>Oxford Road</Street><City>London</City><County>Middlesex</County></TestObject2><TestObject2 phpNameSpace="Kinikit\Core\Util\Serialisation\XML" key="cambridge"><Street>Cambridge Road</Street><City>Cambridge</City><County>Cambs</County></TestObject2></Array>';

        $result = $this->converter->convert($testXML);

        $this->assertEquals(2, sizeof($result));

        $this->assertEquals(new TestObject2 ("Oxford Road", "London", "Middlesex", "oxford"), $result ["oxford"]);
        $this->assertEquals(new TestObject2 ("Cambridge Road", "Cambridge", "Cambs", "cambridge"), $result ["cambridge"]);

    }

    public function testIfTextElementExistsAsAttributeInObjectThisIsMappedToTheTextMemberOfClass() {

        $testXML = '<TestObject4 phpNameSpace="Kinikit\Core\Util\Serialisation\XML" street="Oxford Road" city="London" county="Middlesex">Something special</TestObject4>';

        $result = $this->converter->convert($testXML);

        $this->assertEquals(new TestObject4 ("Oxford Road", "London", "Middlesex", "Something special"), $result);

    }

    public function testCanSupplyAnOptionalMapOfTagNamesToConvertMethodsWhichMapXmlTagsToClassNames() {

        $testXML = '<MyTag street="My Street" city="Oxford" county="Oxon"></MyTag>';
        $converter = new XMLToObjectConverter (array("MyTag" => "Kinikit\Core\Util\Serialisation\XML\TestObject4"));
        $result = $converter->convert($testXML);

        $this->assertEquals(new TestObject4 ("My Street", "Oxford", "Oxon"), $result);

    }

    public function testCanCorrectlyMapNestedArraysOfValues() {

        $testXML = '<TestObject1 phpNameSpace="Kinikit\Core\Util\Serialisation\XML" >
		<name>
			<Array>
				<Value>
					<Array>
						<Value>1</Value>
						<Value>2</Value>
						<Value>5</Value>
					</Array>
				</Value>
				<Value>Monster</Value>
			</Array>
		</name>
		<phone>
			<Value>
				<Value>7</Value>
				<Value>9</Value>
			</Value>
			<Value>
				<Value>10</Value>
				<Value>17</Value>
			</Value>
		</phone>
		</TestObject1>';

        $converter = new XMLToObjectConverter ();
        $result = $converter->convert($testXML);

        $school = $result->getName();
        $teacher = $result->getPhone();

        $this->assertEquals(array(array(1, 2, 5), "Monster"), $school);
        $this->assertEquals(array(array(7, 9), array(10, 17)), $teacher);

    }

   


    private function getFirstElementFromXMLString($xmlString) {
        return $this->getDocumentElementFromXMLString($xmlString)->childNodes->item(0);
    }

    private function getDocumentElementFromXMLString($xmlString) {
        $document = new \DOMDocument ();
        $document->loadXML($xmlString);
        return $document->documentElement;
    }

}

?>
