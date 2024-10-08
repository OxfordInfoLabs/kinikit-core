<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/08/2018
 * Time: 11:11
 */

namespace Kinikit\Core\Validation\FieldValidators;


use Kinikit\Core\Reflection\ClassInspectorProvider;
use Kinikit\Core\Validation\MisconfiguredValidatorException;

class EqualsFieldValidator extends ObjectFieldValidator {


    /**
     * @var ClassInspectorProvider
     */
    private $classInspectorProvider;

    public function __construct($classInspectorProvider, $validatorKey, $validationMessage = null) {
        parent::__construct($validatorKey, $validationMessage);
        $this->classInspectorProvider = $classInspectorProvider;
    }


    /**
     * Validate a value
     *
     * @param $value string
     * @param $fieldName
     * @param $targetObject
     * @param $validatorParams array
     * @return mixed
     * @throws MisconfiguredValidatorException
     */
    public function validateObjectFieldValue($value, $fieldName, $targetObject, &$validatorParams) {

        if (count($validatorParams) < 1) {
            throw new MisconfiguredValidatorException($this->getValidatorKey(), $fieldName, $targetObject);
        }

        $otherField = ltrim($validatorParams[0], "$");
        if (trim($otherField, "'\"") == $otherField) {

            if (is_object($targetObject)) {
                $classInspector = $this->classInspectorProvider->getClassInspector(get_class($targetObject));
                $otherFieldValue = $classInspector->getPropertyData($targetObject, $otherField);
            } else if (is_array($targetObject)) {
                $otherFieldValue = $targetObject[$otherField] ?? null;
            } else {
                $otherFieldValue = null;
            }
            return $value == $otherFieldValue;
        } else {
            return $value == $validatorParams[0];
        }

    }
}
