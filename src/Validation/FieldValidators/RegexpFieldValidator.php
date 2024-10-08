<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/08/2018
 * Time: 11:17
 */

namespace Kinikit\Core\Validation\FieldValidators;


use Kinikit\Core\Validation\MisconfiguredValidatorException;

class RegexpFieldValidator extends ObjectFieldValidator {


    private $regexp;

    public function __construct($regexp = null, $validatorKey = null, $validationMessage = null) {
        $this->regexp = $regexp;
        parent::__construct($validatorKey, $validationMessage);
    }


    /**
     * Validate the value using either the constructed or explicit regexp
     *
     * @param $value
     * @param null $regexp
     * @return bool
     */
    public function validate($value, $regexp = null) {
        return preg_match("/^" . ($this->regexp ? $this->regexp : $regexp) . "$/", $value) == 1;
    }


    /**
     * Validate a value
     *
     * @param $value string
     * @param $fieldName
     * @param $targetObject SerialisableObject
     * @param $validatorParams array
     * @return mixed
     * @throws MisconfiguredValidatorException
     */
    public function validateObjectFieldValue($value, $fieldName, $targetObject, &$validatorParams) {

        if (!$value) return true;

        if ($this->regexp) {
            $regexp = $this->regexp;
        } else {
            if (count($validatorParams) < 1) {
                throw new MisconfiguredValidatorException($this->getValidatorKey(), $fieldName, $targetObject);
            }

            $regexp = $validatorParams[0];

        }

        return $this->validate($value, $regexp);

    }

    /**
     * @return null
     */
    public function getRegexp() {
        return $this->regexp;
    }

    /**
     * @param null $regexp
     */
    public function setRegexp($regexp) {
        $this->regexp = $regexp;
    }


}
