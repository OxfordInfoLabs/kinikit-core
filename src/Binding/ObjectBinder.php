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

                // Construct the class first.
                $instance = $classInspector->createInstance($data);

                // Attempt to call each setter method if we have data.
                $setters = $classInspector->getSetters();
                foreach ($setters as $member => $method) {
                    if (isset($data[$member])) {
                        $method->call($instance, $data);
                    }

                }

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
