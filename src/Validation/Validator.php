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

        $this->validators["required"] = new RequiredFieldValidator("This field is required");
        $this->validators["regexp"] = new RegexpFieldValidator(null, "Value does not match the required format");
        $this->validators["equals"] = new EqualsFieldValidator($classInspectorProvider, "Value does not match the $1 field");
        $this->validators["minLength"] = new LengthFieldValidator(LengthFieldValidator::MODE_MIN, "Value must be at least $1 characters");
        $this->validators["maxLength"] = new LengthFieldValidator(LengthFieldValidator::MODE_MAX, "Value must be no greater than $1 characters");
        $this->validators["min"] = new RangeFieldValidator(RangeFieldValidator::MODE_MIN, "Value must be at least $1");
        $this->validators["max"] = new RangeFieldValidator(RangeFieldValidator::MODE_MAX, "Value must be no greater than $1");
        $this->validators["range"] = new RangeFieldValidator(RangeFieldValidator::MODE_RANGE, "Value must be between $1 and $2");

        $this->validators["numeric"] = new RegexpFieldValidator("[0-9]*", "Value must be numeric");
        $this->validators["alphanumeric"] = new RegexpFieldValidator("[a-zA-Z0-9]*", "Value must be alphanumeric");
        $this->validators["name"] = new RegexpFieldValidator("[a-zA-Z0-9 \-']*", "Value must be a valid name");
        $this->validators["email"] = new RegexpFieldValidator("(?:[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*|\"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*\")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])", "Value must be a valid email");
        $this->validators["date"] = new DateFieldValidator("Value must be a date in $1 format");
    }


    /**
     * Add a field validator by key
     *
     * @param string $key
     * @param ObjectFieldValidator $fieldValidator
     */
    public function addValidator($key, $fieldValidator) {
        $this->validators[$key] = $fieldValidator;
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
                    $valid = $validator->validateObjectFieldValue($value, $field, $object, $validatorArgs, $validatorKey);

                    if ($valid !== true) {
                        if (!isset($validationErrors[$field])) $validationErrors[$field] = array();
                        $message = preg_replace_callback("/\\$([0-9])/", function ($matches) use ($validatorArgs) {
                            return $validatorArgs[$matches[1] - 1];
                        }, $validator->getValidationMessage());
                        $validationErrors[$field][$validatorKey] = new FieldValidationError($field, $validatorKey, $message);
                    }

                }

            }

        }

        return $validationErrors;


    }


}
