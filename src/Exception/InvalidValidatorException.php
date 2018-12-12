<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/08/2018
 * Time: 12:06
 */

namespace Kinikit\Core\Exception;


class InvalidValidatorException extends \Exception {

    public function __construct($validatorKey, $field, $object) {
        $className = get_class($object);
        parent::__construct("The validator defined on field $field on class $className with key $validatorKey does not exist");
    }

}