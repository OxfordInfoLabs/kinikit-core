<?php


namespace Kinikit\Core\Binding;


class SimpleStrongTypeConstructorObject {

    /**
     * @var string
     */
    public $name;

    /**
     * @var integer
     */
    public $age;

    /**
     * @var string
     */
    public $dob;

    /**
     * SimpleStrongTypeConstructorObject constructor.
     * @param string $name
     * @param int $age
     * @param string $dob
     */
    public function __construct(string $name, int $age, string $dob) {
        $this->name = $name;
        $this->age = $age;
        $this->dob = $dob;
    }


}
