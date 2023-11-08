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
    public static function mapArrayKeys($sourceArray, $keyMappings, $removeUnmappedKeys = true) {
        $returnArray = [];
        foreach ($sourceArray as $key => $item) {
            if (isset($keyMappings[$key])) {
                $returnArray[$keyMappings[$key]] = $item;
            } else if (!$removeUnmappedKeys) $returnArray[$key] = $item;
        }
        return $returnArray;
    }

}