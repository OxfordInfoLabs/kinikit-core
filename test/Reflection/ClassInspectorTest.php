<?php

namespace Kinikit\Core\Reflection;

use Kinikit\Core\Annotation\ClassAnnotationParser;

/**
 * Class inspector test cases
 *
 * Class ClassInspectorTest
 */
class ClassInspectorTest extends \PHPUnit\Framework\TestCase {

    public function testCanGetClassLevelAttributesFromInspector() {

        $classInspector = new ClassInspector(TestTypedPOPO::class);
        $this->assertEquals("\Kinikit\Core\Reflection\TestTypedPOPO", $classInspector->getClassName());
        $this->assertEquals("TestTypedPOPO", $classInspector->getShortClassName());
        $this->assertEquals("Kinikit\Core\Reflection", $classInspector->getNamespace());

        $this->assertEquals(["Annotation" => "\Kinikit\Core\Annotation\Annotation",
            "AccessDeniedException" => "\Kinikit\Core\Exception\AccessDeniedException",
            "ObjectInterceptor" => "\Kinikit\Core\DependencyInjection\ObjectInterceptor"], $classInspector->getDeclaredNamespaceClasses());


        $this->assertEquals(ClassAnnotationParser::instance()->parse(TestTypedPOPO::class)->getClassAnnotations(), $classInspector->getClassAnnotations());

    }


    public function testCanGetConstructorAndPublicMethodsForTypedClass() {

        $classInspector = new ClassInspector(TestTypedPOPO::class);

        $reflectionClass = new \ReflectionClass(TestTypedPOPO::class);
        $annotations = ClassAnnotationParser::instance()->parse(TestTypedPOPO::class);
        $declaredNamespaceClasses = ["Annotation" => "\Kinikit\Core\Annotation\Annotation",
            "AccessDeniedException" => "\Kinikit\Core\Exception\AccessDeniedException",
            "ObjectInterceptor" => "\Kinikit\Core\DependencyInjection\ObjectInterceptor"];

        // Check constructor
        $this->assertEquals(new MethodInspector($reflectionClass->getConstructor(), $annotations->getMethodAnnotations()["__construct"], $declaredNamespaceClasses), $classInspector->getConstructor());


        // Check public methods
        $publicMethods = $classInspector->getPublicMethods();
        $this->assertEquals(6, sizeof($publicMethods));
        $this->assertEquals(new MethodInspector($reflectionClass->getMethod("getId"), $annotations->getMethodAnnotations()["getId"], $declaredNamespaceClasses), $publicMethods["getId"]);
        $this->assertEquals(new MethodInspector($reflectionClass->getMethod("getName"), $annotations->getMethodAnnotations()["getName"], $declaredNamespaceClasses), $publicMethods["getName"]);
        $this->assertEquals(new MethodInspector($reflectionClass->getMethod("setName"), $annotations->getMethodAnnotations()["setName"], $declaredNamespaceClasses), $publicMethods["setName"]);
        $this->assertEquals(new MethodInspector($reflectionClass->getMethod("setDob"), $annotations->getMethodAnnotations()["setDob"], $declaredNamespaceClasses), $publicMethods["setDob"]);
        $this->assertEquals(new MethodInspector($reflectionClass->getMethod("clone"), $annotations->getMethodAnnotations()["clone"], $declaredNamespaceClasses), $publicMethods["clone"]);
        $this->assertEquals(new MethodInspector($reflectionClass->getMethod("isSpecial"), $annotations->getMethodAnnotations()["isSpecial"], $declaredNamespaceClasses), $publicMethods["isSpecial"]);


        $this->assertEquals(new MethodInspector($reflectionClass->getMethod("getId"), $annotations->getMethodAnnotations()["getId"], $declaredNamespaceClasses), $classInspector->getPublicMethod("getId"));
        $this->assertEquals(new MethodInspector($reflectionClass->getMethod("getName"), $annotations->getMethodAnnotations()["getName"], $declaredNamespaceClasses), $classInspector->getPublicMethod("getName"));
        $this->assertEquals(new MethodInspector($reflectionClass->getMethod("setName"), $annotations->getMethodAnnotations()["setName"], $declaredNamespaceClasses), $classInspector->getPublicMethod("setName"));
        $this->assertEquals(new MethodInspector($reflectionClass->getMethod("setDob"), $annotations->getMethodAnnotations()["setDob"], $declaredNamespaceClasses), $classInspector->getPublicMethod("setDob"));
        $this->assertEquals(new MethodInspector($reflectionClass->getMethod("clone"), $annotations->getMethodAnnotations()["clone"], $declaredNamespaceClasses), $classInspector->getPublicMethod("clone"));


    }


    public function testCanGetGetterAndSetterMembersForClass() {

        $classInspector = new ClassInspector(TestTypedPOPO::class);
        $getters = $classInspector->getGetters();
        $this->assertEquals(3, sizeof($getters));
        $this->assertEquals("getId", $getters["id"]->getMethodName());
        $this->assertEquals("getName", $getters["name"]->getMethodName());
        $this->assertEquals("isSpecial", $getters["special"]->getMethodName());

        $setters = $classInspector->getSetters();
        $this->assertEquals(2, sizeof($setters));
        $this->assertEquals("setName", $setters["name"]->getMethodName());
        $this->assertEquals("setDob", $setters["dob"]->getMethodName());


    }

}
