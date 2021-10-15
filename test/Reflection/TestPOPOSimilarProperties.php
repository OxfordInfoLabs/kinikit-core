<?php


namespace Kinikit\Core\Reflection;


class TestPOPOSimilarProperties {

    /**
     * @var string
     */
    private $nameKey;

    /**
     * @var string
     */
    private $name;

    /**
     * TestPOPOSimilarProperties constructor.
     *
     * @param string $nameKey
     * @param string $name
     */
    public function __construct($nameKey, $name) {
        $this->nameKey = $nameKey;
        $this->name = $name;
    }


    /**
     * @return string
     */
    public function getNameKey() {
        return $this->nameKey;
    }

    /**
     * @param string $nameKey
     */
    public function setNameKey($nameKey) {
        $this->nameKey = $nameKey;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }


}