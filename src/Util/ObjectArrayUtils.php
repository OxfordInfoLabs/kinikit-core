<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 16/08/2018
 * Time: 17:04
 */

namespace Kinikit\Core\Util;


use Kinikit\Core\DependencyInjection\Container;
use Kinikit\Core\Exception\ClassNotSerialisableException;
use Kinikit\Core\Object\AssociativeArray;
use Kinikit\Core\Object\SerialisableObject;
use Kinikit\Core\Reflection\ClassInspector;
use Kinikit\Core\Reflection\ClassInspectorProvider;


class ObjectArrayUtils {


    /**
     * Get an array of member values for a given member for the array of objects passed
     * using the same indexing system as the passed objects.
     *
     * @static
     * @param $member
     * @param $objects
     */
    public static function getMemberValueArrayForObjects($member, $objects) {

        $returnValues = array();

        $classInspectorProvider = Container::instance()->get(ClassInspectorProvider::class);

        $currentInspector = null;
        foreach ($objects ?? [] as $key => $value) {

            if (is_object($value)) {

                if (!$currentInspector || (get_class($value) != $currentInspector->getClassName())) {
                    $currentInspector = $classInspectorProvider->getClassInspector(get_class($value));
                }

                $returnValues[$key] = $currentInspector->getPropertyData($value, $member);
            }
        }

        return $returnValues;

    }


    /**
     * Index the array of passed objects by the supplied member, returning an associative array.
     *
     * @param $member
     * @param $objects
     */
    public static function indexArrayOfObjectsByMember($member, $objects) {


        // Ensure we make an array to simplify processing of members below.
        if (!is_array($member)) {
            $member = [$member];
        }


        // Get the next member to use for indexing in order.
        $nextMember = array_shift($member);

        $groupedItems = self::groupArrayOfObjectsByMember($nextMember, $objects);

        // Call this recursively if we have more members to process
        foreach ($groupedItems as $key => $items) {
            if (sizeof($member) > 0) {
                $groupedItems[$key] = self::indexArrayOfObjectsByMember($member, $items);
            } else {
                $groupedItems[$key] = $items[0];
            }
        }


        return $groupedItems;
    }


    /**
     * Filter an array of objects by a specified member.  Perhaps in the future extend
     * to multiple match types.
     *
     * @param $member
     * @param $objects
     * @param $filterValue
     */
    public static function filterArrayOfObjectsByMember($member, $objects, $filterValue) {

        $filterValues = is_array($filterValue) ? $filterValue : array($filterValue);

        $filteredObjects = array();

        $classInspectorProvider = Container::instance()->get(ClassInspectorProvider::class);

        $currentInspector = null;

        foreach ($objects as $object) {

            foreach ($filterValues as $value) {

                if (!$currentInspector || (get_class($object) != $currentInspector->getClassName())) {
                    $currentInspector = $classInspectorProvider->getClassInspector(get_class($object));
                }

                if ($value == $currentInspector->getPropertyData($object, $member)) {
                    $filteredObjects[] = $object;
                    break;
                }
            }
        }

        return $filteredObjects;

    }


    /**
     * Group an array of objects by a given member.
     *
     * @param $member
     * @param $objects
     */
    public static function groupArrayOfObjectsByMember($member, $objects) {

        if (!is_array($member))
            $member = array($member);

        $leafMember = array_pop($member);


        $groupedObjects = [];

        $classInspectorProvider = Container::instance()->get(ClassInspectorProvider::class);


        $currentInspector = null;


        foreach ($objects as $object) {

            if (!$currentInspector || (get_class($object) != $currentInspector->getClassName())) {
                $currentInspector = $classInspectorProvider->getClassInspector(get_class($object));
            }

            $rootNode = &$groupedObjects;
            foreach ($member as $memberComponent) {
                $groupValue = $currentInspector->getPropertyData($object, $memberComponent);
                if (!$groupValue && !is_numeric($groupValue)) $groupValue = "NULL";

                if (!isset($rootNode[$groupValue]))
                    $rootNode[$groupValue] = [];

                $rootNode = &$rootNode[$groupValue];
            }

            $leafValue = $groupValue = $currentInspector->getPropertyData($object, $leafMember);
            if (!$leafValue && !is_numeric($leafValue)) $leafValue = "NULL";

            if (!isset($rootNode[$leafValue]))
                $rootNode[$leafValue] = array();

            $leafValues = $rootNode[$leafValue];
            $leafValues[] = $object;
            $rootNode[$leafValue] = $leafValues;
        }

        return $groupedObjects;


    }


    /**
     * Get a member value for an object based upon a passed path.
     *
     * @param $object
     * @param $memberPath
     * @return mixed|null
     */
    public static function getObjectMemberValue($object, $memberPath) {

        $explodedExpression = explode(".", $memberPath);
        foreach ($explodedExpression as $expression) {

            // Must be either an object or array
            if (is_object($object)) {
                $classInspector = new ClassInspector(get_class($object));
                $object = $classInspector->getPropertyData($object, $expression);
            } else if (is_array($object)) {
                $object = $object[$expression];
            }
        }
        return $object;
    }


}
