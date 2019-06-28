<?php


namespace Kinikit\Core\Binding;

use Kinikit\Core\Exception\InsufficientParametersException;
use Kinikit\Core\Exception\ObjectBindingException;
use Kinikit\Core\Exception\WrongParametersException;
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


            try {

                $classInspector = $this->classInspectorProvider->getClassInspector($targetClass);

                $processedKeys = array();


                // Process constructor first.
                if ($classInspector->getConstructor()) {
                    foreach ($classInspector->getConstructor()->getParameters() as $parameter) {

                        $key = $parameter->getName();
                        if (isset($data[$key])) {
                            $data[$key] = $this->bindFromArray($data[$key], $parameter->getType());
                            $processedKeys[] = $key;
                        }

                    }
                }

                // Loop through each property
                foreach ($classInspector->getProperties() as $key => $property) {
                    if (!in_array($key, $processedKeys) && isset($data[$key])) {
                        $propertyType = $property->getType();
                        $data[$key] = $this->bindFromArray($data[$key], $propertyType);
                        $processedKeys[] = $key;
                    }
                }

                // Inject each property
                foreach ($classInspector->getSetters() as $key => $setter) {
                    if (!in_array($key, $processedKeys) && isset($data[$key]) && sizeof($setter->getParameters()) > 0) {
                        $parameter = $setter->getParameters[0];
                        $parameterType = $parameter->getType();
                        $data[$key] = $this->bindFromArray($data[$key], $parameterType);
                    }
                }


                // Construct the class first and then call setters
                $instance = $classInspector->createInstance($data);
                $classInspector->setPropertyData($instance, $data, null, false);


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
    public function bindToArray($object) {

    }


}
