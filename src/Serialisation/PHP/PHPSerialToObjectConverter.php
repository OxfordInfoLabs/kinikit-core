<?php

namespace Kinikit\Core\Serialisation\PHP;

use Kinikit\Core\Serialisation\FormatToObjectConverter;

class PHPSerialToObjectConverter implements FormatToObjectConverter {

    /**
     * Convert a php serialise
     *
     * @param string $jsonString
     */
    public function convert($jsonString) {
        return unserialize($jsonString);
    }


}

?>