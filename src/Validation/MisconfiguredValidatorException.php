<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/08/2018
 * Time: 10:41
 */

namespace Kinikit\Core\Validation;


/**
 * Misconfigured validator exception
 *
 * Class MisconfiguredValidatorException
 * @package Kinikit\Core\Exception
 */
class MisconfiguredValidatorException extends \Exception {

    /**
     * MisconfiguredValidatorException constructor.
     * @param string $validatorKey
     * @param string $fieldName
     * @param SerialisableObject $targetObject
     */
    public function __construct($validatorKey, $fieldName, $targetObject) {
        parent::__construct("Not enough parameters passed to validator of type " . $validatorKey . " for field " . $fieldName . " on class " . get_class($targetObject));
    }

}
