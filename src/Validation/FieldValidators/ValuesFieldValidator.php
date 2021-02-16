<?php


namespace Kinikit\Core\Validation\FieldValidators;


use Kinikit\Core\Logging\Logger;
use Kinikit\Core\Validation\MisconfiguredValidatorException;

class ValuesFieldValidator extends ObjectFieldValidator {


    /**
     * Validate a value against an array of values which can either be supplied
     * as an array of strings or an array of Label/Value array objects.
     *
     * @param mixed $value
     * @param array $values
     */
    public function validate($value, $values) {
        if (is_array($values)) {
            foreach ($values as $matchValue) {
                if (is_string($matchValue) && $matchValue == $value)
                    return true;
                else if (is_array($matchValue) && ($matchValue["value"] ?? "") == $value)
                    return true;
            }
        }

        return false;

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

    public function getEvaluatedValidationMessage($placeholderArguments = []) {

        if (is_array($placeholderArguments)) {
            $values = [];
            foreach ($placeholderArguments as $argument) {
                if (is_string($argument)) {
                    $values[] = $argument;
                } else if (is_array($argument) && isset($argument["value"])) {
                    $values[] = $argument["value"];
                }
            }
            $placeholderArguments = ["[" . join(", ", $values) . "]"];
        } else {
            $placeholderArguments = ["Defined Values"];
        }

        return parent::getEvaluatedValidationMessage($placeholderArguments);
    }


}
