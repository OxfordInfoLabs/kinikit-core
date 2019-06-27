<?php


namespace Kinikit\Core\Util;

/**
 * Static primitive class for resolving primitive issues.
 *
 * Class Primitive
 * @package Kinikit\Core\Util
 */
class Primitive {

    // Built in primitive types
    const TYPES = ["bool", "int", "float", "string", "mixed"];

    const TYPE_BOOLEAN = "bool";
    const TYPE_INT = "int";
    const TYPE_FLOAT = "float";
    const TYPE_STRING = "string";
    const TYPE_MIXED = "mixed";

    /**
     * Check whether an object is of a primitive type.
     *
     * @param $primitiveType
     */
    public static function isOfPrimitiveType($primitiveType, $value) {
        switch ($primitiveType) {
            case self::TYPE_BOOLEAN:
                return is_bool($value);
            case self::TYPE_INT;
                return is_int($value);
            case self::TYPE_FLOAT:
                return is_float($value);
            case self::TYPE_STRING:
                return is_string($value);
        }

        return false;
    }

}
