<?php


namespace Kinikit\Core\Validation\FieldValidators;

use Kinikit\Core\Reflection\ClassInspectorProvider;

/**
 * Validator where one of a group of field values is required
 *
 * Class RequiresOneFieldValidator
 * @package Kinikit\Core\Validation\FieldValidators
 */
class RequiredEitherValidator extends ObjectFieldValidator {
    /**
     * @var ClassInspectorProvider
     */
    private $classInspectorProvider;


    /**
     * RequiresOneFieldValidator constructor.
     */
    public function __construct($classInspectorProvider, $validatorKey, $validationMessage = null) {
        $this->classInspectorProvider = $classInspectorProvider;
        parent::__construct($validatorKey, $validationMessage);
    }


    /**
     * Validate a value
     *
     * @param mixed $value
     * @param string $fieldName
     * @param string[] $otherFields
     * @param object $targetObject
     */
    public function validate($value, $fieldName, $otherFields, $targetObject) {

        $requiredFieldValidator = new RequiredFieldValidator("required");

        // Shortcut the process if this field is set.
        if ($requiredFieldValidator->validate($value))
            return true;

        // Check whether any of the other fields are set.
        foreach ($otherFields as $otherField) {
            if (is_object($targetObject)) {
                $classInspector = $this->classInspectorProvider->getClassInspector(get_class($targetObject));
                $otherFieldValue = $classInspector->getPropertyData($targetObject, $otherField);
            } else if (is_array($targetObject)) {
                $otherFieldValue = $targetObject[$otherField] ?? null;
            } else {
                $otherFieldValue = null;
            }
            if ($requiredFieldValidator->validate($otherFieldValue))
                return true;
        }

        $this->setValidationMessage("One of the fields $fieldName, " . join(", ", $otherFields) . " is required");

        return false;
    }

    /**
     * Validate object field value
     *
     * @param string $value
     * @param $fieldName
     * @param SerialisableObject $targetObject
     * @param array $validatorParams
     * @return mixed|void
     */
    public function validateObjectFieldValue($value, $fieldName, $targetObject, &$validatorParams) {
        return $this->validate($value, $fieldName, $validatorParams, $targetObject);
    }
}