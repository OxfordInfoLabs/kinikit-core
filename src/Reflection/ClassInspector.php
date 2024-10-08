<?php

namespace Kinikit\Core\Reflection;

use Kinikit\Core\Annotation\Annotation;
use Kinikit\Core\Annotation\ClassAnnotationParser;
use Kinikit\Core\Annotation\ClassAnnotations;
use Kinikit\Core\DependencyInjection\Container;
use ReflectionClass;
use ReflectionException;

/**
 * Generic class inspector for inspecting class information using a mixture of reflection
 * and annotations.
 *
 * Class ClassInspector
 */
class ClassInspector {

    private ReflectionClass $reflectionClass;
    private ClassAnnotations $classAnnotations;
    private array $declaredNamespaces = [];
    private array $publicMethods = [];
    private array $setters = [];
    private array $getters = [];
    private array $properties;
    private array $methodInspectors = [];

    /**
     * Construct with a class name or filename.
     *
     * ClassInspector constructor.
     * @param string $class
     * @throws ReflectionException
     */
    public function __construct(string $class) {

        if (is_numeric(strpos($class, "."))) {
            $class = $this->getClassNameFromFile($class);
        }

        $class = str_replace("?", "", $class);

        $this->reflectionClass = new ReflectionClass($class);
        $this->classAnnotations = Container::instance()->get(ClassAnnotationParser::class)->parse($class);

    }


    /**
     * Get the class name for this class
     */
    public function getClassName(): string {
        return "\\" . ltrim($this->reflectionClass->getName(), "\\");
    }

    /**
     * Get the last portion of the full class name.
     * @return string
     */
    public function getShortClassName(): string {
        $exploded = explode("\\", $this->getClassName());
        return array_pop($exploded);
    }

    /**
     * Get the reflection class instance
     *
     * @return ReflectionClass
     */
    public function getReflectionClass(): ReflectionClass {
        return $this->reflectionClass;
    }


    /**
     * Return boolean as to whether or not this is an interface
     *
     * @return bool
     */
    public function isInterface(): bool {
        return $this->reflectionClass->isInterface();
    }


    /**
     * Return boolean as to whether or not this is an abstract class.
     *
     * @return bool
     */
    public function isAbstract(): bool {
        return $this->reflectionClass->isAbstract();
    }


    /**
     * Get the constructor as a method inspector object.
     *
     * @return Method|null
     */
    public function getConstructor(): ?Method {
        return $this->reflectionClass->getConstructor() ? $this->getMethodInspector("__construct") : null;
    }


    /**
     * Get our namespace
     *
     * @return string
     */
    public function getNamespace(): string {
        return $this->reflectionClass->getNamespaceName();
    }

    /**
     * Get the declared namespace classes for this class.
     */
    public function getDeclaredNamespaceClasses(): array {

        if (!$this->declaredNamespaces) {

            if (file_exists($this->reflectionClass->getFileName())) {

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

                $classInspectorProvider = Container::instance()->get(ClassInspectorProvider::class);

                // If a parent class, parse this now.
                if ($this->reflectionClass->getParentClass()) {
                    $parentClassInspector = $classInspectorProvider->getClassInspector($this->reflectionClass->getParentClass()->getName());
                    $this->declaredNamespaces = array_merge($this->declaredNamespaces, $parentClassInspector->getDeclaredNamespaceClasses() ?? []);
                }

                // If contains traits, parse these now.
                if ($this->reflectionClass->getTraits()) {
                    foreach ($this->reflectionClass->getTraits() as $trait) {
                        $traitInspector = $classInspectorProvider->getClassInspector($trait->getName());
                        $this->declaredNamespaces = array_merge($this->declaredNamespaces, $traitInspector->getDeclaredNamespaceClasses());
                    }
                }


            }
        }

        return $this->declaredNamespaces;

    }


    /**
     * Get the whole annotations object
     *
     * @return ClassAnnotations
     */
    public function getClassAnnotationsObject(): ClassAnnotations {
        return $this->classAnnotations;
    }


    /**
     * Get class annotations - indexed by annotation key and index of annotation.
     *
     * @return Annotation[][]
     */
    public function getClassAnnotations(): array {
        return $this->classAnnotations->getClassAnnotations();
    }


    /**
     * Get all public methods as method inspector objects indexed by method name
     *
     * @return Method[]
     */
    public function getPublicMethods(): array {
        if (!($this->publicMethods)) {
            foreach ($this->reflectionClass->getMethods() as $method) {

                if ($method->isDeprecated()) {
                    continue;
                }

                if ($method->isPublic() && !$method->isConstructor()) {
                    $methodName = $method->getName();
                    $methodInspector = $this->getMethodInspector($methodName);
                    $this->publicMethods[$methodName] = $methodInspector;

                    if (str_starts_with($methodName, "get") && count($methodInspector->getParameters()) === 0) {
                        $this->getters[lcfirst(substr($methodName, 3))] = $methodInspector;
                    } else if (str_starts_with($method->getName(), "is") && count($methodInspector->getParameters()) === 0) {
                        $this->getters[lcfirst(substr($methodName, 2))] = $methodInspector;
                    } else if (str_starts_with($method->getName(), "set")) {
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
    public function getPublicMethod(string $string): Method {
        return $this->getMethodInspector($string);
    }


    /**
     * Get all properties of this class
     *
     * @return Property[]
     */
    public function getProperties(): array {
        if (!isset($this->properties)) {

            $this->properties = [];

            foreach ($this->reflectionClass->getProperties() as $property) {
                $propertyName = $property->getName();
                $this->properties[$propertyName] = new Property($property, $this->classAnnotations->getFieldAnnotations()[$propertyName] ?? [], $this);
            }
        }

        return $this->properties;
    }


    /**
     * Get getters
     *
     * @return Method[]
     */
    public function getGetters(): array {
        $this->getPublicMethods();
        return $this->getters;
    }


    /**
     * Get setters
     *
     * @return Method[]
     */
    public function getSetters(): array {
        $this->getPublicMethods();
        return $this->setters;
    }


    /**
     * Return a boolean indicating whether an accessor exists for the specified property name.
     *
     * @param $propertyName
     */
    public function hasAccessor($propertyName): bool {

        // Check getters first
        $getters = $this->getGetters();
        if (isset($getters[$propertyName])) {
            return true;
        }

        // Fall back to properties
        $properties = $this->getProperties();
        return isset($properties[$propertyName]);
    }


    /**
     * Create an instance with constructor arguments as key value pairs
     *
     * @param mixed[string] $constructorArguments
     */
    public function createInstance(array $constructorArguments) {
        $processedArguments = $this->getConstructor() ? $this->getConstructor()->__processMethodArguments($constructorArguments, true) : [];

        return $this->reflectionClass->newInstanceArgs($processedArguments);
    }


    /**
     * Set property data for all properties or a single property if supplied.  The order of setting is via a
     * public setter method first and then via a property.  If publicOnly is true, only public properties
     * will be accessed in the absence of a setter otherwise properties will be coerced to accessible.
     *
     * @param object $object
     * @param array $data
     * @param string|null $propertyName
     * @param bool $publicOnly
     */
    public function setPropertyData(object $object, mixed $data, string $propertyName = null, bool $publicOnly = true): void {

        // If property name, doing one at a time.
        if ($propertyName) {

            $setters = $this->getSetters();
            if (isset($setters[$propertyName])) {

                $parameters = $setters[$propertyName]->getParameters();
                $params = [$parameters[0]?->getName() => $data];

                $setters[$propertyName]->call($object, $params);
            } else {
                $properties = $this->getProperties();
                if (isset($properties[$propertyName])) {
                    if (!$publicOnly || $properties[$propertyName]->getVisibility() === Property::VISIBILITY_PUBLIC) {
                        $properties[$propertyName]->set($object, $data);
                    }
//                } else {
//                    print("No property $propertyName exists on class ". $this->getClassName() . "\n");
////                    throw new WrongParametersException("No property $propertyName exists on class ". $this->getClassName());
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
     * @param string|null $propertyName
     * @param bool $publicOnly
     *
     * @return mixed
     */
    public function getPropertyData(object $object, ?string $propertyName = null, bool $publicOnly = true) {
        if ($propertyName) {
            $getters = $this->getGetters();
            if (isset($getters[$propertyName])) {
                return $getters[$propertyName]->call($object, []);
            }

            $properties = $this->getProperties();
            if (isset($properties[$propertyName])) {
                if (!$publicOnly || $properties[$propertyName]->getVisibility() === Property::VISIBILITY_PUBLIC) {
                    return $properties[$propertyName]->get($object);
                }
            }

        } else {
            $returnData = [];
            $keysSeen = [];
            foreach ($this->getGetters() as $key => $getter) {
                $returnData[$key] = $getter->call($object, []);
                $keysSeen[] = $key;
            }
            foreach ($this->getProperties() as $key => $property) {
                if ((!in_array($key, $keysSeen, true) && !$publicOnly) || $property->getVisibility() === Property::VISIBILITY_PUBLIC) {
                    $returnData[$key] = $this->getPropertyData($object, $key, $publicOnly);
                }
            }
            return $returnData;

        }
    }


    /**
     * Check whether this class has a class attribute
     *
     * @param $attributeClass
     * @return boolean
     */
    public function hasClassAttribute($attributeClass): bool {
        return (bool)count($this->reflectionClass->getAttributes($attributeClass));
    }

    /**
     * Return boolean indicator as to whether or not the class contains a given trait.
     *
     * @param string $traitName
     * @return bool
     */
    public function usesTrait(string $traitName): bool {
        return in_array($traitName, $this->reflectionClass->getTraitNames());
    }

    /**
     * Get a method inspector - cached as accessed for better performance.
     * @returns Method
     * @throws ReflectionException
     */
    private function getMethodInspector($methodName): Method {
        if (!isset($this->methodInspectors[$methodName])) {
            $reflectionMethod = $methodName === "__construct" ? $this->reflectionClass->getConstructor() : $this->reflectionClass->getMethod($methodName);

            $this->methodInspectors[$methodName] = new Method($reflectionMethod, $this->classAnnotations->getMethodAnnotations()[$methodName] ?? [], $this);
        }

        return $this->methodInspectors[$methodName];
    }


    // Get a class name from a file
    private function getClassNameFromFile($filename): string {

        $file = file_get_contents($filename);

        // Get the class
        preg_match("/class (.*?)\W/", $file, $matches);
        $className = trim($matches[1] ?? "");

        // Get the namespace
        preg_match("/namespace (.*?);/", $file, $matches);
        $namespace = trim($matches[1] ?? "");


        return $namespace . "\\" . $className;
    }


}
