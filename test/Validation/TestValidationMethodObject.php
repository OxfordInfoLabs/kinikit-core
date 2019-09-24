<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 24/09/2019
 * Time: 14:55
 */

namespace Kinikit\Core\Validation;


class TestValidationMethodObject {

    /**
     * Name field
     *
     * @required
     * @var string
     */
    private $name;

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name) {
        $this->name = $name;
    }


    public function validate() {
        return ["custom" => new FieldValidationError("custom", "wobbly", "This object is wobbly")];
    }

}