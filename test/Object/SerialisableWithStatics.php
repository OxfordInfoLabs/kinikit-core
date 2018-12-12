<?php

namespace Kinikit\Core\Object;

/**
 * Test object with static properties and methods
 * 
 * @author mark
 *
 */
class SerialisableWithStatics extends SerialisableObject {
	
	private $id;
	private $name;
	
	private static $age;
	protected static $shoeSize;
	
	public function __construct($id = null, $name = null, $age = null, $shoeSize = null) {
		$this->id = $id;
		$this->name = $name;
		SerialisableWithStatics::$age = $age;
		SerialisableWithStatics::$shoeSize = $shoeSize;
	}
	
	/**
	 * @return the $id
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return the $name
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * @return the $age
	 */
	public static function getAge() {
		return SerialisableWithStatics::$age;
	}
	
	/**
	 * @param $id the $id to set
	 */
	public function setId($id) {
		$this->id = $id;
	}
	
	/**
	 * @param $name the $name to set
	 */
	public function setName($name) {
		$this->name = $name;
	}
	
	/**
	 * @param $age the $age to set
	 */
	public static function setAge($age) {
		SerialisableWithStatics::$age = $age;
	}

}

?>