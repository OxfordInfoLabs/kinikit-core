<?php

namespace Kinikit\Core\Util;

use Kinikit\Core\Exception\ClassNotConstructableException;
use Kinikit\Core\Exception\ClassNotFoundException;
use Kinikit\Core\Exception\ClassNotSerialisableException;

include_once "autoloader.php";

/**
 * Test cases for the class utils helper.
 * 
 * @author mark
 *
 */
class ClassUtilsTest extends \PHPUnit\Framework\TestCase {
	
	public function testIfAttemptingToCreateNewInstanceOfClassWhichDoesNotExistClassNotFoundExceptionIsRaised() {
        self::assertTrue(true);
		try {
			ClassUtils::createNewClassInstance ( "BernardMatthews" );
			$this->fail ( "Should have thrown here" );
		} catch ( ClassNotFoundException $e ) {
			// Success
		}
		
		try {
			ClassUtils::createNewClassInstance ( "BobBuilder" );
			$this->fail ( "Should have thrown here" );
		} catch ( ClassNotFoundException $e ) {
			// Success
		}
	
	}
	
	public function testIfAttemptingToCreateNewInstanceOfClassWhichHasNoBlankConstructorExceptionIsRaised() {
        self::assertTrue(true);
	    try {
			ClassUtils::createNewClassInstance ( "\Kinikit\Core\Util\NoBlankConstructor" );
			$this->fail ( "Should have thrown here" );
		} catch ( ClassNotConstructableException $e ) {
			// Success
		}
	}
	
	public function testIfAttemptingToCreateValidInstanceANewInstanceIsReturned() {
		
		$this->assertEquals ( new PerfectClass (), ClassUtils::createNewClassInstance ( "Kinikit\Core\Util\PerfectClass" ) );
	
	}
	
	public function testIfLocationSuppliedForNoneIncludedClassItIsIncluded() {
		
		// Check that exception raised initially
		try {
			ClassUtils::createNewClassInstance ( "NotIncludedClass" );
			$this->fail ( "Should have thrown here" );
		} catch ( ClassNotFoundException $e ) {
			// Success
		}
		
		// Now check that the class is included and created if location is supplied
		$this->assertTrue ( ClassUtils::createNewClassInstance ( "Kinikit\Core\Util\NotIncludedClass", "common/util" ) instanceof NotIncludedClass );
	
	}
	
	public function testIfRequireSerialisableFlagPassedExceptionIsRaisedIfClassIsNotSerialisable() {
        self::assertTrue(true);
	    try {
			ClassUtils::createNewClassInstance ( "Kinikit\Core\Util\PerfectClass", null, true );
			$this->fail ( "Should have thrown here" );
		} catch ( ClassNotSerialisableException $e ) {
			// Success
		}
	
	}

}

?>