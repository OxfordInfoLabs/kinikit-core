<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/08/2018
 * Time: 10:26
 */

namespace Kinikit\Core\Validation\FieldValidators;


class RequiredFieldValidator extends ObjectFieldValidator {


    /**
     * Validate a value
     *
     * @param $value
     */
    public function validate($value){
        return $value ? true : false;
    }

    /**
     * Validate required field.
     *
     * @param string $value
     * @param $fieldName
     * @param SerialisableObject $targetObject
     * @param array $validatorParams
     * @return bool|string
     */
    public function validateObjectFieldValue($value, $fieldName, $targetObject, &$validatorParams) {
        return $this->validate($value);
    }

}
