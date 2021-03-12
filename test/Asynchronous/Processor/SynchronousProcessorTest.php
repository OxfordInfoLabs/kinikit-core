<?php


namespace Kinikit\Core\Asynchronous\Processor;

use Kinikit\Core\Asynchronous\Asynchronous;
use Kinikit\Core\Asynchronous\TestAsynchronous;
use Kinikit\Core\DependencyInjection\Container;

include "autoloader.php";

class SynchronousProcessorTest extends \PHPUnit\Framework\TestCase {

    /**
     * @var SynchronousProcessor
     */
    private $processor;


    /**
     * Set up function
     */
    public function setUp(): void {
        $this->processor = Container::instance()->get(SynchronousProcessor::class);
    }

    public function testExecuteAndWaitCorrectlyWaitsForAllBackgroundThreadsToExecuteAndUpdatesDataAndStatus() {

        $asynchronous1 = new TestAsynchronous("Mary");
        $asynchronous2 = new TestAsynchronous("Mark");
        $asynchronous3 = new TestAsynchronous("James");

        // Execute and wait
        $this->processor->executeAndWait([$asynchronous1, $asynchronous2, $asynchronous3]);

        // Check that the status and data is all updated
        $this->assertEquals("Mary", $asynchronous1->getName());
        $this->assertEquals("Evaluated: Mary", $asynchronous1->getEvaluatedProperty());
        $this->assertEquals("Returned: Mary", $asynchronous1->getReturnValue());
        $this->assertEquals(Asynchronous::STATUS_COMPLETED, $asynchronous1->getStatus());

        $this->assertEquals("Mark", $asynchronous2->getName());
        $this->assertEquals("Evaluated: Mark", $asynchronous2->getEvaluatedProperty());
        $this->assertEquals("Returned: Mark", $asynchronous2->getReturnValue());
        $this->assertEquals(Asynchronous::STATUS_COMPLETED, $asynchronous2->getStatus());

        $this->assertEquals("James", $asynchronous3->getName());
        $this->assertEquals("Evaluated: James", $asynchronous3->getEvaluatedProperty());
        $this->assertEquals("Returned: James", $asynchronous3->getReturnValue());
        $this->assertEquals(Asynchronous::STATUS_COMPLETED, $asynchronous3->getStatus());

    }

    public function testExecuteAndWaitCorrectlyWaitsForAllBackgroundThreadsToExecuteAndCapturesExceptionsOnFailure() {

        $asynchronous1 = new TestAsynchronous("Mark");
        $asynchronous2 = new TestAsynchronous("FAIL");
        $asynchronous3 = new TestAsynchronous("James");

        // Execute and wait
        $this->processor->executeAndWait([$asynchronous1, $asynchronous2, $asynchronous3]);

        // Check that the status and data is all updated
        $this->assertEquals("Mark", $asynchronous1->getName());
        $this->assertEquals("Evaluated: Mark", $asynchronous1->getEvaluatedProperty());
        $this->assertEquals("Returned: Mark", $asynchronous1->getReturnValue());
        $this->assertEquals(Asynchronous::STATUS_COMPLETED, $asynchronous1->getStatus());

        $this->assertEquals("FAIL", $asynchronous2->getName());
        $this->assertNull($asynchronous2->getEvaluatedProperty());
        $this->assertNull($asynchronous2->getReturnValue());
        $this->assertEquals("Failed", $asynchronous2->getExceptionData()["message"]);
        $this->assertEquals(Asynchronous::STATUS_FAILED, $asynchronous2->getStatus());

        $this->assertEquals("James", $asynchronous3->getName());
        $this->assertEquals("Evaluated: James", $asynchronous3->getEvaluatedProperty());
        $this->assertEquals("Returned: James", $asynchronous3->getReturnValue());
        $this->assertEquals(Asynchronous::STATUS_COMPLETED, $asynchronous3->getStatus());

    }
}