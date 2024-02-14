<?php

namespace Kinikit\Core\Serialisation\JSON;

use Kinikit\Core\Binding\ObjectBinder;
use Kinikit\Core\Serialisation\ObjectToFormatConverter;

/**
 * Converter which recursively converts Objects / Values to JSON.
 *
 * @noProxy
 */
class ObjectToJSONConverter implements ObjectToFormatConverter {


    private $objectBinder;

    /**
     * Construct an object to JSON converter with autowired ObjectBinder
     *
     * ObjectToJSONConverter constructor.
     *
     * @param ObjectBinder $objectBinder
     */
    public function __construct($objectBinder) {
        $this->objectBinder = $objectBinder;
    }

    /**
     * Convert a passed object to JSON notation
     *
     */
    public function convert($object, $prettyPrint = false) {

        // Convert to array form first (public only).
        $object = $this->objectBinder->bindToArray($object);

        // Then simply encode using PHP JSON libraries - ignore UTF8 issues
        return json_encode($object, JSON_INVALID_UTF8_IGNORE + ($prettyPrint ? JSON_PRETTY_PRINT : null));
    }


}

?>
