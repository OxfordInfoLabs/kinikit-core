<?php


namespace Kinikit\Core\Serialisation\XML;

use Kinikit\Core\Object\SerialisableObject;

class TestWithNoneSerialisable extends SerialisableObject {

    private $name;
    private $phone;
    private $age;
    private $notes;

    public function TestWithNoneSerialisable($name = null, $phone = null, $age = null, $notes = null) {
        $this->name = $name;
        $this->phone = $phone;
        $this->age = $age;
        $this->notes = $notes;
    }

    public function getName() {
        return $this->name;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function getAge() {
        return $this->age;
    }

    public function getNotes() {
        return $this->notes;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setPhone($phone) {
        $this->phone = $phone;
    }

    public function setAge($age) {
        $this->age = $age;
    }

    public function setNotes($notes) {
        $this->notes = $notes;
    }

    public function noneSerialisableProperties() {
        return array("phone", "age");
    }

}

?>
