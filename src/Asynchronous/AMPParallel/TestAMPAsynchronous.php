<?php


namespace Kinikit\Core\Asynchronous\AMPParallel;


use Kinikit\Core\Asynchronous\Asynchronous;

class TestAMPAsynchronous extends Asynchronous {

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $evaluatedProperty;

    /**
     * Construct this class
     *
     * TestAsynchronous constructor.
     */
    public function __construct($name) {
        $this->name = $name;
    }


    /**
     * Do a simple evaluation
     *
     * @return mixed|void
     */
    public function run() {

        if ($this->name == "FAIL") {
            throw new \Exception("Failed");
        }
        $this->evaluatedProperty = "Evaluated: " . $this->name;

        return "Returned: " . $this->name;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEvaluatedProperty() {
        return $this->evaluatedProperty;
    }


}