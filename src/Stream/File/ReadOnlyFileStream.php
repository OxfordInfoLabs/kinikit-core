<?php


namespace Kinikit\Core\Stream\File;


use Kinikit\Core\Stream\Resource\ReadOnlyFilePointerResourceStream;


/**
 * Read only file stream - great for reading from files and urls
 *
 * @package Kinikit\Core\Stream\File
 */
class ReadOnlyFileStream extends ReadOnlyFilePointerResourceStream {

    /**
     * Construct with a filename and optional context options
     *
     * @param $filename
     * @param ?array $contextOptions
     */
    public function __construct($filename, $contextOptions = null) {

        try {
            if (!$contextOptions) {
                $resource = fopen($filename, "r", false);
            } else {
                $resource = fopen($filename, "r", false, $contextOptions);
            }

            // If no resource, throw stream exception with message
            if ($resource === false) {
                $this->throwLastStreamError();
            }

            // Construct parent with successful resource
            parent::__construct($resource);

        } catch (\ErrorException $e) {
            $this->throwLastStreamError($e->getMessage());
        }
    }


}