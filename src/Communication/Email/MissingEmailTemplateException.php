<?php


namespace Kinikit\Core\Communication\Email;


use Kinikit\Core\Exception\ItemNotFoundException;

class MissingEmailTemplateException extends ItemNotFoundException {

    public function __construct($templateName) {
        parent::__construct("The email template with name $templateName cannot be found");
    }

}
