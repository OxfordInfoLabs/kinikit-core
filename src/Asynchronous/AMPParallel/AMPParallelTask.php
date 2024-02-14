<?php

namespace Kinikit\Core\Asynchronous\AMPParallel;

use Amp\Cancellation;
use Amp\Parallel\Worker\Task;
use Amp\Sync\Channel;
use Kinikit\Core\Asynchronous\Asynchronous;
use Kinikit\Core\Binding\ObjectBinder;
use Kinikit\Core\DependencyInjection\Container;


class AMPParallelTask implements Task {

    /**
     * @var Asynchronous
     */
    private $asynchronous;


    /**
     * @param Asynchronous $asynchronous
     */
    public function __construct($asynchronous) {
        $this->asynchronous = $asynchronous;

    }

    public function run(Channel $channel, Cancellation $cancellation): mixed {

        // Attempt to run asynchronous and set accordingly
        try {
            $result = $this->asynchronous->run();
            $this->asynchronous->setStatus(Asynchronous::STATUS_COMPLETED);
            $this->asynchronous->setReturnValue($result);
        } catch (\Throwable $e) {
            $this->asynchronous->setStatus(Asynchronous::STATUS_FAILED);
            $objectBinder = Container::instance()->get(ObjectBinder::class);
            $exceptionArray = $objectBinder->bindToArray($e);
            if (is_array($exceptionArray)) {
                unset($exceptionArray["file"]);
                unset($exceptionArray["line"]);
                unset($exceptionArray["previous"]);
                unset($exceptionArray["trace"]);
                unset($exceptionArray["traceAsString"]);
            }
            $this->asynchronous->setExceptionData($exceptionArray);

        }
        return $this->asynchronous;
    }
}