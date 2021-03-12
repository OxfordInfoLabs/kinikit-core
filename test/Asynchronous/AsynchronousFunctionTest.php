<?php


namespace Kinikit\Core\Asynchronous;

include "autoloader.php";

class AsynchronousFunctionTest extends \PHPUnit\Framework\TestCase {


    private $name = "Mark";

    public function testAsynchronousFunctionRunsTheFunctionAndAttachesValue() {

        $asynchronousFunction = new AsynchronousFunction(function () {
            return "I am a function";
        });

        $this->assertEquals("I am a function", $asynchronousFunction->run());


        $asynchronousFunction = new AsynchronousFunction(function () {
            throw new \Exception("Bad Call");
        });

        try {
            $asynchronousFunction->run();
            $this->fail("Should have thrown here");
        } catch (\Exception $e) {
            $this->assertEquals(new \Exception("Bad Call"), $e);
        }


    }


    public function testCanConstructWithThisObject() {

        $asynchronousFunction = new AsynchronousFunction(function () {
            return $this->name;
        }, $this);

        $this->assertEquals("Mark", $asynchronousFunction->run());


    }
}