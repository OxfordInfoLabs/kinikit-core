<?php

namespace Kinikit\Core\Asynchronous\Processor;

use Amp\Parallel\Worker\Execution;
use Kinikit\Core\Asynchronous\AMPParallel\AMPParallelTask;
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

        // Queue each of our async instances using an AMPParallelTask wrapper.
        $executions = [];
        foreach ($asynchronousInstances as $asynchronousInstance) {
            $executions[] = submit(new AMPParallelTask($asynchronousInstance));
        }

        // Await execution of all queued tasks.
        $responses = await(array_map(
            fn(Execution $e) => $e->getFuture(),
            $executions,
        ));

        // Grab response instances and resync original instances for reference integrity.
        foreach ($responses as $index => $response) {
            $classInspector = $this->classInspectorProvider->getClassInspector(get_class($response));
            $properties = $classInspector->getPropertyData($response, null, false);
            $classInspector->setPropertyData($asynchronousInstances[$index], $properties, null, false);
        }


    }
}