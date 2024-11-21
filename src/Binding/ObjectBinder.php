<?php


namespace Kinikit\Core\Binding;

use Kinikit\Core\Exception\InsufficientParametersException;
use Kinikit\Core\Exception\WrongParametersException;
use Kinikit\Core\Reflection\ClassInspectorProvider;
use Kinikit\Core\Reflection\Property;
use Kinikit\Core\Util\Primitive;

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
    public function bindFromArray($data, $targetClass, $publicOnly = true, $throwOnExtraFields = false) {
        if (is_array($data)) {
            $originalKeys = array_keys($data);
        }

        $targetClass = str_replace("?", "", $targetClass);

        //Remove array suffix
        $arrayTrimmed = preg_replace("/\[[a-z]*\]$/", "", $targetClass);
        $arrayWithAngleBrackets = preg_match("/array<.+,\s*(.+)>$/", $targetClass, $matches);
        if (count($matches) > 1){
            $arrayTrimmed = $matches[1];
        }

        // If an array of objects, process these next.
        if ($arrayTrimmed != $targetClass) {
            $result = [];

            if (is_array($data)) {
                foreach ($data as $key => $dataItem) {
                    $result[$key] = $this->bindFromArray($dataItem, $arrayTrimmed, $publicOnly, throwOnExtraFields: $throwOnExtraFields);
                }
            }

            return $result;

        }

        $targetClasses = explode("|", $targetClass);

        if (count($targetClasses) > 1) {
            foreach ($targetClasses as $possibleClass) {
                try {
                    return $this->bindFromArray($data, $possibleClass, $publicOnly, throwOnExtraFields: $throwOnExtraFields);
                } catch (\Exception $exception) {
                    // Not this type
                }
            }
            throw new ObjectBindingException("Couldn't bind union type $targetClass to any of " . join(", ", $targetClasses));
        }

        if (enum_exists($targetClass)) {
            //Check enum is a string
            if (!Primitive::isOfPrimitiveType(Primitive::TYPE_STRING, $data)) {
                throw new ObjectBindingException("Enum requires string of the specific case to bind. E.g. 'ACTIVE' for DomainStatus::ACTIVE");
            }

            $cases = $targetClass::cases();
            foreach ($cases as $case) {
                if ($case->name == $data) {
                    return $case;
                }
            }
            throw new ObjectBindingException("Enum $targetClass failed to bind from string $data. \nUse the string of the case E.g. 'ACTIVE' for DomainStatus::ACTIVE");
        }

        // if a primitive, shortcut and return the value intact.
        if (Primitive::isStringPrimitiveType($targetClass)) {
            return Primitive::convertToPrimitive($targetClass, $data);
        }

        // if this is not an array we have a malformed data issue.
        if (!is_array($data)) {
            $dt = print_r($data, true);
            throw new ObjectBindingException("Bind data for object is not an array. \nData: $dt");
        }

        // Handle date time specifically
        if (trim($targetClass, "\\") == \DateTime::class && $data["timestamp"] ?? null) {
            return date_create_from_format("U", $data["timestamp"], new \DateTimeZone($data["timezone"]["name"] ?? "UTC"));
        }


        try {

            $classInspector = $this->classInspectorProvider->getClassInspector($targetClass);

            $processedKeys = array();


            // Process constructor first.
            if ($classInspector->getConstructor()) {
                foreach ($classInspector->getConstructor()->getParameters() as $parameter) {

                    $key = $parameter->getName();
                    if (isset($data[$key])) {
                        $data[$key] = $this->bindFromArray($data[$key], $parameter->getType(), $publicOnly, throwOnExtraFields: $throwOnExtraFields);
                        $processedKeys[] = $key;
                    }

                }
            }


            // Inject each setter for params which were passed in and weren't included in constructor
            foreach ($classInspector->getSetters() as $key => $setter) {
                if (!in_array($key, $processedKeys) && isset($data[$key]) && sizeof($setter->getParameters()) > 0) {
                    $parameter = $setter->getParameters()[0];
                    $parameterType = $parameter->getType();
                    $data[$key] = $this->bindFromArray($data[$key], $parameterType, $publicOnly, throwOnExtraFields: $throwOnExtraFields);
                    $processedKeys[] = $key;
                }
            }


            // Loop through each property as fall back provided public included.
            foreach ($classInspector->getProperties() as $key => $property) {
                if (!in_array($key, $processedKeys) && isset($data[$key])) {
                    $propertyType = $property->getType();
                    $data[$key] = $this->bindFromArray($data[$key], $propertyType, $publicOnly, throwOnExtraFields: $throwOnExtraFields);
                    $processedKeys[] = $key;
                }
            }

            if ($throwOnExtraFields) {
                sort($originalKeys);
                sort($processedKeys);
                if ($originalKeys != $processedKeys) {
                    throw new ObjectBindingException(
                        "Failed to bind all the given keys in array. Bound keys: [" . join(", ", $processedKeys) . "] Keys to bind: [". join(", ", array_keys($data)) . "]");
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

        return $instance;


    }


    /**
     * Bind an object / array of objects to an array
     *
     * @param $object
     * @return array|string
     */
    public function bindToArray($object, $publicOnly = true, $seenObjects = [], $ignoreGetters = false) {


        if ($object === null) {
            return $object;
        }

        // if primitive, return intact straight away.
        if (Primitive::isPrimitive($object)) {
            return $object;
        }

        if ($object instanceof \UnitEnum) {
            return $object->name;
        }

        // Handle array case
        if (is_array($object)) {
            $objects = [];
            foreach ($object as $key => $value) {
                $objects[$key] = $this->bindToArray($value, $publicOnly, $seenObjects);
            }
            return $objects;
        }

        // If we have already seen this object, return null
        if (in_array($object, $seenObjects)) {
            return null;
        } else {
            $seenObjects[] = $object;
        }


        // If a resource we can't serialise
        if (is_resource($object)) {
            return null;
        }

        // if a resource, return intact
        if (!is_object($object)) {
            return $object;
        }


        $classInspector = $this->classInspectorProvider->getClassInspector(get_class($object));

        $processedKeys = [];
        $targetArray = [];

        // Work through getters first of all
        if (!$ignoreGetters) {
            $getters = $classInspector->getGetters();
            foreach ($getters as $key => $getter) {

                try {
                    $value = $getter->call($object, []);

                    $targetArray[$key] = $this->bindToArray($value, $publicOnly, $seenObjects);
                    $processedKeys[$key] = 1;
                } catch (\Throwable $e) {
                    // Continue if exception on getter - omit from array.
                }
            }
        }

        // Now work through members.
        $members = $classInspector->getProperties();
        foreach ($members as $key => $member) {
            if (!isset($processedKeys[$key]) && (!$publicOnly || $member->getVisibility() == Property::VISIBILITY_PUBLIC)) {
                $value = $member->get($object);
                $targetArray[$key] = $this->bindToArray($value, $publicOnly, $seenObjects);
                $processedKeys[$key] = 1;
            }
        }


        return $targetArray;
    }


}
