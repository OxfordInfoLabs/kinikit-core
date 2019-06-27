<?php


namespace Kinikit\Core\Binding;


class SimpleStrongTypeGetterSetterObj {

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $age;

    /**
     * @var string
     */
    private $dob;

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getAge(): int {
        return $this->age;
    }

    /**
     * @param int $age
     */
    public function setAge(int $age): void {
        $this->age = $age;
    }

    /**
     * @return string
     */
    public function getDob(): string {
        return $this->dob;
    }

    /**
     * @param string $dob
     */
    public function setDob(string $dob): void {
        $this->dob = $dob;
    }


}
