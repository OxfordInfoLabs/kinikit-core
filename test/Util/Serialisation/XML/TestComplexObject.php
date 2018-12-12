<?php

namespace Kinikit\Core\Util\Serialisation\XML;
use Kinikit\Core\Object\SerialisableObject;

/**
 * Complex object, contains instances of the test objects
 *
 */
class TestComplexObject extends SerialisableObject {
	
	private $details;
	private $addresses;
	private $jobDetails;
	
	public function __construct($details = null, $addresses = array(), $jobDetails = null) {
		$this->details = $details;
		$this->addresses = $addresses;
		$this->jobDetails = $jobDetails;
	}
	
	public function getDetails() {
		return $this->details;
	}
	
	public function getAddresses() {
		return $this->addresses;
	}
	
	public function getJobDetails() {
		return $this->jobDetails;
	}
	
	public function setDetails($details) {
		$this->details = $details;
	}
	
	public function setAddresses($addresses) {
		$this->addresses = $addresses;
	}
	
	public function setJobDetails($jobDetails) {
		$this->jobDetails = $jobDetails;
	}

}

?>