<?php


namespace Kinikit\Core\Asynchronous\Processor;


use Kinikit\Core\Asynchronous\Asynchronous;


/**
 * Base interface for asynchronous processor implementations.
 *
 * Interface AsynchronousProcessor
 *
 * @implementationConfigParam asynchronous.processor
 * @implementation pcntl \Kinikit\Core\Asynchronous\Processor\PCNTLAsynchronousProcessor
 * @implementation sync \Kinikit\Core\Asynchronous\Processor\SynchronousProcessor
 * @defaultImplementation \Kinikit\Core\Asynchronous\Processor\SynchronousProcessor
 */
interface AsynchronousProcessor {

    /**
     * Execute multiple asynchronous instances in the background and wait for them to complete.
     *
     * @template T of Asynchronous
     * @param T[] $asynchronousInstances
     * @return T[]
     */
    public function executeAndWait($asynchronousInstances);

}