<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 16/08/2018
 * Time: 17:10
 */

namespace Kinikit\Core\Validation\FieldValidators;


use Kinikit\Core\Object\SerialisableObject;

abstract class ObjectFieldValidator extends SerialisableObject {


    private $validationMessage;

    public function __construct($validationMessage = null) {
        $this->validationMessage = $validationMessage;
    }

    /**
     * Validate a value
     *
     * @param $value string
     * @param $fieldName
     * @param $targetObject SerialisableObject
     * @param $validatorParams array
     * @param $validatorKey
     * @return mixed
     */
    public abstract function validateObjectFieldValue($value, $fieldName, $targetObject, &$validatorParams, $validatorKey);


    /**
     * @return null
     */
    public function getValidationMessage() {
        return $this->validationMessage;
    }

    /**
     * @param null $validationMessage
     */
    public function setValidationMessage($validationMessage) {
        $this->validationMessage = $validationMessage;
    }


}