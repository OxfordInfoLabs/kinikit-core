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
     * Return a boolean indicator as to whether or not this value is primitive
     *
     * @param $value
     */
    public static function isPrimitive($value) {
        return is_bool($value) || is_int($value) || is_float($value) || is_string($value);
    }


    /**
     * Check whether a value is of a primitive type.
     *
     * @param $primitiveType
     */
    public static function isOfPrimitiveType($primitiveType, $value) {
        switch ($primitiveType) {
            case self::TYPE_BOOLEAN:
            case self::TYPE_BOOL:
                return is_bool($value) || ($value === "true" || $value === "false") || is_int($value);
            case self::TYPE_INT;
            case self::TYPE_INTEGER:
                return is_int($value) || is_numeric($value) && is_int(+$value);
            case self::TYPE_FLOAT:
                return is_float($value) || is_numeric($value) && is_float(+$value);
            case self::TYPE_STRING:
                return is_string($value) || is_numeric($value) || is_float($value) || is_bool($value);
            case self::TYPE_MIXED:
                return true;
        }

        return false;
    }


    /**
     * Convert a value to the correct primitive type.  Returns the value intact if not possible.
     *
     * @param $primitiveType
     * @param $value
     */
    public static function convertToPrimitive($primitiveType, $value) {
        if (self::isOfPrimitiveType($primitiveType, $value)) {
            switch ($primitiveType) {
                case self::TYPE_BOOLEAN:
                case self::TYPE_BOOL:
                    if ($value === "true") return true;
                    if ($value === "false") return false;
                    return boolval($value);
                case self::TYPE_INT;
                case self::TYPE_INTEGER:
                    return intval($value);
                case self::TYPE_FLOAT:
                    return floatval($value);
            }
        }

        return $value;
    }


}
