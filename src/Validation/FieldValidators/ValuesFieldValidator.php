<?php


namespace Kinikit\Core\Validation\FieldValidators;


use Kinikit\Core\Validation\MisconfiguredValidatorException;

class ValuesFieldValidator extends ObjectFieldValidator {


    /**
     * Validate a value against an array of values
     *
     * @param mixed $value
     * @param array $values
     */
    public function validate($value, $values) {
        return in_array($value, $values);
    }


    /**
     * Validate an object field value - this is used by the Validator
     * object in the case of an object
     *
     * @param $value string
     * @param $fieldName
     * @param $targetObject SerialisableObject
     * @param $validatorParams array
     * @return mixed
     */
    public function validateObjectFieldValue($value, $fieldName, $targetObject, &$validatorParams) {

        if (!$value) return true;

        if (!is_array($validatorParams)) {
            throw new MisconfiguredValidatorException($this->getValidatorKey(), $fieldName, $targetObject);
        }

        return $this->validate($value, $validatorParams);
    }
}
