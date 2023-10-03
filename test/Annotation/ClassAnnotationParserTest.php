<?php


namespace Kinikit\Core\Annotation;

use Kinikit\Core\Reflection\TestNullableTypedPOPO;

include_once "autoloader.php";

class ClassAnnotationParserTest extends \PHPUnit\Framework\TestCase {

    private $classAnnotationParser;

    public function setUp(): void {
        $this->classAnnotationParser = new ClassAnnotationParser();
    }

    public function testCanGetClassAnnotationsFromAnnotatedClass() {

        $annotations = $this->classAnnotationParser->parse("Kinikit\Core\Annotation\TestAnnotatedClass");
        $this->assertTrue($annotations instanceof ClassAnnotations);

        $classAnnotations = $annotations->getClassAnnotations();
        $this->assertEquals(5, sizeof($classAnnotations));
        $this->assertEquals(new Annotation("mapped"), $classAnnotations["mapped"][0]);
        $this->assertEquals(new Annotation("ormTable", "active_record_container"), $classAnnotations["ormTable"][0]);
        $this->assertEquals(new Annotation("package", "Kinikit\Core\Util\Annotation"), $classAnnotations["package"][0]);
        $this->assertEquals(new Annotation("authors", "mark, nathan,lucien"), $classAnnotations["authors"][0]);
        $this->assertTrue(isset($classAnnotations["comment"]));

    }


    public function testCanGetFieldAnnotationsFromAnnotatedClass() {

        $annotations = $this->classAnnotationParser->parse("Kinikit\Core\Annotation\TestAnnotatedClass");
        $this->assertTrue($annotations instanceof ClassAnnotations);

        $fieldAnnotations = $annotations->getFieldAnnotations();
        $this->assertEquals(4, sizeof($fieldAnnotations));

        $tagAnnotations = $fieldAnnotations["tag"];
        $this->assertEquals(5, sizeof($tagAnnotations));
        $this->assertEquals(new Annotation("field"), $tagAnnotations["field"][0]);
        $this->assertEquals(new Annotation("primaryKey"), $tagAnnotations["primaryKey"][0]);
        $this->assertEquals(new Annotation("ormColumn", "tag_name"), $tagAnnotations["ormColumn"][0]);
        $this->assertEquals(new Annotation("validation", "required"), $tagAnnotations["validation"][0]);
        $this->assertTrue(isset($tagAnnotations["comment"]));


        $descAnnotations = $fieldAnnotations["description"];
        $this->assertEquals(3, sizeof($descAnnotations));
        $this->assertEquals(new Annotation("field"), $descAnnotations["field"][0]);
        $this->assertEquals(new Annotation("validation", "required,maxlength(255)"), $descAnnotations["validation"][0]);
        $this->assertTrue(isset($descAnnotations["comment"]));


        $arAnnotations = $fieldAnnotations["activeRecords"];
        $this->assertEquals(6, sizeof($arAnnotations));
        $this->assertEquals(new Annotation("relationship"), $arAnnotations["relationship"][0]);
        $this->assertEquals(new Annotation("multiple"), $arAnnotations["multiple"][0]);
        $this->assertEquals(new Annotation("relatedClass", "TestActiveRecord"), $arAnnotations["relatedClass"][0]);
        $this->assertEquals(new Annotation("relatedFields", "tag=>containerTag"), $arAnnotations["relatedFields"][0]);
        $this->assertEquals(new Annotation("orderingFields", "id DESC"), $arAnnotations["orderingFields"][0]);
        $this->assertTrue(isset($arAnnotations["comment"]));


        $npAnnotations = $fieldAnnotations["nonPersisted"];
        $this->assertEquals(1, sizeof($npAnnotations));
        $this->assertTrue(isset($npAnnotations["comment"]));


    }

    public function testCanGetAnnotationsForNullableTypes(){
        $classAnnotations = $this->classAnnotationParser->parse(TestNullableTypedPOPO::class);


        $str = "";

        [$a, $b] = [false, false];
        foreach ($classAnnotations->getMethodAnnotations() as $fieldAnnotationGroup){
            foreach ($fieldAnnotationGroup as $annotations){
                foreach ($annotations as $annotation) {
                    $str .= print_r($annotation, true);
                    if ($annotation->getValue() === "?string \$hat") {
                        $a = true;
                    }
                    if ($annotation->getValue() === "?TestTypedPOPO \$testTypedPOPO") {
                        $b = true;
                    }
                }
            }
        }

        $this->assertTrue($a);
        $this->assertTrue($b);
    }

}
