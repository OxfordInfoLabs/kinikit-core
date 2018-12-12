<?php

namespace Kinikit\Core\Exception;

/**
 * Generic size mismatch exception raised if two objects are not the same size.
 * 
 * @author mark
 *
 */
class SizeMismatchException extends \Exception {
	
	/**
	 * Construct the exception with two descriptions for information purposes.
	 * 
	 * @param string $firstObjectDescription
	 * @param string $secondObjectDescription
	 */
	public function __construct($firstObjectDescription, $secondObjectDescription) {
		parent::__construct ( "Unexpected size mismatch between two objects '" . $firstObjectDescription . "' and '" . $secondObjectDescription . "' which should be of equal size." );
	}

}

?>