<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/08/2018
 * Time: 11:30
 */

namespace Kinikit\Core\Validation;


use Kinikit\Core\Object\SerialisableObject;

class TestValidatedObject extends SerialisableObject {


    /**
     * @validation required,numeric
     */
    private $id;

    /**
     * @validation required,alphanumeric,minlength(3)
     */
    private $username;

    /**
     * @validation required,name
     */
    private $name;

    /**
     * @validation regexp([0-9a-z]*),minlength(8),maxlength(16)
     */
    private $password;

    /**
     * @validation equals(password)
     */
    private $confirmPassword;

    /**
     * @validation range(18|65)
     */
    private $age;

    /**
     * @validation min(3),max(11)
     */
    private $shoeSize;

    /**
     * @validation email
     */
    private $emailAddress;


    /**
     * @validation date
     */
    private $standardDate;


    /**
     * @validation date(d-m-Y)
     */
    private $customDate;


    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username) {
        $this->username = $username;
    }

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

    /**
     * @return mixed
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getConfirmPassword() {
        return $this->confirmPassword;
    }

    /**
     * @param mixed $confirmPassword
     */
    public function setConfirmPassword($confirmPassword) {
        $this->confirmPassword = $confirmPassword;
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
    public function setAge($age) {
        $this->age = $age;
    }

    /**
     * @return mixed
     */
    public function getShoeSize() {
        return $this->shoeSize;
    }

    /**
     * @param mixed $shoeSize
     */
    public function setShoeSize($shoeSize) {
        $this->shoeSize = $shoeSize;
    }

    /**
     * @return mixed
     */
    public function getEmailAddress() {
        return $this->emailAddress;
    }

    /**
     * @param mixed $emailAddress
     */
    public function setEmailAddress($emailAddress) {
        $this->emailAddress = $emailAddress;
    }

    /**
     * @return mixed
     */
    public function getStandardDate() {
        return $this->standardDate;
    }

    /**
     * @param mixed $standardDate
     */
    public function setStandardDate($standardDate) {
        $this->standardDate = $standardDate;
    }

    /**
     * @return mixed
     */
    public function getCustomDate() {
        return $this->customDate;
    }

    /**
     * @param mixed $customDate
     */
    public function setCustomDate($customDate) {
        $this->customDate = $customDate;
    }


}