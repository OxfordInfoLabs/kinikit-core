<?php


namespace Kinikit\Core\Stream\Process;


use Kinikit\Core\Stream\File\ReadOnlyFileStream;
use Kinikit\Core\Stream\ReadableStream;
use Kinikit\Core\Stream\Resource\ReadOnlyFilePointerResourceStream;

/**
 * Read only process stream which creates a process using the passed
 * command string and then opens a stream to receive data as a stream.
 *
 * Class ReadOnlyProcessStream
 * @package Kinikit\Core\Stream\Process
 */
class ReadOnlyProcessStream extends ReadOnlyFilePointerResourceStream {

    /**
     * @var mixed
     */
    private $proc;

    /**
     * Set up the resource according to the
     *
     * ReadOnlyProcessStream constructor.
     * @param $command
     */
    public function __construct($command) {

        $descriptorSpec = [
            ["pipe", "r"],
            ["pipe", "w"]
        ];

        // Open the process and map pipes array
        $this->proc = proc_open($command,
            $descriptorSpec, $pipes);

        parent::__construct($pipes[1]);
    }


}