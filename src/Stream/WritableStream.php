<?php


namespace Kinikit\Core\Stream;


interface WritableStream extends Stream {

    /**
     * Write the supplied bytes to the stream
     *
     * @param $string
     * @return mixed
     */
    public function write($string);

}