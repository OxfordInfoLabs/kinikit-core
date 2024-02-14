<?php

namespace Kinikit\Core\Asynchronous\Processor;

use Amp\Future;
use Kinikit\Core\Asynchronous\AMPParallel\AMPParallelTask;
use Kinikit\Core\Asynchronous\Asynchronous;
use Kinikit\Core\Reflection\ClassInspectorProvider;
use function Amp\Future\await;
use function Amp\Parallel\Worker\submit;

class AMPParallelAsynchronousProcessor implements AsynchronousProcessor {

    /**
     * @var ClassInspectorProvider
     */
    private $classInspectorProvider;

    /**
     * @param ClassInspectorProvider $classInspectorProvider
     */
    public function __construct(ClassInspectorProvider $classInspectorProvider) {
        $this->classInspectorProvider = $classInspectorProvider;
    }


    public function executeAndWait($asynchronousInstances) {

        // Turn an async instance to a future using an AMPParallelTask wrapper.
        $toFuture = fn(Asynchronous $instance) => submit(new AMPParallelTask($instance))->getFuture();

        // Await execution of all queued tasks.
        $responses = await(
            array_map($toFuture, $asynchronousInstances)
        );

        // Grab response instances and resync original instances for reference integrity.
        foreach ($responses as $index => $response) {
            $classInspector = $this->classInspectorProvider->getClassInspector(get_class($response));
            $properties = $classInspector->getPropertyData($response, null, false);
            $classInspector->setPropertyData($asynchronousInstances[$index], $properties, null, false);
        }


    }
}