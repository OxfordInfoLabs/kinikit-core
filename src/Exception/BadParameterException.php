<?php

namespace Kinikit\Core\Exception;

/**
 * General exception thrown if a bad parameter is passed to a constructor or function call.
 *
 */
class BadParameterException extends \Exception {
	
	public function __construct($functionName, $parameter, $badValue) {
		if (is_object ( $badValue ))
			$badValue = get_class ( $badValue );
		parent::__construct ( "Bad Value '$badValue' for parameter '$parameter' Passed to '$functionName'" );
	}

}

?>