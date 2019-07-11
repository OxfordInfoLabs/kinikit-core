<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 16/08/2018
 * Time: 14:50
 */

namespace Kinikit\Core\Annotation;

include_once "autoloader.php";

class ClassAnnotationsTest extends \PHPUnit\Framework\TestCase {

    public function testCanGetAllAnnotationsMatchingTag() {

        $classAnnotationParser = new ClassAnnotationParser();

        $annotations = $classAnnotationParser->parse("Kinikit\Core\Annotation\TestAnnotatedClass");
        $this->assertTrue($annotations instanceof ClassAnnotations);

        $matchingAnnotations = $annotations->getFieldAnnotationsForMatchingTag("validation");
        $this->assertEquals(2, sizeof($matchingAnnotations));
        $this->assertEquals(new Annotation("validation", "required"), $matchingAnnotations["tag"][0]);
        $this->assertEquals(new Annotation("validation", "required,maxlength(255)"), $matchingAnnotations["description"][0]);


    }

}
