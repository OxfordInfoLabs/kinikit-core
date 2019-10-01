<?php


namespace Kinikit\Core\Reflection;


use Kinikit\Core\Annotation\Annotation;

trait TestPOPOTrait {

    private $id;
    private $name;
    private $dob;

    /**
     * TestPOPO constructor.
     *
     * @param int $id
     * @param string $name
     * @param string $dob
     */
    public function __construct($id, $name, $dob = "01/01/2016") {
        $this->id = $id;
        $this->name = $name;
        $this->dob = $dob;
    }


    /**
     * @return int
     */
    public function getId() {
        return $this->id;
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

    /**
     * @param string $dob
     */
    public function setDob($dob) {
        $this->dob = $dob;
    }

    /**
     * @return bool
     */
    public function isSpecial() {
        return true;
    }

    /**
     * Clone method testing a class type
     *
     * @param TestAnnotatedPOPO $otherPOPO
     * @return TestAnnotatedPOPO
     */
    public function clone($otherPOPO) {
        return clone $otherPOPO;
    }


    /**
     * Annotation
     *
     * @param Annotation $annotation
     * @return Annotation
     */
    public function evaluateAnnotation($annotation) {
        return $annotation;
    }

}
