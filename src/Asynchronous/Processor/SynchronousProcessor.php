<?php


namespace Kinikit\Core\Asynchronous\Processor;

use Kinikit\Core\Asynchronous\Asynchronous;
use Kinikit\Core\Binding\ObjectBinder;
use Kinikit\Core\Reflection\ClassInspectorProvider;

/**
 * Synchronous implementation for the asynchronous processor - useful for testing and benchmarking
 *
 * Class SynchronousProcessor
 *
 * @package Kinikit\Core\Asynchronous\Processor
 */
class SynchronousProcessor implements AsynchronousProcessor {

    /**
     * @var ObjectBinder
     */
    private $objectBinder;

    /**
     * @var ClassInspectorProvider
     */
    private $classInspectorProvider;


    /**
     * AsynchronousProcessor constructor.
     *
     * @param ObjectBinder $objectBinder
     * @param ClassInspectorProvider $classInspectorProvider
     */
    public function __construct($objectBinder, $classInspectorProvider) {
        $this->objectBinder = $objectBinder;
        $this->classInspectorProvider = $classInspectorProvider;
    }


    /**
     * Execute and wait
     *
     * @param Asynchronous[] $asynchronousInstances
     * @return Asynchronous[]
     */
    public function executeAndWait($asynchronousInstances) {

        foreach ($asynchronousInstances as $instance) {

            $classInspector = $this->classInspectorProvider->getClassInspector(get_class($instance));


            try {
                // Run the instance
                $returnValue = $instance->run();

                // Update the status and return value
                $classInspector->setPropertyData($instance, Asynchronous::STATUS_COMPLETED, "status", false);
                $classInspector->setPropertyData($instance, $returnValue, "returnValue", false);

            } catch (\Exception $e) {

                $exceptionArray = $this->objectBinder->bindToArray($e);

                if (is_array($exceptionArray)) {
                    unset($exceptionArray["file"]);
                    unset($exceptionArray["line"]);
                    unset($exceptionArray["previous"]);
                    unset($exceptionArray["trace"]);
                    unset($exceptionArray["traceAsString"]);
                }


                // Update status and exception
                $classInspector->setPropertyData($instance, Asynchronous::STATUS_FAILED, "status", false);
                $classInspector->setPropertyData($instance, $exceptionArray, "exceptionData", false);

            }
        }

        return $asynchronousInstances;
    }
}