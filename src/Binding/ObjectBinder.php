<?php


namespace Kinikit\Core\Binding;

use Kiniauth\Objects\Security\UserRole;
use Kinikit\Core\Exception\InsufficientParametersException;
use Kinikit\Core\Exception\WrongParametersException;
use Kinikit\Core\Reflection\Property;
use Kinikit\Core\Util\Primitive;
use Kinikit\Core\Reflection\ClassInspectorProvider;
use Kinikit\Core\Reflection\Parameter;

/**
 * Binder object used for converting an object (or an array of objects) recursively to associative array data
 * and vice versa.  Similar to GSON for Java.
 *
 * @noProxy
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
    public function bindFromArray($data, $targetClass, $publicOnly = true) {

        $result = null;

        $arrayTrimmed = preg_replace("/\[[a-z]*\]$/", "", $targetClass);

        // If an array of objects, process these next.
        if ($arrayTrimmed != $targetClass) {

            $result = [];

            foreach ($data as $key => $dataItem) {
                $result[$key] = $this->bindFromArray($dataItem, $arrayTrimmed, $publicOnly);
            }

        } else {

            // if a primitive, shortcut and return the value intact.
            if (in_array($targetClass, Primitive::TYPES)) {
                return Primitive::convertToPrimitive($targetClass, $data);
            }


            try {

                $classInspector = $this->classInspectorProvider->getClassInspector($targetClass);

                $processedKeys = array();


                // Process constructor first.
                if ($classInspector->getConstructor()) {
                    foreach ($classInspector->getConstructor()->getParameters() as $parameter) {

                        $key = $parameter->getName();
                        if (isset($data[$key])) {
                            $data[$key] = $this->bindFromArray($data[$key], $parameter->getType(), $publicOnly);
                            $processedKeys[] = $key;
                        }

                    }
                }


                // Inject each setter first of all.
                foreach ($classInspector->getSetters() as $key => $setter) {
                    if (!in_array($key, $processedKeys) && isset($data[$key]) && sizeof($setter->getParameters()) > 0) {
                        $parameter = $setter->getParameters()[0];
                        $parameterType = $parameter->getType();
                        $data[$key] = $this->bindFromArray($data[$key], $parameterType, $publicOnly);
                        $processedKeys[] = $key;
                    }
                }


                // Loop through each property as fall back provided public included.
                foreach ($classInspector->getProperties() as $key => $property) {
                    if (!in_array($key, $processedKeys) && isset($data[$key])) {
                        $propertyType = $property->getType();
                        $data[$key] = $this->bindFromArray($data[$key], $propertyType, $publicOnly);
                        $processedKeys[] = $key;
                    }
                }


                // Construct the class first and then call setters
                $instance = $classInspector->createInstance($data);
                $classInspector->setPropertyData($instance, $data, null, $publicOnly);


            } catch (WrongParametersException $e) {
                throw new ObjectBindingException($e);
            } catch (InsufficientParametersException $e) {
                throw new ObjectBindingException($e);
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
    public function bindToArray($object, $publicOnly = true) {


        if ($object === null) {
            return $object;
        }

        // if primitive, return intact straight away.
        if (Primitive::isPrimitive($object)) {
            return $object;
        }

        // Handle array case
        if (is_array($object)) {
            $objects = [];
            foreach ($object as $key => $value) {
                $objects[$key] = $this->bindToArray($value, $publicOnly);
            }
            return $objects;
        }

        $classInspector = $this->classInspectorProvider->getClassInspector(get_class($object));

        $processedKeys = [];
        $targetArray = [];

        // Work through getters first of all
        $getters = $classInspector->getGetters();
        foreach ($getters as $key => $getter) {

            try {
                $value = $getter->call($object, []);

                $targetArray[$key] = $this->bindToArray($value, $publicOnly);
                $processedKeys[$key] = 1;
            } catch (\Throwable $e) {
                // Continue if exception on getter - omit from array.
            }
        }

        // Now work through members.
        $members = $classInspector->getProperties();
        foreach ($members as $key => $member) {
            if (!isset($processedKeys[$key]) && (!$publicOnly || $member->getVisibility() == Property::VISIBILITY_PUBLIC)) {
                $value = $member->get($object);
                $targetArray[$key] = $this->bindToArray($value, $publicOnly);
                $processedKeys[$key] = 1;
            }
        }


        return $targetArray;
    }


}
