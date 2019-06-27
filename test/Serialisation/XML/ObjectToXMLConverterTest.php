<?php

namespace Kinikit\Core\Serialisation\XML;

use Kinikit\Core\Exception\ClassNotSerialisableException;

include_once "autoloader.php";


/**
 * Test cases for the object to XML Converter.
 *
 */
class ObjectToXMLConverterTest extends \PHPUnit\Framework\TestCase {

    private $converter;

    public function setUp(): void {
        $this->converter = new ObjectToXMLConverter ();
    }

    public function testPrimitiveValuesGetReturnedAsTheyAreOnConversion() {

        $string = "Boo ska boo";
        $number = 1.456;
        $trueBoolean = true;
        $falseBoolean = false;

        $this->assertEquals("<![CDATA[" . $string . "]]>", $this->converter->convert($string));
        $this->assertEquals("<![CDATA[" . $number . "]]>", $this->converter->convert($number));
        $this->assertEquals("1", $this->converter->convert($trueBoolean));
        $this->assertEquals("0", $this->converter->convert($falseBoolean));

    }

    public function testArraysOfPrimitiveValuesGetWrappedInArrayAndValueTags() {

        // String Array
        $array = array("Matthew", "Mark", "Luke", "John");
        $output = $this->converter->convert($array);
        $expectedOutput = "<Array><Value><![CDATA[Matthew]]></Value><Value><![CDATA[Mark]]></Value><Value><![CDATA[Luke]]></Value><Value><![CDATA[John]]></Value></Array>";
        $this->assertEquals($expectedOutput, $output);

        // Numeric Array
        $array = array(1, 5.6, 6, 7.5);
        $output = $this->converter->convert($array);
        $expectedOutput = "<Array><Value><![CDATA[1]]></Value><Value><![CDATA[5.6]]></Value><Value><![CDATA[6]]></Value><Value><![CDATA[7.5]]></Value></Array>";
        $this->assertEquals($expectedOutput, $output);

        // Boolean Array
        $array = array(true, false, true);
        $output = $this->converter->convert($array);
        $expectedOutput = "<Array><Value>1</Value><Value>0</Value><Value>1</Value></Array>";
        $this->assertEquals($expectedOutput, $output);

    }

    public function testAssociativeArraysHaveAnAdditionalKeyAttributeSetOnTheValueTag() {

        $array = array("Dad" => "John", "Mum" => "Diane", "Oldest" => "Mark", "Middle" => "Luke", "Youngest" => "Tim");
        $output = $this->converter->convert($array);
        $expectedOutput = '<Array><Value key="Dad"><![CDATA[John]]></Value><Value key="Mum"><![CDATA[Diane]]></Value><Value key="Oldest"><![CDATA[Mark]]></Value><Value key="Middle"><![CDATA[Luke]]></Value><Value key="Youngest"><![CDATA[Tim]]></Value></Array>';
        $this->assertEquals($expectedOutput, $output);

    }

    public function testNoneSerialisableObjectsThrowAnExceptionOnAttemptedConversion() {
        self::assertTrue(true);
        // Try one
        $testObject0 = new TestObject0 ("Arbury Primary School", "Mr Hessey");
        try {
            $this->converter->convert($testObject0);
            $this->fail("Should have thrown here");
        } catch (ClassNotSerialisableException $e) {
            // Success
        }

    }

    public function testStandardObjectWithPrivateMembersAndPublicGettersAndSettersGetsMappedAsExpected() {

        // Try one
        $testObject1 = new TestObject1 ("Marko", "01865 989889", 13, array("run it by me", "shop around", "go fishing"));
        $expectedOutput = '<TestObject1 phpNameSpace="Kinikit\Core\Serialisation\XML"><name><![CDATA[Marko]]></name><phone><![CDATA[01865 989889]]></phone><age><![CDATA[13]]></age><notes><Array><Value><![CDATA[run it by me]]></Value><Value><![CDATA[shop around]]></Value><Value><![CDATA[go fishing]]></Value></Array></notes></TestObject1>';
        $this->assertEquals($expectedOutput, $this->converter->convert($testObject1));

        // Try another
        $testObject1 = new TestObject1 ("Pierre", "718288", 39, array("Go now"));
        $expectedOutput = '<TestObject1 phpNameSpace="Kinikit\Core\Serialisation\XML"><name><![CDATA[Pierre]]></name><phone><![CDATA[718288]]></phone><age><![CDATA[39]]></age><notes><Array><Value><![CDATA[Go now]]></Value></Array></notes></TestObject1>';
        $this->assertEquals($expectedOutput, $this->converter->convert($testObject1));

    }

    public function testProtectedMemberObjectGetsMappedCorrectly() {

        $testObject2 = new TestObject2 ("3 Hawlings Row", "Oxford", "Oxon");
        $expectedOutput = '<TestObject2 phpNameSpace="Kinikit\Core\Serialisation\XML"><street><![CDATA[3 Hawlings Row]]></street><city><![CDATA[Oxford]]></city><county><![CDATA[Oxon]]></county></TestObject2>';
        $this->assertEquals($expectedOutput, $this->converter->convert($testObject2));

    }

    public function testPrivateFieldProtectedAccessorObjectGetsMappedCorrectly() {

        $testObject3 = new TestObject3 ("Medicine", "Doctor", 37750);
        $expectedOutput = '<TestObject3 phpNameSpace="Kinikit\Core\Serialisation\XML"><profession><![CDATA[Medicine]]></profession><title><![CDATA[Doctor]]></title><salary><![CDATA[37750]]></salary></TestObject3>';
        $this->assertEquals($expectedOutput, $this->converter->convert($testObject3));

    }

    public function testComplexObjectGetsMappedCorrectly() {

        $details = new TestObject1 ("marko", "01865 878787", 3, array("boo"));

        $address1 = new TestObject2 ("3 hawlings row", "oxford", "oxon");
        $address2 = new TestObject2 ("3 shepherds bush", "london", "middlesex");
        $addresses = array($address1, $address2);

        $job = new TestObject3 ("IT Pro", "Systems Architect", 99000);

        $complexObject = new TestComplexObject ($details, $addresses, $job);

        $expectedOutput = '<TestComplexObject phpNameSpace="Kinikit\Core\Serialisation\XML"><details>' . $this->converter->convert($details) . "</details>" . "<addresses><Array><Value>" . $this->converter->convert($address1) . "</Value><Value>" . $this->converter->convert($address2) . "</Value></Array></addresses>" . "<jobDetails>" . $this->converter->convert($job) . "</jobDetails>" . "</TestComplexObject>";

        $this->assertEquals($expectedOutput, $this->converter->convert($complexObject));

    }

    public function testObjectsWithMagicTextElementGetWrittenOutWithAllOtherMembersAsAttributes() {

        $testObject = new TestObject4 ("The Lane", "Hove", "East Sussex", "Some other random text");

        $expectedOutput = '<TestObject4 phpNameSpace="Kinikit\Core\Serialisation\XML" street="The Lane" city="Hove" county="East Sussex">Some other random text</TestObject4>';

        $this->assertEquals($expectedOutput, $this->converter->convert($testObject));

    }

    public function testAssociativeArrayObjectsAlwaysHaveAKeyAttributeRatherThanNestedKeyElement() {

        $testObject = new TestObject2 ("The Lane", "Hove", "East Sussex", "sussex");

        $expectedOutput = '<TestObject2 phpNameSpace="Kinikit\Core\Serialisation\XML" key="sussex"><street><![CDATA[The Lane]]></street><city><![CDATA[Hove]]></city><county><![CDATA[East Sussex]]></county></TestObject2>';

        $this->assertEquals($expectedOutput, $this->converter->convert($testObject));

        // Now bulk up into an array and test again
        $testObject2 = new TestObject2 ("My lane", "Brighton", "ES", "brighton");
        $array = array($testObject, $testObject2);

        $expectedOutput = '<Array><Value><TestObject2 phpNameSpace="Kinikit\Core\Serialisation\XML" key="sussex"><street><![CDATA[The Lane]]></street><city><![CDATA[Hove]]></city><county><![CDATA[East Sussex]]></county></TestObject2></Value>' . '<Value><TestObject2 phpNameSpace="Kinikit\Core\Serialisation\XML" key="brighton"><street><![CDATA[My lane]]></street><city><![CDATA[Brighton]]></city><county><![CDATA[ES]]></county></TestObject2></Value></Array>';

        $this->assertEquals($expectedOutput, $this->converter->convert($array));

    }

    public function testBlankTextEntriesAreHandledCorrectly() {

        $testWithText = new TestObjectWithText ("Marko");
        $this->assertEquals('<TestObjectWithText phpNameSpace="Kinikit\Core\Serialisation\XML" otherMember="">Marko</TestObjectWithText>', $this->converter->convert($testWithText));

        $testBlankContent = new TestObjectWithText ("");
        $this->assertEquals('<TestObjectWithText phpNameSpace="Kinikit\Core\Serialisation\XML" otherMember=""></TestObjectWithText>', $this->converter->convert($testBlankContent));

        $testArrayOfText = array(new TestObjectWithText ("Mark"), new TestObjectWithText (""));
        $this->assertEquals('<Array><Value><TestObjectWithText phpNameSpace="Kinikit\Core\Serialisation\XML" otherMember="">Mark</TestObjectWithText></Value><Value><TestObjectWithText phpNameSpace="Kinikit\Core\Serialisation\XML" otherMember=""></TestObjectWithText></Value></Array>', $this->converter->convert($testArrayOfText));

    }


    public function testCanConstructWithTagMappingsAndTheseGetReplacedInOutput() {

        $converter = new ObjectToXMLConverter(array("TestObject3" => "Details", "TestObject1" => "Address", "TestObject2" => "Monkey"));

        $complexObject = new TestComplexObject(array(new TestObject3("Professor", "Sir", 22000), new TestObject3("Coder", "Mr", 35000)), new TestObject1("Pineapple", "01223 355931", 25, new TestObject2("Magdalen Centre", "Oxford", "Oxon", "oxil")));

        $expectedOutput = '<TestComplexObject phpNameSpace="Kinikit\Core\Serialisation\XML">';
        $expectedOutput .= "<details><Array><Value><Details><profession><![CDATA[Professor]]></profession><title><![CDATA[Sir]]></title><salary><![CDATA[22000]]></salary></Details></Value><Value><Details><profession><![CDATA[Coder]]></profession><title><![CDATA[Mr]]></title><salary><![CDATA[35000]]></salary></Details></Value></Array></details>";
        $expectedOutput .= '<addresses><Address><name><![CDATA[Pineapple]]></name><phone><![CDATA[01223 355931]]></phone><age><![CDATA[25]]></age><notes><Monkey key="oxil"><street><![CDATA[Magdalen Centre]]></street><city><![CDATA[Oxford]]></city><county><![CDATA[Oxon]]></county></Monkey></notes></Address></addresses>';
        $expectedOutput .= "<jobDetails></jobDetails>";
        $expectedOutput .= "</TestComplexObject>";

        $this->assertEquals($expectedOutput, $converter->convert($complexObject));

    }


    public function testCanOmitContainerElementsForOutputWithArrays() {

        $complexObject = new TestComplexObject(array(new TestObject3("Professor", "Sir", 22000), new TestObject3("Coder", "Mr", 35000)));

        $expectedOutput = '<TestComplexObject phpNameSpace="Kinikit\Core\Serialisation\XML">';
        $expectedOutput .= "<details>";
        $expectedOutput .= '<TestObject3 phpNameSpace="Kinikit\Core\Serialisation\XML"><profession><![CDATA[Professor]]></profession><title><![CDATA[Sir]]></title><salary><![CDATA[22000]]></salary></TestObject3>';
        $expectedOutput .= '<TestObject3 phpNameSpace="Kinikit\Core\Serialisation\XML"><profession><![CDATA[Coder]]></profession><title><![CDATA[Mr]]></title><salary><![CDATA[35000]]></salary></TestObject3>';
        $expectedOutput .= "</details><addresses></addresses><jobDetails></jobDetails></TestComplexObject>";

        $this->assertEquals($expectedOutput, $this->converter->convert($complexObject, false, true));


    }


    public function testOmitSingletonClassFlagRemovesClassTagsFromSingleObjectMembers() {

        $complexObject = new TestComplexObject(new TestObject3("Professor", "Sir", 22000),
            new TestObject2("Magdalen Centre", "Oxford", "oxon"),
            array(new TestObject1("Mark", "712", "12", "Bing"), new TestObject1("Philip", "713", "15", "Bong")));


        $expectedOutput = "<details>";
        $expectedOutput .= "<profession><![CDATA[Professor]]></profession><title><![CDATA[Sir]]></title><salary><![CDATA[22000]]></salary>";
        $expectedOutput .= "</details>";
        $expectedOutput .= "<addresses>";
        $expectedOutput .= "<street><![CDATA[Magdalen Centre]]></street><city><![CDATA[Oxford]]></city><county><![CDATA[oxon]]></county>";
        $expectedOutput .= "</addresses>";
        $expectedOutput .= "<jobDetails>";
        $expectedOutput .= "<Kinikit\Core\Serialisation\XML\TestObject1><name><![CDATA[Mark]]></name><phone><![CDATA[712]]></phone><age><![CDATA[12]]></age><notes><![CDATA[Bing]]></notes></Kinikit\Core\Serialisation\XML\TestObject1>";
        $expectedOutput .= "<Kinikit\Core\Serialisation\XML\TestObject1><name><![CDATA[Philip]]></name><phone><![CDATA[713]]></phone><age><![CDATA[15]]></age><notes><![CDATA[Bong]]></notes></Kinikit\Core\Serialisation\XML\TestObject1>";
        $expectedOutput .= "</jobDetails>";


        $this->assertEquals($expectedOutput, $this->converter->convert($complexObject, false, true, false));

    }


    public function testCanIgnoreBlankMembersIfRequired() {

        $complexObject = new TestComplexObject(new TestObject3("Professor", "", 22000),
            new TestObject2("Magdalen Centre", "Oxford", null), "");


        $expectedOutput = '<TestComplexObject phpNameSpace="Kinikit\Core\Serialisation\XML"><details><TestObject3 phpNameSpace="Kinikit\Core\Serialisation\XML">';
        $expectedOutput .= "<profession><![CDATA[Professor]]></profession><salary><![CDATA[22000]]></salary>";
        $expectedOutput .= "</TestObject3></details>";
        $expectedOutput .= "<addresses>";
        $expectedOutput .= '<TestObject2 phpNameSpace="Kinikit\Core\Serialisation\XML"><street><![CDATA[Magdalen Centre]]></street><city><![CDATA[Oxford]]></city>';
        $expectedOutput .= "</TestObject2></addresses></TestComplexObject>";


        $this->assertEquals($expectedOutput, $this->converter->convert($complexObject, true));

    }


}

?>
