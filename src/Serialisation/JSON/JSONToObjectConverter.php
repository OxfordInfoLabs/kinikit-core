<?php

namespace Kinikit\Core\Serialisation\JSON;

use Kinikit\Core\Exception\PropertyNotWritableException;
use Kinikit\Core\Util\ClassUtils;
use Kinikit\Core\Util\Logging\Logger;
use Kinikit\Core\Util\ObjectArrayUtils;
use Kinikit\Core\Serialisation\FormatToObjectConverter;

/**
 * @noProxy
 *
 * Class JSONToObjectConverter
 * @package Kinikit\Core\Serialisation\JSON
 */
class JSONToObjectConverter implements FormatToObjectConverter {

    /**
     * Convert a json string into objects.  If the mapToClass member is passed
     * the converter will attempt to map the result to an instance of that class type or array.
     *
     * @param string $jsonString
     * @param string $mapToClass
     */
    public function convert($jsonString, $mapToClass = null) {

        // Decode the string using PHP JSON Decode routine
        $converted = json_decode($jsonString, true);

        if ($mapToClass) {
            $converted = ObjectArrayUtils::convertArrayToSerialisableObjects($converted, $mapToClass);
        }

        // Now convert to objects and return
        return $converted;
    }


}

?>
