<?php


namespace Kinikit\Core\Stream;


/**
 * Generic Stream Interface - defines core functionality of a stream.
 *
 * @package Kinikit\Core\Stream
 */
interface Stream {


    /**
     * Indicator as to whether or not this stream is still open
     *
     * @return boolean
     */
    public function isOpen();

    /**
     * Return a boolean indicating whether this is the end of file
     *
     * @return boolean
     */
    public function isEof();


    /**
     * Close the stream
     *
     * @return mixed
     */
    public function close();

}