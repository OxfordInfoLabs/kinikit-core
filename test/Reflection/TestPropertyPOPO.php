<?php


namespace Kinikit\Core\Reflection;


class TestPropertyPOPO {

    /**
     * @var string
     */
    private $hidden;

    /**
     * @var int
     */
    private $constructorOnly;


    /**
     * @var string
     */
    private $withGetter;


    /**
     * @var TestAnnotatedPOPO
     */
    private $withSetter;


    /**
     * @var string
     */
    private $withSetterAndGetter;


    /**
     * @var TestTypedPOPO
     */
    public $writable;


    private $__setterValues = [];

    /**
     * Constructor
     *
     * TestPropertyPOPO constructor.
     */
    public function __construct(int $constructorOnly) {
        $this->constructorOnly = $constructorOnly;
    }

    /**
     * @return string
     */
    public function getWithGetter(): string {
        return "GETTER_CALLED";
    }

    /**
     * @return string
     */
    public function getWithSetterAndGetter(): string {
        return "GETTER_CALLED";
    }

    /**
     * @param TestAnnotatedPOPO $withSetter
     */
    public function setWithSetter(TestAnnotatedPOPO $withSetter): void {
        $this->__setterValues["withSetter"] = $withSetter;
    }

    /**
     * @param string $withSetterAndGetter
     */
    public function setWithSetterAndGetter(string $withSetterAndGetter): void {
        $this->__setterValues["withSetterAndGetter"] = $withSetterAndGetter;
    }


    // Return data for testing
    public function returnData() {
        return array("hidden" => $this->hidden, "constructorOnly" => $this->constructorOnly,
            "withGetter" => $this->withGetter, "withSetter" => $this->withSetter,
            "withSetterAndGetter" => $this->withSetterAndGetter, "writable" => $this->writable);
    }


    // Return setter values for testing
    public function returnSetterValues() {
        return $this->__setterValues;
    }

}
