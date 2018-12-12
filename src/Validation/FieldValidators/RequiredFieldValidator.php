<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/08/2018
 * Time: 10:26
 */

namespace Kinikit\Core\Validation\FieldValidators;


use Kinikit\Core\Object\SerialisableObject;

class RequiredFieldValidator extends ObjectFieldValidator {


    /**
     * Validate required field.
     *
     * @param string $value
     * @param $fieldName
     * @param SerialisableObject $targetObject
     * @param array $validatorParams
     * @param $validatorKey
     * @return bool|string
     */
    public function validateObjectFieldValue($value, $fieldName, $targetObject, &$validatorParams, $validatorKey) {
        return $value ? true : false;
    }

}