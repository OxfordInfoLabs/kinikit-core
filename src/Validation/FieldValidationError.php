<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/08/2018
 * Time: 10:29
 */

namespace Kinikit\Core\Validation;


class FieldValidationError  {

    protected $fieldName;
    protected $validatorKey;
    protected $errorMessage;


    /**
     * FieldValidationError constructor.
     * @param $fieldName
     * @param $validatorKey
     * @param $errorMessage
     */
    public function __construct($fieldName = null, $validatorKey = null, $errorMessage = null) {
        $this->fieldName = $fieldName;
        $this->validatorKey = $validatorKey;
        $this->errorMessage = $errorMessage;
    }


    /**
     * @return mixed
     */
    public function getFieldName() {
        return $this->fieldName;
    }

    /**
     * @return mixed
     */
    public function getValidatorKey() {
        return $this->validatorKey;
    }

    /**
     * @return mixed
     */
    public function getErrorMessage() {
        return $this->errorMessage;
    }


}
