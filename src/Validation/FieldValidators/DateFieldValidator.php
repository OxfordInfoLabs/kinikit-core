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
     * Validate the date field against the passed format (default to british date).
     *
     *
     * @param string $value
     * @param string $dateFormat
     */
    public function validate($value, $dateFormat = null) {

        if (!$value) return true;

        if ($dateFormat) {

            switch ($dateFormat) {

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
                    $format = $dateFormat;
                    break;
            }


        } else {
            $format = "d/m/Y";
        }

        return date_create_from_format($format, $value) ? true : false;

    }


    /**
     * Validate a value
     *
     * @param $value string
     * @param $fieldName
     * @param $targetObject SerialisableObject
     * @param $validatorParams array
     * @return mixed
     */
    public function validateObjectFieldValue($value, $fieldName, $targetObject, &$validatorParams) {

        // Synchronise validator params if not set
        if (!$validatorParams[0]) {
            $validatorParams[0] = "d/m/Y";
        }

        return $this->validate($value, $validatorParams[0] ?? null);
    }
}
