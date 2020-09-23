<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 16/08/2018
 * Time: 17:10
 */

namespace Kinikit\Core\Validation\FieldValidators;


abstract class ObjectFieldValidator {

    /**
     * @var string
     */
    private $validatorKey;


    /**
     * @var string
     */
    private $validationMessage;


    public function __construct($validatorKey, $validationMessage = null) {
        $this->validatorKey = $validatorKey;
        $this->validationMessage = $validationMessage;
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
    public abstract function validateObjectFieldValue($value, $fieldName, $targetObject, &$validatorParams);

    /**
     * @return string
     */
    public function getValidatorKey() {
        return $this->validatorKey;
    }


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


    /**
     * Get the evaluated validation message with placeholder arguments replaced with
     * passed values
     *
     * @param $placeholderArguments
     * @return string|string[]|null
     */
    public function getEvaluatedValidationMessage($placeholderArguments = []) {

        // Replace indexed params
        $result = preg_replace_callback("/\\$([0-9])/", function ($matches) use ($placeholderArguments) {
            return $placeholderArguments[$matches[1] - 1];
        }, $this->getValidationMessage());


        // Replace all
        $result = str_replace('$ALL', join(", ", $placeholderArguments), $result);

        return $result;

    }


}
