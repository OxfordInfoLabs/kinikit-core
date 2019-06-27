<?php


namespace Kinikit\Core\Reflection;


class ReturnType {

    /**
     * @var string
     */
    private $type;

    /**
     * @var bool
     */
    private $explicitlyTyped;

    /**
     * ReturnType constructor.
     * @param string $type
     * @param bool $explicitlyTyped
     */
    public function __construct($type, $explicitlyTyped = false) {
        $this->type = $type;
        $this->explicitlyTyped = $explicitlyTyped;
    }


    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isExplicitlyTyped() {
        return $this->explicitlyTyped;
    }


}
