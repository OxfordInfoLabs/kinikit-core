<?php

namespace Kinikit\Core\Object;

class SerialisableWithNumbersInProperties extends SerialisableObject {
	
	private $regularProperty;
	private $m4Property;
	private $another5Property;
	
	public function __construct($regularProperty = null, $m4Property = null, $another5Property = null) {
		$this->regularProperty = $regularProperty;
		$this->m4Property = $m4Property;
		$this->another5Property = $another5Property;
	}
	
	/**
	 * @return the $regularProperty
	 */
	public function getRegularProperty() {
		return $this->regularProperty;
	}
	
	/**
	 * @return the $m4Property
	 */
	public function getM4Property() {
		return $this->m4Property;
	}
	
	/**
	 * @return the $another5Property
	 */
	public function getAnother5Property() {
		return $this->another5Property;
	}
	
	/**
	 * @param field_type $regularProperty
	 */
	public function setRegularProperty($regularProperty) {
		$this->regularProperty = $regularProperty;
	}
	
	/**
	 * @param field_type $m4Property
	 */
	public function setM4Property($m4Property) {
		$this->m4Property = $m4Property;
	}
	
	/**
	 * @param field_type $another5Property
	 */
	public function setAnother5Property($another5Property) {
		$this->another5Property = $another5Property;
	}

}

?>