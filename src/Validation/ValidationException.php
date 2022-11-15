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

        parent::__construct("The following validation errors occurred: " . join("<br>", $validationErrorMessages));
    }

    /**
     * @return the $exceptionArray
     */
    public function getValidationErrors() {
        return $this->validationErrors;
    }


    // Generate validation messages from a message array
    private function generateValidationMessages($messageArray, $parentKey = "") {
        $messages = [];
        if (is_array($messageArray)) {
            foreach ($messageArray as $key => $items) {

                // Check for submessages at this level
                $subMessages = [];
                if (is_array($items)) {
                    foreach ($items as $subItemKey => $subItem) {
                        if ($subItem instanceof FieldValidationError) {
                            $subMessages[] = $subItem->getErrorMessage();
                        }
                    }
                }
                if (sizeof($subMessages) > 0) {
                    $messages[] = $parentKey . $key . ": " . join(", ", $subMessages);
                } else {
                    $messages = array_merge($messages, $this->generateValidationMessages($items, $key . "->"));
                }
            }
        }

        return $messages;
    }

}

?>
