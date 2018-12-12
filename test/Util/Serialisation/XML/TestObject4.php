<?php

namespace Kinikit\Core\Util\Serialisation\XML;

use Kinikit\Core\Object\SerialisableObject;

class TestObject4 extends SerialisableObject {

	protected $street;
	protected $city;
	protected $county;
	protected $text;
	
	public function __construct($street = null, $city = null, $county = null, $text = null){
		$this->street = $street;
		$this->city = $city;
		$this->county = $county;
		$this->text = $text;
	}


}


?>