<?php

namespace Kinikit\Core\Util\HTTP;

use Kinikit\Core\Util\Logging\Logger;
use Kinikit\Core\Util\Serialisation\JSON\JSONToObjectConverter;
use Kinikit\Core\Util\Serialisation\XML\XMLToObjectConverter;

/**
 * Lightweight wrapper around the $_REQUEST object which cleans all parameters and provides array type access.
 *
 * Class HttpRequest
 * @package Kinikit\Core\Util\HTTP
 */
class HttpRequest {

    private static $instance;
    private $parameters = array();

    // Private constructor - should use instance method
    private function __construct() {

        // GRAB DIRECT POST DATA AS AN ASSOCIATIVE ARRAY
        // Required for data submitted as POST over ajax
        $directPHPInput = file_get_contents("php://input");
        $explodedParams = explode("&", $directPHPInput);

        if (isset($_SERVER["CONTENT_TYPE"]) && $_SERVER["CONTENT_TYPE"] == "application/xml") {
            $converter = new XMLToObjectConverter();
        } else {
            $converter = new JSONToObjectConverter();
        }

        $convertedInput = array();

        // Convert get params
        foreach ($_GET as $key => $value) {

            if (is_array($value)) {
                $decoded = array();
                foreach ($value as $valueEntry) {
                    $decoded[] = urldecode($valueEntry);
                }
            } else
                $decoded = urldecode($value);

            // Handle booleans.
            if ($decoded == "false") {
                $decoded = false;
            } else if ($decoded == "true") {
                $decoded = true;
            }

            $convertedInput[urldecode($key)] = $decoded;
        }


        // If only one param and not a key value pair, assume payload.
        if (sizeof($explodedParams) == 1 && !preg_match("/^[a-z0-9A-Z]+\=/", $explodedParams[0])) {
            $convertedInput["payload"] = $converter->convert(rawurldecode($explodedParams[0]));
        } else {

            // Convert post params
            foreach ($explodedParams as $param) {
                $explodedParam = explode("=", $param);

                if (sizeof($explodedParam) == 2) {
                    $decoded = urldecode($explodedParam[1]);

                    $converted = $converter->convert($decoded);
                    $convertedInput[urldecode($explodedParam[0])] = $converted ? $converted : $decoded;
                }
            }
        }


        $this->parameters = $convertedInput;
    }

    /**
     * Get a request parameter by key
     *
     * @param string $key
     */
    public function getParameter($key) {
        return isset($this->parameters[$key]) ? $this->parameters[$key] : null;
    }


    /**
     * Get all cleaned request parameters
     */
    public function getAllValues() {
        return $this->parameters;
    }

    public function getAllRawValues() {
        return $_REQUEST;
    }

    /**
     * Enforce a singleton session object
     *
     * @return HttpRequest
     */
    public static function instance($reload = false) {

        if (HttpRequest::$instance == null || $reload) {
            HttpRequest::$instance = new HttpRequest ();
        }


        return HttpRequest::$instance;
    }

}
