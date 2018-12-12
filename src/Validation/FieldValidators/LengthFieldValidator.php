<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/08/2018
 * Time: 11:09
 */

namespace Kinikit\Core\Validation\FieldValidators;


use Kinikit\Core\Object\SerialisableObject;

class LengthFieldValidator extends ObjectFieldValidator {

    private $mode;

    const MODE_MIN = "MIN";
    const MODE_MAX = "MAX";
    const MODE_RANGE = "RANGE";

    public function __construct($mode = null, $validationMessage = null) {
        $this->mode = $mode;
        parent::__construct($validationMessage);
    }


    /**
     * Validate a range object
     *
     * @param string $value
     * @param $fieldName
     * @param SerialisableObject $targetObject
     * @param array $validatorParams
     * @param $validatorKey
     * @return bool|mixed
     * @throws MisconfiguredValidatorException
     */
    public function validateObjectFieldValue($value, $fieldName, $targetObject, &$validatorParams, $validatorKey) {

        if (sizeof($validatorParams) < ($this->mode == self::MODE_RANGE ? 2 : 1)) {
            throw new MisconfiguredValidatorException($validatorKey, $fieldName, $targetObject);
        }

        if (!$value) return true;

        switch ($this->mode) {
            case self::MODE_MIN:
                return strlen($value) >= $validatorParams[0];
            case self::MODE_MAX:
                return strlen($value) <= $validatorParams[0];
            case self::MODE_RANGE:
                return strlen($value) <= $validatorParams[1] && strlen($value) >= $validatorParams[0];
        }

    }

}