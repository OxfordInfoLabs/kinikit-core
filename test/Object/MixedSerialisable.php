<?php

namespace Kinikit\Core\Object;

class MixedSerialisable extends SerialisableObject {
	
	private $petType;
	private $petName;
	protected $petAge;
	protected $petSex;
	protected $petOwner;
	public $birthDate;
	public $birthPlace;
	public $mothersName;
	
	public function __construct($petType = null, $petName = null, $petAge = null, $petSex = null, $petOwner = null, $birthDate = null, $birthPlace = null, $mothersName = null) {
		$this->petType = $petType;
		$this->petName = $petName;
		$this->petAge = $petAge;
		$this->petSex = $petSex;
		$this->petOwner = $petOwner;
		$this->birthDate = $birthDate;
		$this->birthPlace = $birthPlace;
		$this->mothersName = $mothersName;
	}
	
	public function setPetName($petName) {
		$this->petName = "PetName:" . $petName;
	}
	
	public function getPetName() {
		return "PetName:" . $this->petName;
	}
	
	protected function setPetSex($petSex) {
		$this->petSex = "PetSex:" . $petSex;
	}
	
	protected function getPetSex() {
		return "PetSex:" . $this->petSex;
	}
	
	public function setPetOwner($petOwner) {
		$this->petOwner = "PetOwner:" . $petOwner;
	}
	
	public function getPetOwner() {
		return "PetOwner:" . $this->petOwner;
	}
	
	protected function setBirthPlace($birthPlace) {
		$this->birthPlace = "BirthPlace:" . $birthPlace;
	}
	
	protected function getBirthPlace() {
		return "BirthPlace:" . $this->birthPlace;
	}
	
	public function setMothersName($mothersName) {
		$this->mothersName = "MothersName:" . $mothersName;
	}
	
	public function getMothersName() {
		return "MothersName:" . $this->mothersName;
	}
	
	/**
	 * Return a logical toString
	 */
	public function toString() {
		return join ( array ($this->petType, $this->petName, $this->petAge, $this->petSex, $this->petOwner, $this->birthDate, $this->birthPlace, $this->mothersName ), "," );
	}

}

?>