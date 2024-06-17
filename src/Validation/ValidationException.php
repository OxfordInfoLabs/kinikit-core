<?php

namespace Kinikit\Core\Validation;

/**
 * Generic validation exception which accepts an array of problems keyed in by a field key.
 *
 * @author oxil
 *
 */
class ValidationException extends \Exception {

    private $validationErrors;

    /**
     * Construct with an array of validation errors.
     * and a seperator which defaults to html new line.
     *
     * @param array $validationErrors
     */
    public function __construct($validationErrors) {
        $this->validationErrors = $validationErrors;

        // Create a message appropriately.
        $validationErrorMessages = $this->generateValidationMessages($this->validationErrors);

        parent::__construct("The following validation errors occurred: \n" . $validationErrorMessages);
    }

    /**
     * @return the $exceptionArray
     */
    public function getValidationErrors() {
        return $this->validationErrors;
    }


    // Generate validation messages from a message array
    private function generateValidationMessages($messageArray, $parentKey = "Errors") {
        $out = "";
        if (is_array($messageArray)){
            foreach ($messageArray as $key => $value){ // Expand out children
                $out .= $this->generateValidationMessages($value, $parentKey ."->". $key) . "\n";
            }
        } else {
            if ($messageArray instanceof FieldValidationError){
                return $parentKey . ": " . $messageArray->getErrorMessage();
            }
        }
        return $out;

    }

}
