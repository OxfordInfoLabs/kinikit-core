<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/08/2018
 * Time: 11:30
 */

namespace Kinikit\Core\Validation;

class TestValidatedObject {


    /**
     * @required
     * @numeric
     */
    private $id;

    /**
     * @required
     * @alphanumeric
     * @minLength 3
     */
    private $username;

    /**
     * @required
     * @name
     */
    private $name;

    /**
     * @regexp [0-9a-z]*
     * @minLength 8
     * @maxLength 16
     */
    private $password;

    /**
     * @equals password
     */
    private $confirmPassword;

    /**
     * @range 18,65
     */
    private $age;

    /**
     * @min 3
     * @max 11
     */
    private $shoeSize;

    /**
     * @email
     */
    private $emailAddress;


    /**
     * @date
     */
    private $standardDate;


    /**
     * @date d-m-Y
     */
    private $customDate;


    /**
     * @requiredEither requiredFirstGroup2
     */
    private $requiredFirstGroup1;


    private $requiredFirstGroup2;


    /**
     * @requiredEither requiredSecondGroup2
     */
    private $requiredSecondGroup1;

    private $requiredSecondGroup2;


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

    /**
     * @return mixed
     */
    public function getRequiredFirstGroup1() {
        return $this->requiredFirstGroup1;
    }

    /**
     * @param mixed $requiredFirstGroup1
     */
    public function setRequiredFirstGroup1($requiredFirstGroup1) {
        $this->requiredFirstGroup1 = $requiredFirstGroup1;
    }

    /**
     * @return mixed
     */
    public function getRequiredFirstGroup2() {
        return $this->requiredFirstGroup2;
    }

    /**
     * @param mixed $requiredFirstGroup2
     */
    public function setRequiredFirstGroup2($requiredFirstGroup2) {
        $this->requiredFirstGroup2 = $requiredFirstGroup2;
    }

    /**
     * @return mixed
     */
    public function getRequiredSecondGroup1() {
        return $this->requiredSecondGroup1;
    }

    /**
     * @param mixed $requiredSecondGroup1
     */
    public function setRequiredSecondGroup1($requiredSecondGroup1) {
        $this->requiredSecondGroup1 = $requiredSecondGroup1;
    }

    /**
     * @return mixed
     */
    public function getRequiredSecondGroup2() {
        return $this->requiredSecondGroup2;
    }

    /**
     * @param mixed $requiredSecondGroup2
     */
    public function setRequiredSecondGroup2($requiredSecondGroup2) {
        $this->requiredSecondGroup2 = $requiredSecondGroup2;
    }


}
