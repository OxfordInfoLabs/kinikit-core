<?php


namespace Kinikit\Core\Binding;


class SimpleGetterSetterObj {

    private $name;
    private $age;
    private $dob;

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getAge() {
        return $this->age;
    }

    /**
     * @param mixed $age
     */
    public function setAge($age): void {
        $this->age = $age;
    }

    /**
     * @return mixed
     */
    public function getDob() {
        return $this->dob;
    }

    /**
     * @param mixed $dob
     */
    public function setDob($dob): void {
        $this->dob = $dob;
    }


}
