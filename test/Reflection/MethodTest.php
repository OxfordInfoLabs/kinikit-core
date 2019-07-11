<?php


namespace Kinikit\Core\Reflection;


use Kinikit\Core\Annotation\Annotation;
use Kinikit\Core\Annotation\ClassAnnotationParser;
use Kinikit\Core\Annotation\TestAnnotatedClass;
use Kinikit\Core\Exception\InsufficientParametersException;
use Kinikit\Core\Exception\WrongParametersException;

include_once 'autoloader.php';

class MethodTest extends \PHPUnit\Framework\TestCase {


    public function testCanGetMethodLevelAttributes() {


        $classInspector = new ClassInspector(TestTypedPOPO::class);
        $annotations = (new ClassAnnotationParser())->parse(TestTypedPOPO::class)->getMethodAnnotations()["__construct"];
        $reflectionMethod = (new \ReflectionClass(TestTypedPOPO::class))->getConstructor();

        $methodInspector = new Method($reflectionMethod, $annotations, $classInspector);

        $this->assertEquals("\Kinikit\Core\Reflection\TestTypedPOPO", $methodInspector->getDeclaringClassInspector()->getClassName());
        $this->assertEquals("__construct", $methodInspector->getMethodName());
        $this->assertEquals($annotations, $methodInspector->getMethodAnnotations());


    }


    public function testCanGetParametersForTypedMethodArguments() {

        // Check constructor
        $classInspector = new ClassInspector(TestTypedPOPO::class);
        $reflectionMethod = (new \ReflectionClass(TestTypedPOPO::class))->getConstructor();
        $reflectionParams = $reflectionMethod->getParameters();
        $methodInspector = new Method($reflectionMethod, null, $classInspector);


        $params = $methodInspector->getParameters();
        $this->assertEquals(3, sizeof($params));
        $this->assertEquals(new Parameter($reflectionParams[0], $methodInspector), $params[0]);
        $this->assertEquals(new Parameter($reflectionParams[1], $methodInspector), $params[1]);
        $this->assertEquals(new Parameter($reflectionParams[2], $methodInspector), $params[2]);


        // Check one without params
        $reflectionMethod = (new \ReflectionClass(TestTypedPOPO::class))->getMethod("getId");
        $methodInspector = new Method($reflectionMethod, null, $classInspector);

        $params = $methodInspector->getParameters();
        $this->assertEquals(0, sizeof($params));


        // Check one with param
        $reflectionMethod = (new \ReflectionClass(TestTypedPOPO::class))->getMethod("setDob");
        $reflectionParams = $reflectionMethod->getParameters();
        $methodInspector = new Method($reflectionMethod, null, $classInspector);

        $params = $methodInspector->getParameters();
        $this->assertEquals(1, sizeof($params));
        $this->assertEquals(new Parameter($reflectionParams[0], $methodInspector), $params[0]);

        // Check one with class param
        $reflectionMethod = (new \ReflectionClass(TestTypedPOPO::class))->getMethod("clone");
        $reflectionParams = $reflectionMethod->getParameters();
        $methodInspector = new Method($reflectionMethod, null, $classInspector);

        $params = $methodInspector->getParameters();
        $this->assertEquals(1, sizeof($params));
        $this->assertEquals(new Parameter($reflectionParams[0], $methodInspector), $params[0]);

    }


    public function testCanGetParametersForAnnotatedMethodArguments() {

        $classInspector = new ClassInspector(TestAnnotatedPOPO::class);

        // Check constructor
        $reflectionMethod = (new \ReflectionClass(TestAnnotatedPOPO::class))->getConstructor();
        $reflectionParams = $reflectionMethod->getParameters();
        $annotations = (new ClassAnnotationParser())->parse(TestAnnotatedPOPO::class)->getMethodAnnotations()["__construct"];


        $methodInspector = new Method($reflectionMethod, $annotations, $classInspector);

        $params = $methodInspector->getParameters();
        $this->assertEquals(3, sizeof($params));
        $this->assertEquals(new Parameter($reflectionParams[0], $methodInspector), $params[0]);
        $this->assertEquals(new Parameter($reflectionParams[1], $methodInspector), $params[1]);
        $this->assertEquals(new Parameter($reflectionParams[2], $methodInspector), $params[2]);


        // Check one without params
        $reflectionMethod = (new \ReflectionClass(TestAnnotatedPOPO::class))->getMethod("getId");

        $annotations = (new ClassAnnotationParser())->parse(TestAnnotatedPOPO::class)->getMethodAnnotations()["getId"];

        $methodInspector = new Method($reflectionMethod, $annotations, $classInspector);

        $params = $methodInspector->getParameters();
        $this->assertEquals(0, sizeof($params));


        // Check one with param
        $reflectionMethod = (new \ReflectionClass(TestAnnotatedPOPO::class))->getMethod("setDob");
        $reflectionParams = $reflectionMethod->getParameters();
        $annotations = (new ClassAnnotationParser())->parse(TestAnnotatedPOPO::class)->getMethodAnnotations()["setDob"];

        $methodInspector = new Method($reflectionMethod, $annotations, $classInspector);

        $params = $methodInspector->getParameters();
        $this->assertEquals(1, sizeof($params));
        $this->assertEquals(new Parameter($reflectionParams[0], $methodInspector), $params[0]);

        // Check one with class param
        $reflectionMethod = (new \ReflectionClass(TestAnnotatedPOPO::class))->getMethod("clone");
        $reflectionParams = $reflectionMethod->getParameters();

        $annotations = (new ClassAnnotationParser())->parse(TestAnnotatedPOPO::class)->getMethodAnnotations()["clone"];

        $methodInspector = new Method($reflectionMethod, $annotations, $classInspector);

        $params = $methodInspector->getParameters();
        $this->assertEquals(1, sizeof($params));
        $this->assertEquals(new Parameter($reflectionParams[0], $methodInspector), $params[0]);


        // Check one with class param
        $reflectionMethod = (new \ReflectionClass(TestAnnotatedPOPO::class))->getMethod("evaluateAnnotation");
        $reflectionParams = $reflectionMethod->getParameters();

        $annotations = (new ClassAnnotationParser())->parse(TestAnnotatedPOPO::class)->getMethodAnnotations()["evaluateAnnotation"];

        $methodInspector = new Method($reflectionMethod, $annotations, $classInspector);

        $params = $methodInspector->getParameters();
        $this->assertEquals(1, sizeof($params));
        $this->assertEquals(new Parameter($reflectionParams[0], $methodInspector), $params[0]);

    }


    public function testCanGetReturnValueForTypedMethods() {

        $classInspector = new ClassInspector(TestTypedPOPO::class);

        // Check constructor
        $reflectionMethod = (new \ReflectionClass(TestTypedPOPO::class))->getConstructor();
        $methodInspector = new Method($reflectionMethod, null, $classInspector);
        $this->assertEquals(new ReturnType($methodInspector), $methodInspector->getReturnType());


        // Check one without params
        $reflectionMethod = (new \ReflectionClass(TestTypedPOPO::class))->getMethod("getId");
        $methodInspector = new Method($reflectionMethod, null, $classInspector);
        $this->assertEquals(new ReturnType($methodInspector), $methodInspector->getReturnType());


        // Check one with param
        $reflectionMethod = (new \ReflectionClass(TestTypedPOPO::class))->getMethod("setDob");
        $methodInspector = new Method($reflectionMethod, null, $classInspector);
        $this->assertEquals(new ReturnType($methodInspector), $methodInspector->getReturnType());


        // Check one with class param
        $reflectionMethod = (new \ReflectionClass(TestTypedPOPO::class))->getMethod("clone");
        $methodInspector = new Method($reflectionMethod, null, $classInspector);
        $this->assertEquals(new ReturnType($methodInspector), $methodInspector->getReturnType());


    }


    public function testCanGetReturnValueForAnnotatedMethods() {

        $classInspector = new ClassInspector(TestAnnotatedPOPO::class);

        // Check constructor
        $reflectionMethod = (new \ReflectionClass(TestAnnotatedPOPO::class))->getConstructor();
        $annotations = (new ClassAnnotationParser())->parse(TestAnnotatedPOPO::class)->getMethodAnnotations()["__construct"];

        $methodInspector = new Method($reflectionMethod, $annotations, $classInspector);
        $this->assertEquals(new ReturnType($methodInspector), $methodInspector->getReturnType());


        // Check one without params
        $reflectionMethod = (new \ReflectionClass(TestAnnotatedPOPO::class))->getMethod("getId");
        $annotations = (new ClassAnnotationParser())->parse(TestAnnotatedPOPO::class)->getMethodAnnotations()["getId"];
        $methodInspector = new Method($reflectionMethod, $annotations, $classInspector);
        $this->assertEquals(new ReturnType($methodInspector), $methodInspector->getReturnType());


        // Check one with param
        $reflectionMethod = (new \ReflectionClass(TestAnnotatedPOPO::class))->getMethod("setDob");
        $annotations = (new ClassAnnotationParser())->parse(TestAnnotatedPOPO::class)->getMethodAnnotations()["setDob"];

        $methodInspector = new Method($reflectionMethod, $annotations, $classInspector);
        $this->assertEquals(new ReturnType($methodInspector), $methodInspector->getReturnType());

        // Check one with class param
        $reflectionMethod = (new \ReflectionClass(TestAnnotatedPOPO::class))->getMethod("clone");
        $annotations = (new ClassAnnotationParser())->parse(TestAnnotatedPOPO::class)->getMethodAnnotations()["clone"];

        $methodInspector = new Method($reflectionMethod, $annotations, $classInspector);
        $this->assertEquals(new ReturnType($methodInspector), $methodInspector->getReturnType());


        // Check one with class param
        $reflectionMethod = (new \ReflectionClass(TestAnnotatedPOPO::class))->getMethod("evaluateAnnotation");
        $annotations = (new ClassAnnotationParser())->parse(TestAnnotatedPOPO::class)->getMethodAnnotations()["evaluateAnnotation"];

        $methodInspector = new Method($reflectionMethod, $annotations, $classInspector);
        $this->assertEquals(new ReturnType($methodInspector), $methodInspector->getReturnType());


    }


    public function testCanCallMethodProvidedAllRequiredArgumentsAreSupplied() {

        $classInspector = new ClassInspector(TestTypedPOPO::class);
        $methodInspector = $classInspector->getPublicMethod("setName");

        $testTypedPOPO = new TestTypedPOPO(12, "Mark");

        // Try missing params first
        try {
            $methodInspector->call($testTypedPOPO, []);
            $this->fail("Should have thrown here");
        } catch (InsufficientParametersException $e) {
            // Success
        }

        // Try wrong types now
        try {
            $methodInspector->call($testTypedPOPO, ["name" => true]);
            $this->fail("Should have thrown here");
        } catch (WrongParametersException $e) {
            // Success
        }


        $methodInspector = $classInspector->getPublicMethod("clone");


        // Try wrong object type now
        try {
            $methodInspector->call($testTypedPOPO, ["otherPOPO" => new TestAnnotatedPOPO(11, "hello")]);
            $this->fail("Should have thrown here");
        } catch (WrongParametersException $e) {
            // Success
        }


        // Try successful ones
        $methodInspector = $classInspector->getPublicMethod("setName");
        $this->assertEquals(null, $methodInspector->call($testTypedPOPO, ["name" => "Mark"]));

        $methodInspector = $classInspector->getPublicMethod("getName");
        $this->assertEquals("Mark", $methodInspector->call($testTypedPOPO, []));

        $methodInspector = $classInspector->getPublicMethod("clone");
        $methodInspector->call($testTypedPOPO, ["otherPOPO" => new TestTypedPOPO(11, "hello")]);


    }

}
