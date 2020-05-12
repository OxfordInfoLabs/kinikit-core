<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/08/2018
 * Time: 11:26
 */

namespace Kinikit\Core\Validation;


use Kinikit\Core\DependencyInjection\Container;
use Kinikit\Core\Exception\ClassNotSerialisableException;
use Kinikit\Core\Exception\InvalidValidatorException;
use Kinikit\Core\Object\SerialisableObject;
use Kinikit\Core\Reflection\ClassInspectorProvider;
use Kinikit\Core\Serialisation\XML\XMLToObjectConverter;
use Kinikit\Core\Validation\FieldValidators\DateFieldValidator;
use Kinikit\Core\Validation\FieldValidators\EqualsFieldValidator;
use Kinikit\Core\Validation\FieldValidators\LengthFieldValidator;
use Kinikit\Core\Validation\FieldValidators\ObjectFieldValidator;
use Kinikit\Core\Validation\FieldValidators\RangeFieldValidator;
use Kinikit\Core\Validation\FieldValidators\RegexpFieldValidator;
use Kinikit\Core\Validation\FieldValidators\RequiredFieldValidator;

/**
 * Class Validator
 * @package Kinikit\Core\Validation
 *
 * @noProxy
 */
class Validator {


    /**
     * @var ClassInspectorProvider
     */
    private $classInspectorProvider;

    private $validators;

    /**
     * Validator constructor.
     * @param ClassInspectorProvider $classInspectorProvider
     */
    public function __construct($classInspectorProvider) {

        $this->classInspectorProvider = $classInspectorProvider;

        $this->addValidator(new RequiredFieldValidator("required", "This field is required"));
        $this->addValidator(new RegexpFieldValidator(null, "regexp", "Value does not match the required format"));
        $this->addValidator(new EqualsFieldValidator($classInspectorProvider, "equals", "Value does not match the $1 field"));
        $this->addValidator(new LengthFieldValidator(LengthFieldValidator::MODE_MIN, "minLength", "Value must be at least $1 characters"));
        $this->addValidator(new LengthFieldValidator(LengthFieldValidator::MODE_MAX, "maxLength", "Value must be no greater than $1 characters"));
        $this->addValidator(new RangeFieldValidator(RangeFieldValidator::MODE_MIN, "min", "Value must be at least $1"));
        $this->addValidator(new RangeFieldValidator(RangeFieldValidator::MODE_MAX, "max", "Value must be no greater than $1"));
        $this->addValidator(new RangeFieldValidator(RangeFieldValidator::MODE_RANGE, "range", "Value must be between $1 and $2"));

        $this->addValidator(new RegexpFieldValidator("[0-9]*", "numeric", "Value must be numeric"));
        $this->addValidator(new RegexpFieldValidator("[a-zA-Z0-9]*", "alphanumeric", "Value must be alphanumeric"));
        $this->addValidator(new RegexpFieldValidator("[a-zA-Z0-9 \-']*", "name", "Value must be a valid name"));
        $this->addValidator(new RegexpFieldValidator("(?:[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*|\"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*\")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])", "email", "Value must be a valid email"));
        $this->addValidator(new DateFieldValidator("date", "Value must be a date in $1 format"));
    }


    /**
     * Add a field validator by key
     *
     * @param string $key
     * @param ObjectFieldValidator $fieldValidator
     */
    public function addValidator($fieldValidator) {
        $key = $fieldValidator->getValidatorKey();
        $this->validators[$key] = $fieldValidator;
    }


    /**
     * Get a validator by key
     *
     * @param $key
     * @return ObjectFieldValidator|null
     */
    public function getValidator($key) {
        return $this->validators[$key] ?? null;
    }

    /**
     * Validate an object using markup attributes.
     *
     * @param mixed $object
     */
    public function validateObject($object) {

        // Get a class inspector instance for this object.
        $classInspector = $this->classInspectorProvider->getClassInspector(get_class($object));

        // Look up field annotations of type validation.
        $classAnnotations = $classInspector->getClassAnnotationsObject();
        $validationFields = $classAnnotations->getFieldAnnotations();

        $validationErrors = array();

        foreach ($validationFields as $field => $annotations) {

            $value = $classInspector->getProperties()[$field]->get($object);

            foreach ($annotations as $annotation) {

                $validatorKey = $annotation[0]->getLabel();
                $validatorArgs = $annotation[0]->getValue() ? explode(",", $annotation[0]->getValue()) : [];

                $validator = isset($this->validators[$validatorKey]) ? $this->validators[$validatorKey] : null;
                if (isset($validator)) {
                    $valid = $validator->validateObjectFieldValue($value, $field, $object, $validatorArgs);

                    if ($valid !== true) {
                        if (!isset($validationErrors[$field])) $validationErrors[$field] = array();
                        $message = $validator->getEvaluatedValidationMessage($validatorArgs);
                        $validationErrors[$field][$validatorKey] = new FieldValidationError($field, $validatorKey, $message);
                    }

                }

            }

            // If the value is an object, recursively validate.
            if (is_object($value)) {
                $subValidationErrors = $this->validateObject($value);
                if ($subValidationErrors) {
                    if (!isset($validationErrors[$field])) $validationErrors[$field] = array();
                    $validationErrors[$field] = array_merge($validationErrors[$field], $subValidationErrors);
                }
            }

            // if the value is an array, recursively validate
            if (is_array($value)) {
                $validationErrorArray = [];
                foreach ($value as $index => $valueEntry) {
                    if (is_object($valueEntry)) {
                        $subValidationErrors = $this->validateObject($valueEntry);
                        if ($subValidationErrors) {
                            $validationErrorArray[$index] = $subValidationErrors;
                        }
                    }
                }

                if (sizeof($validationErrorArray)) {
                    if (!isset($validationErrors[$field]))
                        $validationErrors[$field] = array();

                    $validationErrors[$field] = array_merge($validationErrors[$field], $validationErrorArray);
                }

            }


        }

        // Also if a validate method exists on the object, execute that now and merge any
        // validation errors in.
        if (isset($classInspector->getPublicMethods()["validate"])) {
            $customValidation = $object->validate();
            if (sizeof($customValidation)) {
                $validationErrors = array_merge($validationErrors, $customValidation);
            }
        }

        return $validationErrors;


    }


}
