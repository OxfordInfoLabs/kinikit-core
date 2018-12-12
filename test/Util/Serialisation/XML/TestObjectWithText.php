<?php

namespace Kinikit\Core\Util\Serialisation\XML;


use Kinikit\Core\Object\SerialisableObject;

class TestObjectWithText extends SerialisableObject {

	private $text;
	private $otherMember;
	
	
	public function __construct($text){
		$this->text = $text;
	}
	
	public function getText(){
		return $this->text;
	}
	
	
	public function setText($text){
		$this->text = $text;	
	}
	
	public function getOtherMember(){
		return $this->otherMember;
	}


}


?>