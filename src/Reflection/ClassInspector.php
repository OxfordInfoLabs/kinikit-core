<?php

namespace Kinikit\Core\Reflection;

use Kinikit\Core\Annotation\Annotation;
use Kinikit\Core\Annotation\ClassAnnotationParser;
use Kinikit\Core\DependencyInjection\Container;

/**
 * Generic class inspector for inspecting class information using a mixture of reflection
 * and annotations.
 *
 * Class ClassInspector
 */
class ClassInspector {

    private $reflectionClass;
    private $classAnnotations;
    private $declaredNamespaces;

    private $publicMethods;
    private $setters = array();
    private $getters = array();
    private $properties;
    private $methodInspectors = array();

    /**
     * Construct with either a class name or object
     *
     * ClassInspector constructor.
     * @param mixed $class
     */
    public function __construct($class) {

        $this->reflectionClass = new \ReflectionClass($class);
        $this->classAnnotations = Container::instance()->get(ClassAnnotationParser::class)->parse($class);

    }


    /**
     * Get the class name for this class
     */
    public function getClassName() {
        return "\\" . ltrim($this->reflectionClass->getName(), "\\");
    }

    /**
     * Get the last portion of the full class name.
     */
    public function getShortClassName() {
        $exploded = explode("\\", $this->getClassName());
        return array_pop($exploded);
    }

    /**
     * Get the reflection class instance
     *
     * @return \ReflectionClass
     */
    public function getReflectionClass() {
        return $this->reflectionClass;
    }


    /**
     * Return boolean as to whether or not this is an interface
     *
     * @return bool
     */
    public function isInterface() {
        return $this->reflectionClass->isInterface();
    }


    /**
     * Get the constructor as a method inspector object.
     *
     * @return Method
     */
    public function getConstructor() {
        return $this->reflectionClass->getConstructor() ? $this->getMethodInspector("__construct") : null;
    }


    /**
     * Get our namespace
     *
     * @return string
     */
    public function getNamespace() {
        return $this->reflectionClass->getNamespaceName();
    }

    /**
     * Get the declared namespace classes for this class.
     */
    public function getDeclaredNamespaceClasses() {

        if (!$this->declaredNamespaces) {

            $source = file_get_contents($this->reflectionClass->getFileName());
            $fragment = explode("class ", $source);
            preg_match_all("/use (.*?);/", $fragment[0], $matches);
            $this->declaredNamespaces = [];
            if (isset($matches[1])) {
                foreach ($matches[1] as $namespacedClass) {
                    $explodedClass = explode("\\", $namespacedClass);
                    $className = array_pop($explodedClass);
                    $this->declaredNamespaces[$className] = "\\" . $namespacedClass;
                }
            }
        }

        return $this->declaredNamespaces;

    }


    /**
     * Get the whole annotations object
     *
     * @return \Kinikit\Core\Annotation\ClassAnnotations
     */
    public function getClassAnnotationsObject() {
        return $this->classAnnotations;
    }


    /**
     * Get class annotations - indexed by annotation key and index of annotation.
     *
     * @return Annotation[][]
     */
    public function getClassAnnotations() {
        return $this->classAnnotations->getClassAnnotations();
    }


    /**
     * Get all public methods as method inspector objects indexed by method name
     *
     * @return Method[]
     */
    public function getPublicMethods() {
        if (!isset($this->publicMethods)) {
            $this->publicMethods = array();
            foreach ($this->reflectionClass->getMethods() as $method) {
                if ($method->isPublic() && !$method->isConstructor()) {
                    $methodName = $method->getName();
                    $methodInspector = $this->getMethodInspector($methodName);
                    $this->publicMethods[$methodName] = $methodInspector;

                    if (substr($methodName, 0, 3) == "get" && sizeof($methodInspector->getParameters()) == 0) {
                        $this->getters[lcfirst(substr($methodName, 3))] = $methodInspector;
                    } else if (substr($method->getName(), 0, 2) == "is" && sizeof($methodInspector->getParameters()) == 0) {
                        $this->getters[lcfirst(substr($methodName, 2))] = $methodInspector;
                    } else if (substr($method->getName(), 0, 3) == "set") {
                        $this->setters[lcfirst(substr($methodName, 3))] = $methodInspector;
                    }

                }
            }
        }

        return $this->publicMethods;
    }


    /**
     * Get the public method
     *
     * @param string $string
     * @return Method
     */
    public function getPublicMethod(string $string) {
        return $this->getMethodInspector($string);
    }


    /**
     * Get all properties of this class
     *
     * @return Property[]
     */
    public function getProperties() {
        if (!isset($this->properties)) {

            $this->properties = [];

            foreach ($this->reflectionClass->getProperties() as $property) {
                $this->properties[$property->getName()] = new Property($property, $this->classAnnotations->getFieldAnnotations()[$property->getName()], $this);
            }
        }

        return $this->properties;
    }


    /**
     * Get getters
     *
     * @return Method[string]
     */
    public function getGetters() {
        $this->getPublicMethods();
        return $this->getters;
    }


    /**
     * Get setters
     *
     * @return Method[string]
     */
    public function getSetters() {
        $this->getPublicMethods();
        return $this->setters;
    }


    /**
     * Create an instance with constructor arguments as key value pairs
     *
     * @param mixed[string] $constructorArguments
     */
    public function createInstance($constructorArguments) {
        $processedArguments = $this->getConstructor() ? $this->getConstructor()->__processMethodArguments($constructorArguments) : [];

        return $this->reflectionClass->newInstanceArgs($processedArguments);
    }


    /**
     * Set property data for all properties or a single property if supplied.  The order of setting is via a
     * public setter method first and then via a property.  If publicOnly is true, only public properties
     * will be accessed in the absence of a setter otherwise properties will be coerced to accessible.
     *
     * @param object $object
     * @param array $data
     * @param string $propertyName
     * @param bool $publicOnly
     */
    public function setPropertyData($object, $data, $propertyName = null, $publicOnly = true) {

        // If property name, doing one at a time.
        if ($propertyName) {

            $setters = $this->getSetters();
            if (isset($setters[$propertyName])) {

                $parameters = $setters[$propertyName]->getParameters();
                if (sizeof($parameters) > 0) {
                    $params = [$parameters[0]->getName() => $data];
                }

                $setters[$propertyName]->call($object, $params);
            } else {
                $properties = $this->getProperties();
                if (isset($properties[$propertyName])) {
                    if (!$publicOnly || $properties[$propertyName]->getVisibility() == Property::VISIBILITY_PUBLIC)
                        $properties[$propertyName]->set($object, $data);
                }
            }

        } else {
            foreach ($data as $key => $datum) {
                $this->setPropertyData($object, $datum, $key, $publicOnly);
            }
        }

    }


    /**
     * Get property data for all properties or a single property if supplied.  The order of getting is via a public getter method first
     * and then via a property.  If publicOnly is true, only public properties will be accessed in the absence of a
     * getter, otherwise properties will be coerced to accessible.
     *
     * @param object $object
     * @param string $propertyName
     * @param bool $publicOnly
     *
     * @return mixed
     */
    public function getPropertyData($object, $propertyName = null, $publicOnly = true) {
        if ($propertyName) {
            $getters = $this->getGetters();
            if (isset($getters[$propertyName])) {
                return $getters[$propertyName]->call($object, []);
            }

            $properties = $this->getProperties();
            if (isset($properties[$propertyName])) {
                if (!$publicOnly || $properties[$propertyName]->getVisibility() == Property::VISIBILITY_PUBLIC)
                    return $properties[$propertyName]->get($object);
            }
        } else {
            $returnData = [];
            $keysSeen = [];
            foreach ($this->getGetters() as $key => $getter) {
                $returnData[$key] = $getter->call($object, []);
                $keysSeen[] = $key;
            }
            foreach ($this->getProperties() as $key => $property) {
                if (!in_array($key, $keysSeen) && !$publicOnly || $property->getVisibility() == Property::VISIBILITY_PUBLIC)
                    $returnData[$key] = $this->getPropertyData($object, $key, $publicOnly);
            }
            return $returnData;

        }
    }


    // Get a method inspector - cached as accessed for better performance.
    private function getMethodInspector($methodName) {
        if (!isset($this->methodInspectors[$methodName])) {
            $reflectionMethod = $methodName == "__construct" ? $this->reflectionClass->getConstructor() : $this->reflectionClass->getMethod($methodName);
            $this->methodInspectors[$methodName] = new Method($reflectionMethod, $this->classAnnotations->getMethodAnnotations()[$methodName], $this);
        }

        return $this->methodInspectors[$methodName];
    }


}
