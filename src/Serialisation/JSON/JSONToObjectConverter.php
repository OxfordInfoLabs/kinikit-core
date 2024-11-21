<?php

namespace Kinikit\Core\Serialisation\JSON;

use Kinikit\Core\Binding\ObjectBinder;
use Kinikit\Core\Exception\PropertyNotWritableException;
use Kinikit\Core\Serialisation\FormatToObjectConverter;
use Kinikit\Core\Util\ClassUtils;
use Kinikit\Core\Util\Logging\Logger;

/**
 * @noProxy
 *
 * Class JSONToObjectConverter
 * @package Kinikit\Core\Serialisation\JSON
 */
class JSONToObjectConverter implements FormatToObjectConverter {

    public function __construct(
        private ObjectBinder $objectBinder
    ) {
    }


    /**
     * Convert a json string into objects.  If the mapToClass member is passed
     * the converter will attempt to map the result to an instance of that class type or array.
     *
     * @param string $jsonString
     * @param string $mapToClass
     */
    public function convert($jsonString, $mapToClass = null, $throwOnExtraFields = false) {

        // Decode the string using PHP JSON Decode routine
        $converted = json_decode($jsonString, true);

        if ($mapToClass && $converted !== null) {
            $converted = $this->objectBinder->bindFromArray($converted, $mapToClass, throwOnExtraFields: $throwOnExtraFields);
        }

        // Now convert to objects and return
        return $converted;
    }


}

?>
