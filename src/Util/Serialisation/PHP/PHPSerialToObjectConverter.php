<?php

namespace Kinikit\Core\Util\Serialisation\PHP;

use Kinikit\Core\Util\Serialisation\FormatToObjectConverter;

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