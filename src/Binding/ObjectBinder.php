<?php


namespace Kinikit\Core\Binding;

use Kinikit\Core\Exception\ObjectBindingException;
use Kinikit\Core\Util\Primitive;
use Kinikit\Core\Reflection\ClassInspectorProvider;
use Kinikit\Core\Reflection\Parameter;

/**
 * Binder object used for converting an object (or an array of objects) recursively to associative array data
 * and vice versa.  Similar to GSON for Java
 *
 * Class ObjectBinder
 * @package Kinikit\Core\Object
 */
class ObjectBinder {

    /**
     * @var ClassInspectorProvider
     */
    private $classInspectorProvider;


    /**
     * ObjectBinder constructor.
     * @param ClassInspectorProvider $classInspectorProvider
     */
    public function __construct($classInspectorProvider) {
        $this->classInspectorProvider = $classInspectorProvider;
    }


    /**
     * Bind from an array of data to a specified class or array type.
     *
     * @param array $data
     * @return mixed
     */
    public function bindFromArray($data, $targetClass) {

        $result = null;


        $arrayTrimmed = preg_replace("/\[[a-z]*\]$/", "", $targetClass);

        // If an array of objects, process these next.
        if ($arrayTrimmed != $targetClass) {

            $result = [];

            foreach ($data as $key => $dataItem) {
                $result[$key] = $this->bindFromArray($dataItem, $arrayTrimmed);
            }

        } else {

            // if a primitive, shortcut and return the value intact.
            if (in_array($targetClass, Primitive::TYPES)) {
                return $data;
            }


            $classInspector = $this->classInspectorProvider->getClassInspector($targetClass);

            // If we have a constructor, form arguments
            $constructorParams = array();
            $constructor = $classInspector->getConstructor();
            if ($constructor) {
                foreach ($constructor->getParameters() as $parameter) {
                    if (isset($data[$parameter->getName()])) {
                        $value = $data[$parameter->getName()];


                        $value = $this->bindFromArray($value, $parameter->getType());

                        // Check for bad typed arguments if explicit typing.
                        if ($parameter->isExplicitlyTyped()) {
                            if ($parameter->isPrimitive() && !Primitive::isOfPrimitiveType($parameter->getType(), $value)) {
                                throw new ObjectBindingException("Explicitly typed required constructor parameter {$parameter->getName()} was supplied with the wrong type");
                            }

                        }

                        $constructorParams[] = $value;
                    } else {

                        if ($parameter->isExplicitlyTyped()) {

                            if ($parameter->getRequired())
                                throw new ObjectBindingException("Explicitly typed required constructor parameter {$parameter->getName()} was not supplied when binding object of type $targetClass");

                        }

                        $constructorParams[] = $parameter->getDefaultValue() == Parameter::NO_DEFAULT_VALUE ? null : $parameter->getDefaultValue();
                    }
                }
            }

            // Create the class
            $reflectionClass = $classInspector->getReflectionClass();
            $instance = $reflectionClass->newInstanceArgs($constructorParams);

            // Attempt to call each setter method if we have data.
            $setters = $classInspector->getSetters();
            foreach ($setters as $member => $method) {
                if (isset($data[$member])) {

                    $value = $data[$member];

                    $parameter = $method->getParameters()[0];

                    // Handle nested objects
                    $value = $this->bindFromArray($value, $parameter->getType());

                    // Check for bad typed arguments if explicit typing.
                    if ($parameter->isExplicitlyTyped()) {
                        if ($parameter->isPrimitive() && !Primitive::isOfPrimitiveType($parameter->getType(), $value)) {
                            throw new ObjectBindingException("Explicitly typed setter parameter {$parameter->getName()} was supplied with the wrong type");
                        }

                    }

                    $setters[$member]->getReflectionMethod()->invokeArgs($instance, [$value]);

                }

            }


            $result = $instance;

        }


        return $result;


    }


    /**
     * Bind an object / array of objects to an array
     *
     * @param $object
     * @return array
     */
    public function bindToArray($object) {

    }


}
