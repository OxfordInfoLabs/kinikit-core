<?php

namespace Kinikit\Core\Util;

class ArrayUtils {


    /**
     * Map array keys in a source array to alternative keys using the array of key mappings.  By default
     * unmapped keys are removed from the source array but this can be disabled with the boolean.
     *
     *
     * @param array $sourceArray
     * @param array $keyMappings
     * @param bool $removeUnmappedKeys
     *
     * @return array
     */
    public static function mapArrayKeys(array $sourceArray, array $keyMappings, bool $removeUnmappedKeys = true): array {
        $returnArray = [];
        foreach ($sourceArray as $key => $item) {
            if (isset($keyMappings[$key])) {
                $returnArray[$keyMappings[$key]] = $item;
            } else if (!$removeUnmappedKeys) {
                $returnArray[$key] = $item;
            }
        }
        return $returnArray;
    }


    /**
     * Recursive array merge which preserves keys at all levels
     *
     * @param array $array1
     * @param ...$arrays
     * @return array
     */
    public static function mergeArrayRecursive(array $array1, ...$arrays): array {

        foreach ($arrays as $array) {
            reset($array1); //important
            foreach ($array as $key => $value) {
                if (is_array($value) && @is_array($array1[$key] ?? null)) {
                    $array1[$key] = self::mergeArrayRecursive($array1[$key], $value);
                } else {
                    $array1[$key] = $value;
                }
            }
        }

        return $array1;
    }

    /**
     * Check if all of a list of booleans are true
     * @param bool[] $array
     * @return bool
     */
    public static function all(array $array): bool {
        $trueSoFar = true;
        foreach ($array as $bool) {
            if (!is_bool($bool)) {
                throw new \Exception("All only takes in booleans");
            }
            $trueSoFar = $bool && $trueSoFar;
        }
        return $trueSoFar;
    }

    /**
     * Check if any of a list of booleans are true
     * @param bool[] $array
     * @return bool
     */
    public static function any(array $array): bool {
        $anyTrueSoFar = false;
        foreach ($array as $bool) {
            if (!is_bool($bool)) {
                throw new \Exception("Any only takes in booleans, " . gettype($bool) . " passed in");
            }
            $anyTrueSoFar = $bool || $anyTrueSoFar;
        }
        return $anyTrueSoFar;
    }

}