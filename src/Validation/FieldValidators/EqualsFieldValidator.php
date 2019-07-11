<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/08/2018
 * Time: 11:11
 */

namespace Kinikit\Core\Validation\FieldValidators;


use Kinikit\Core\Validation\MisconfiguredValidatorException;
use Kinikit\Core\Reflection\ClassInspectorProvider;

class EqualsFieldValidator extends ObjectFieldValidator {


    /**
     * @var ClassInspectorProvider
     */
    private $classInspectorProvider;

    public function __construct($classInspectorProvider, $validationMessage = null) {
        parent::__construct($validationMessage);
        $this->classInspectorProvider = $classInspectorProvider;
    }

    /**
     * Validate a value
     *
     * @param $value string
     * @param $fieldName
     * @param $targetObject
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
            $classInspector = $this->classInspectorProvider->getClassInspector(get_class($targetObject));
            $otherFieldValue = $classInspector->getPropertyData($targetObject, $otherField);
            return $value == $otherFieldValue;
        } else {
            return $value == $validatorParams[0];
        }

    }
}
