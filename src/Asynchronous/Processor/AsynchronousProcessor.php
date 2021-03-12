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
 * @defaultImplementation \Kinikit\Core\Asynchronous\Processor\PCNTLAsynchronousProcessor
 */
interface AsynchronousProcessor {

    /**
     * Execute multiple asynchronous instances in the background and wait for them to complete.
     *
     * @param $asynchronousInstances Asynchronous[]
     */
    public function executeAndWait($asynchronousInstances);

}