<?php

namespace Kinikit\Core\Serialisation\PHP;

use Kinikit\Core\Serialisation\ObjectToFormatConverter;

/**
 * Converter which recursively converts Objects / Values to JSON.
 *
 */
class ObjectToPHPSerialConverter implements ObjectToFormatConverter {

    /**
     * Convert a passed object to JSON notation
     *
     */
    public function convert($object) {
        return serialize($object);
    }


}

?>
