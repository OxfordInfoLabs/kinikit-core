<?php

namespace Kinikit\Core\Object;

class BlankDynamic extends DynamicSerialisableObject {

    public function __construct() {
        parent::__construct(false);
    }

}

?>