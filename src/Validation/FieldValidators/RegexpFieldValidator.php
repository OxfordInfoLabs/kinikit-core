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

    public function __construct($regexp = null, $validationMessage = null) {
        $this->regexp = $regexp;
        parent::__construct($validationMessage);
    }

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

        if (!$value) return true;

        if ($this->regexp) {
            $regexp = $this->regexp;
        } else {
            if (sizeof($validatorParams) < 1) {
                throw new MisconfiguredValidatorException($validatorKey, $fieldName, $targetObject);
            }

            $regexp = $validatorParams[0];

        }

        return preg_match("/^" . $regexp . "$/", $value) == 1;

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
