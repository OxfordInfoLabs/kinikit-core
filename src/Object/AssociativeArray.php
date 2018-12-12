<?php

namespace Kinikit\Core\Object;

/**
 * Associative array explicit object.  This enables us to differentiate between numeric and associative arrays
 * especially when they are blank.  Particularly useful for transfer when serialising objects using JSON etc.
 *
 * Class AssociativeArray
 */
class AssociativeArray extends DynamicSerialisableObject {

    public function __construct() {
        parent::__construct(false);
    }


    /**
     * Convert this into a regular php array.
     */
    public function toArray($deep = true) {

        // Get each property
        $array = array();
        foreach ($this->__getSerialisablePropertyMap() as $key => $value) {
            if ($value instanceof AssociativeArray) {
                $array[$key] = $deep ? $value->toArray() : $value;
            } else {
                $array[$key] = $value;
            }
        }

        return $array;
    }

    /**
     * Convert a regular array to an associative array. optionally recursive is required.
     *
     * @param $array
     * @param bool $recursive
     */
    public static function toAssociative($array, $recursive = false) {
        $associative = new AssociativeArray();

        foreach($array as $key => $value) {
            if ($recursive && is_array($value)) {
                $associative[$key] = self::toAssociative($value, true);
            } else {
                $associative[$key] = $value;
            }
        }

        return $associative;

    }


} 