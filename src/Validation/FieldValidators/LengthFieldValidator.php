<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/08/2018
 * Time: 11:09
 */

namespace Kinikit\Core\Validation\FieldValidators;


use Kinikit\Core\Validation\MisconfiguredValidatorException;

class LengthFieldValidator extends ObjectFieldValidator {

    private $mode;

    const MODE_MIN = "MIN";
    const MODE_MAX = "MAX";
    const MODE_RANGE = "RANGE";

    public function __construct($mode = null, $validatorKey = null, $validationMessage = null) {
        $this->mode = $mode;
        parent::__construct($validatorKey, $validationMessage);
    }


    /**
     * Validate a length value - depending upon the mode of this validator
     * there will be one or 2 boundaries supplied.
     *
     * @param $value
     * @param float $boundary1
     * @param float $boundary2
     */
    public function validate($value, $boundary1, $boundary2 = null) {

        if (!$value) return true;

        switch ($this->mode) {
            case self::MODE_MIN:
                return strlen($value) >= $boundary1;
            case self::MODE_MAX:
                return strlen($value) <= $boundary1;
            case self::MODE_RANGE:
                return strlen($value) <= $boundary2 && strlen($value) >= $boundary1;
        }


    }


    /**
     * Validate a range object
     *
     * @param string $value
     * @param $fieldName
     * @param SerialisableObject $targetObject
     * @param array $validatorParams
     * @return bool|mixed
     * @throws MisconfiguredValidatorException
     */
    public function validateObjectFieldValue($value, $fieldName, $targetObject, &$validatorParams) {

        if (count($validatorParams) < ($this->mode == self::MODE_RANGE ? 2 : 1)) {
            throw new MisconfiguredValidatorException($this->getValidatorKey(), $fieldName, $targetObject);
        }

        return $this->validate($value, $validatorParams[0], $validatorParams[1] ?? null);
    }

}
