<?php


namespace Kinikit\Core\Reflection;


use Kinikit\Core\Annotation\Annotation;
use Kinikit\Core\Annotation\ClassAnnotationParser;

class MethodInspectorTest extends \PHPUnit\Framework\TestCase {


    public function testCanGetMethodLevelAttributes() {


        $annotations = ClassAnnotationParser::instance()->parse(TestTypedPOPO::class)->getMethodAnnotations()["__construct"];
        $reflectionMethod = (new \ReflectionClass(TestTypedPOPO::class))->getConstructor();

        $methodInspector = new MethodInspector($reflectionMethod, $annotations, []);

        $this->assertEquals("Kinikit\Core\Reflection\TestTypedPOPO", $methodInspector->getDeclaringClassName());
        $this->assertEquals("__construct", $methodInspector->getMethodName());
        $this->assertEquals($annotations, $methodInspector->getMethodAnnotations());


    }


    public function testCanGetParametersForTypedMethodArguments() {

        // Check constructor
        $reflectionMethod = (new \ReflectionClass(TestTypedPOPO::class))->getConstructor();
        $methodInspector = new MethodInspector($reflectionMethod, null, []);

        $params = $methodInspector->getParameters();
        $this->assertEquals(3, sizeof($params));
        $this->assertEquals(new Parameter("id", "int", true, Parameter::NO_DEFAULT_VALUE, true), $params[0], true);
        $this->assertEquals(new Parameter("name", "string", true, Parameter::NO_DEFAULT_VALUE, true), $params[1], true);
        $this->assertEquals(new Parameter("dob", "string", false, "01/01/2016", true), $params[2], true);


        // Check one without params
        $reflectionMethod = (new \ReflectionClass(TestTypedPOPO::class))->getMethod("getId");
        $methodInspector = new MethodInspector($reflectionMethod, null, []);

        $params = $methodInspector->getParameters();
        $this->assertEquals(0, sizeof($params));


        // Check one with param
        $reflectionMethod = (new \ReflectionClass(TestTypedPOPO::class))->getMethod("setDob");
        $methodInspector = new MethodInspector($reflectionMethod, null, []);

        $params = $methodInspector->getParameters();
        $this->assertEquals(1, sizeof($params));
        $this->assertEquals(new Parameter("dob", "string", true, Parameter::NO_DEFAULT_VALUE, true), $params[0]);

        // Check one with class param
        $reflectionMethod = (new \ReflectionClass(TestTypedPOPO::class))->getMethod("clone");
        $methodInspector = new MethodInspector($reflectionMethod, null, []);

        $params = $methodInspector->getParameters();
        $this->assertEquals(1, sizeof($params));
        $this->assertEquals(new Parameter("otherPOPO", "\Kinikit\Core\Reflection\TestTypedPOPO", true, Parameter::NO_DEFAULT_VALUE, true), $params[0], true);

    }


    public function testCanGetParametersForAnnotatedMethodArguments() {

        $declaredNamespaceClasses = ["Annotation" => "\Kinikit\Core\Annotation\Annotation"];

        // Check constructor
        $reflectionMethod = (new \ReflectionClass(TestAnnotatedPOPO::class))->getConstructor();
        $annotations = ClassAnnotationParser::instance()->parse(TestAnnotatedPOPO::class)->getMethodAnnotations()["__construct"];

        $methodInspector = new MethodInspector($reflectionMethod, $annotations, $declaredNamespaceClasses);

        $params = $methodInspector->getParameters();
        $this->assertEquals(3, sizeof($params));
        $this->assertEquals(new Parameter("id", "int", true), $params[0]);
        $this->assertEquals(new Parameter("name", "string", true), $params[1]);
        $this->assertEquals(new Parameter("dob", "string", false, "01/01/2016"), $params[2]);


        // Check one without params
        $reflectionMethod = (new \ReflectionClass(TestAnnotatedPOPO::class))->getMethod("getId");
        $annotations = ClassAnnotationParser::instance()->parse(TestAnnotatedPOPO::class)->getMethodAnnotations()["getId"];

        $methodInspector = new MethodInspector($reflectionMethod, $annotations, $declaredNamespaceClasses);

        $params = $methodInspector->getParameters();
        $this->assertEquals(0, sizeof($params));


        // Check one with param
        $reflectionMethod = (new \ReflectionClass(TestAnnotatedPOPO::class))->getMethod("setDob");
        $annotations = ClassAnnotationParser::instance()->parse(TestAnnotatedPOPO::class)->getMethodAnnotations()["setDob"];

        $methodInspector = new MethodInspector($reflectionMethod, $annotations, $declaredNamespaceClasses);

        $params = $methodInspector->getParameters();
        $this->assertEquals(1, sizeof($params));
        $this->assertEquals(new Parameter("dob", "string", true), $params[0]);

        // Check one with class param
        $reflectionMethod = (new \ReflectionClass(TestAnnotatedPOPO::class))->getMethod("clone");
        $annotations = ClassAnnotationParser::instance()->parse(TestAnnotatedPOPO::class)->getMethodAnnotations()["clone"];

        $methodInspector = new MethodInspector($reflectionMethod, $annotations, $declaredNamespaceClasses);

        $params = $methodInspector->getParameters();
        $this->assertEquals(1, sizeof($params));
        $this->assertEquals(new Parameter("otherPOPO", "\Kinikit\Core\Reflection\TestAnnotatedPOPO", true), $params[0]);


        // Check one with class param
        $reflectionMethod = (new \ReflectionClass(TestAnnotatedPOPO::class))->getMethod("evaluateAnnotation");
        $annotations = ClassAnnotationParser::instance()->parse(TestAnnotatedPOPO::class)->getMethodAnnotations()["evaluateAnnotation"];

        $methodInspector = new MethodInspector($reflectionMethod, $annotations, $declaredNamespaceClasses);

        $params = $methodInspector->getParameters();
        $this->assertEquals(1, sizeof($params));
        $this->assertEquals(new Parameter("annotation", "\Kinikit\Core\Annotation\Annotation", true), $params[0]);

    }


    public function testCanGetReturnValueForTypedMethods() {


        // Check constructor
        $reflectionMethod = (new \ReflectionClass(TestTypedPOPO::class))->getConstructor();
        $methodInspector = new MethodInspector($reflectionMethod, null, []);
        $this->assertNull($methodInspector->getReturnType());


        // Check one without params
        $reflectionMethod = (new \ReflectionClass(TestTypedPOPO::class))->getMethod("getId");
        $methodInspector = new MethodInspector($reflectionMethod, null, []);
        $this->assertEquals(new ReturnType("int", true), $methodInspector->getReturnType());


        // Check one with param
        $reflectionMethod = (new \ReflectionClass(TestTypedPOPO::class))->getMethod("setDob");
        $methodInspector = new MethodInspector($reflectionMethod, null, []);
        $this->assertNull($methodInspector->getReturnType());


        // Check one with class param
        $reflectionMethod = (new \ReflectionClass(TestTypedPOPO::class))->getMethod("clone");
        $methodInspector = new MethodInspector($reflectionMethod, null, []);
        $this->assertEquals(new ReturnType("\\" . TestTypedPOPO::class, true), $methodInspector->getReturnType());


    }


    public function testCanGetReturnValueForAnnotatedMethods() {

        $declaredNamespaceClasses = ["Annotation" => "\Kinikit\Core\Annotation\Annotation"];

        // Check constructor
        $reflectionMethod = (new \ReflectionClass(TestAnnotatedPOPO::class))->getConstructor();
        $annotations = ClassAnnotationParser::instance()->parse(TestAnnotatedPOPO::class)->getMethodAnnotations()["__construct"];

        $methodInspector = new MethodInspector($reflectionMethod, $annotations, $declaredNamespaceClasses);
        $this->assertNull($methodInspector->getReturnType());


        // Check one without params
        $reflectionMethod = (new \ReflectionClass(TestAnnotatedPOPO::class))->getMethod("getId");
        $annotations = ClassAnnotationParser::instance()->parse(TestAnnotatedPOPO::class)->getMethodAnnotations()["getId"];
        $methodInspector = new MethodInspector($reflectionMethod, $annotations, $declaredNamespaceClasses);
        $this->assertEquals(new ReturnType("int"), $methodInspector->getReturnType());


        // Check one with param
        $reflectionMethod = (new \ReflectionClass(TestAnnotatedPOPO::class))->getMethod("setDob");
        $annotations = ClassAnnotationParser::instance()->parse(TestAnnotatedPOPO::class)->getMethodAnnotations()["setDob"];

        $methodInspector = new MethodInspector($reflectionMethod, $annotations, $declaredNamespaceClasses);
        $this->assertNull($methodInspector->getReturnType());

        // Check one with class param
        $reflectionMethod = (new \ReflectionClass(TestAnnotatedPOPO::class))->getMethod("clone");
        $annotations = ClassAnnotationParser::instance()->parse(TestAnnotatedPOPO::class)->getMethodAnnotations()["clone"];

        $methodInspector = new MethodInspector($reflectionMethod, $annotations, $declaredNamespaceClasses);
        $this->assertEquals(new ReturnType("\\" . TestAnnotatedPOPO::class), $methodInspector->getReturnType());


        // Check one with class param
        $reflectionMethod = (new \ReflectionClass(TestAnnotatedPOPO::class))->getMethod("evaluateAnnotation");
        $annotations = ClassAnnotationParser::instance()->parse(TestAnnotatedPOPO::class)->getMethodAnnotations()["evaluateAnnotation"];

        $methodInspector = new MethodInspector($reflectionMethod, $annotations, $declaredNamespaceClasses);
        $this->assertEquals(new ReturnType("\\" . Annotation::class), $methodInspector->getReturnType());


    }


}
