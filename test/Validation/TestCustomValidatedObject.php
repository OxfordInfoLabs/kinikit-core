<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/08/2018
 * Time: 17:17
 */

namespace Kinikit\Core\Validation;



class TestCustomValidatedObject  {

    /**
     * @required
     * @macaroni
     */
    private $customField;


    public function getCustomField() {
        return $this->customField;
    }

    /**
     * @param mixed $customField
     */
    public function setCustomField($customField) {
        $this->customField = $customField;
    }


}
