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
    const TYPES = ["bool", "boolean", "int", "integer", "float", "string", "mixed", "void"];

    const TYPE_BOOL = "bool";
    const TYPE_BOOLEAN = "boolean";
    const TYPE_INT = "int";
    const TYPE_INTEGER = "integer";
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
            case self::TYPE_BOOL:
                return is_bool($value);
            case self::TYPE_INT;
            case self::TYPE_INTEGER:
                return is_int($value);
            case self::TYPE_FLOAT:
                return is_float($value);
            case self::TYPE_STRING:
                return is_string($value);
            case self::TYPE_MIXED:
                return true;
        }

        return false;
    }

}
