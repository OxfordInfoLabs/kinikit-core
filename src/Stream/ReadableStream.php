<?php


namespace Kinikit\Core\Stream;


interface ReadableStream extends Stream {

    /**
     * Read the number of bytes specified from the stream
     *
     * @param $length
     * @return string
     */
    public function read($length);


    /**
     * Read a whole line as a string
     *
     * @return mixed
     */
    public function readLine();


    /**
     * Read a CSV line using the passed separator and enclosures
     */
    public function readCSVLine($separator = ",", $enclosure = '"');


    /**
     * Read the entire contents from this stream - convenience method
     *
     * @return string
     */
    public function getContents();


}