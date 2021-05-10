<?php


namespace Kinikit\Core\HTTP\Dispatcher;


use Kinikit\Core\HTTP\HttpRequestTimeoutException;
use Kinikit\Core\HTTP\Request\Headers as RequestHeaders;
use Kinikit\Core\HTTP\Request\Request;
use Kinikit\Core\HTTP\Response\Headers;
use Kinikit\Core\HTTP\Response\Response;
use Kinikit\Core\Stream\File\ReadOnlyFileStream;
use Kinikit\Core\Stream\Http\ReadOnlyHttpStream;
use Kinikit\Core\Stream\StreamException;


/**
 * Request dispatcher using the built in PHP stream operations
 *
 * Class PHPRequestDispatcher
 * @package Kinikit\Core\HTTP\Dispatcher
 */
class PHPRequestDispatcher implements HttpRequestDispatcher {

    /**
     * Dispatch the request and return the response
     *
     * @param Request $request
     * @return Response|void
     */
    public function dispatch($request) {

        $headers = $request->getHeaders()->getHeaders();

        // Ensure we have a default content type for requests
        if (!isset($headers[RequestHeaders::CONTENT_TYPE]) && $request->getMethod() != Request::METHOD_GET) {
            $request->getHeaders()->set(RequestHeaders::CONTENT_TYPE, "application/x-www-form-urlencoded");
        }

        $headers = $request->getHeaders()->getHeaders();

        $headersString = "";
        foreach ($headers as $header => $value) {
            $headersString .= $header . ": " . $value . "\r\n";
        }

        $contextOptions = ["http" =>
            [
                "header" => $headersString,
                "method" => $request->getMethod(),
                "content" => $request->getBody(),
                "ignore_errors" => true
            ]
        ];

        // Configure timeout if required
        if ($request->getTimeout()) {
            $contextOptions["http"]["timeout"] = $request->getTimeout();
        }

        $context = stream_context_create($contextOptions);

        try {
            $stream = new ReadOnlyHttpStream($request->getEvaluatedUrl(), $context);

            return $this->processResponse($request, $stream);


        } catch (StreamException $e) {

            // Detect and throw in timeout circumstance
            if ($e->getMessage() == "Request timed out for stream") {
                throw new HttpRequestTimeoutException();
            } else {
                throw ($e);
            }
        }


    }


    /**
     * Get the response headers for the last request
     */
    private function processResponse($request, $responseStream) {

        $headers = array();
        $responseCode = 0;

        $headersObject = $responseStream->getResponseHeaders();

        foreach ($headersObject as $k => $v) {
            $t = explode(':', $v, 2);
            if (isset($t[1]))
                $headers[trim($t[0])] = trim($t[1]);
            else if ($k == 0) {
                if (preg_match("#HTTP/[0-9\.]+\s+([0-9]+)#", $v, $out))
                    $responseCode = intval($out[1]);
            }
        }
        return new Response($responseStream, $responseCode, new Headers($headers), $request);
    }


}