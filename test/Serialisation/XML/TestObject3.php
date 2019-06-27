<?php

namespace Kinikit\Core\Serialisation\XML;
use Kinikit\Core\Object\SerialisableObject;

/**
 * Test object with private fields and protected setters and getters
 *
 */
class TestObject3 extends SerialisableObject {
	
	private $profession;
	private $title;
	private $salary;
	
	public function __construct($profession = null, $title = null, $salary = null){
		$this->profession = $profession;
		$this->title = $title;
		$this->salary = $salary; 
	}
	
	protected function getProfession(){
		return $this->profession;
	}
	
	protected function getTitle(){
		return $this->title;
	}
	
	protected function getSalary(){
		return $this->salary;
	}
	
	protected function setProfession($profession){
		$this->profession = $profession;
	}
	
	protected function setTitle($title){
		$this->title = $title;
	}
	
	protected function setSalary($salary){
		$this->salary = $salary;
	}
	
	
	public function toString(){
		return $profession.",".$title.",".$salary;
	}
	
}



?>
