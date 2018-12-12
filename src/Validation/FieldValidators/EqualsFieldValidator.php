<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/08/2018
 * Time: 11:11
 */

namespace Kinikit\Core\Validation\FieldValidators;


use Kinikit\Core\Exception\MisconfiguredValidatorException;

class EqualsFieldValidator extends ObjectFieldValidator {

    /**
     * Validate a value
     *
     * @param $value string
     * @param $fieldName
     * @param $targetObject SerialisableObject
     * @param $validatorParams array
     * @param $validatorKey
     * @return mixed
     * @throws MisconfiguredValidatorException
     */
    public function validateObjectFieldValue($value, $fieldName, $targetObject, &$validatorParams, $validatorKey) {

        if (sizeof($validatorParams) < 1) {
            throw new MisconfiguredValidatorException($validatorKey, $fieldName, $targetObject);
        }

        $otherField = $validatorParams[0];
        if (trim($otherField, "'\"") == $otherField) {
            $otherFieldValue = $targetObject->__getSerialisablePropertyValue($otherField);
            return $value == $otherFieldValue;
        } else {
            return $value == $validatorParams[0];
        }

    }
}