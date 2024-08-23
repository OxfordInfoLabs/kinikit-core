<?php

namespace Kinikit\Core\HTTP\Dispatcher;

use Kinikit\Core\HTTP\Request\Headers as RequestHeaders;
use Kinikit\Core\HTTP\Request\Request;
use Kinikit\Core\HTTP\Response\Headers;
use Kinikit\Core\HTTP\Response\Response;
use Kinikit\Core\Stream\String\ReadOnlyStringStream;

class CurlMultiRequestDispatcher implements HttpMultiRequestDispatcher {

    /**
     * Dispatch multiple requests and return all responses once completed
     *
     * @param Request[] $requests
     * @return Response[]
     */
    public function dispatch($requests) {

        $curlMulti = curl_multi_init();

        // Loop through each request, make a curl object
        $curls = [];
        foreach ($requests as $request) {

            // Create our curl and set up options
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $request->getUrl());
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HEADER, true);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request->getMethod());

            if ($request->getBody()) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $request->getBody());
            }


            // Grab headers from request and update the headers array
            $headers = $request->getHeaders()->getHeaders() ?? [];

            $headersArray = [];
            foreach ($headers as $header => $value) {
                $headersArray[] = $header . ": " . $value;
            }

            if (!isset($headers[RequestHeaders::CONTENT_TYPE]) && $request->getMethod() != Request::METHOD_GET) {
                $headersArray[] = RequestHeaders::CONTENT_TYPE . ": application/x-www-form-urlencoded";
            }

            curl_setopt($curl, CURLOPT_HTTPHEADER, $headersArray);


            // Add our curl to the multi handle
            curl_multi_add_handle($curlMulti, $curl);


            $curls[] = $curl;

        }

        do {
            $status = curl_multi_exec($curlMulti, $active);
            if ($active) {
                curl_multi_select($curlMulti);
            }
        } while ($active && $status == CURLM_OK);


        // Generate all responses and clean up the curl session
        $responses = [];
        foreach ($curls as $index => $curl) {

            // Grab response meta data from curl
            $headersLength = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $responseCode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);

            // Process raw response and chomp out headers
            $rawResponse = curl_multi_getcontent($curl);
            $headersString = substr($rawResponse, 0, $headersLength);

            $headers = $this->processHeaders($headersString);
            $content = substr($rawResponse, $headersLength);


            // Gather bits we need
            $responseStream = new ReadOnlyStringStream($content);


            // Add the response to the list
            $responses[] = new Response($responseStream, $responseCode, new Headers($headers), $requests[$index]);

            curl_multi_remove_handle($curlMulti, $curl);
        }
        curl_multi_close($curlMulti);


        // Return responses
        return $responses;

    }


    // Process headers from a headers string
    private function processHeaders($headersString) {

        // Split lines
        $headerLines = explode("\r\n", $headersString);

        // Knock off the status message
        array_shift($headerLines);

        // Define as array before using in loop
        $indexedHeaders = [];

        // Create an associative array containing the response headers
        foreach ($headerLines as $value) {
            $splitHeader = explode(":", $value);
            if (sizeof($splitHeader) == 2) {
                $indexedHeaders[trim($splitHeader[0])] = trim($splitHeader[1]);
            }
        }

        return $indexedHeaders;

    }

}