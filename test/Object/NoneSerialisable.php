<?php

namespace Kinikit\Core\Object;

class NoneSerialisable {
	
	private $test;
	private $monkey;
	
	/**
	 * @return the $test
	 */
	public function getTest() {
		return $this->test;
	}
	
	/**
	 * @return the $monkey
	 */
	public function getMonkey() {
		return $this->monkey;
	}
	
	/**
	 * @param $test the $test to set
	 */
	public function setTest($test) {
		$this->test = $test;
	}
	
	/**
	 * @param $monkey the $monkey to set
	 */
	public function setMonkey($monkey) {
		$this->monkey = $monkey;
	}

}

?>