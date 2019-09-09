<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/08/2018
 * Time: 17:21
 */

namespace Kinikit\Core\Validation\FieldValidators;


use Kinikit\Core\Object\SerialisableObject;

class DateFieldValidator extends ObjectFieldValidator {

    /**
     * Validate a value
     *
     * @param $value string
     * @param $fieldName
     * @param $targetObject SerialisableObject
     * @param $validatorParams array
     * @param $validatorKey
     * @return mixed
     */
    public function validateObjectFieldValue($value, $fieldName, $targetObject, &$validatorParams, $validatorKey) {

        if (!$value) return true;

        if (sizeof($validatorParams) > 0) {

            switch ($validatorParams[0]) {

                case "britishdate":
                    $format = "d/m/Y";
                    break;
                case "britishdatetime":
                    $format = "d/m/Y H:i:s";
                    break;
                case "time":
                    $format = "H:i:s";
                    break;
                case "sqldate":
                    $format = "Y-m-d";
                    break;
                case "sqldatetime":
                    $format = "Y-m-d H:i:s";
                    break;
                default:
                    $format = $validatorParams[0];
                    break;
            }


        } else {
            $validatorParams[0] = $format = "d/m/Y";
        }

        return date_create_from_format($format, $value) ? true : false;


    }
}
