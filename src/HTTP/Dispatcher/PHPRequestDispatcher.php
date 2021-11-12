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

        $headerStrings = [];
        foreach ($headers as $header => $value) {
            $headerStrings[] = $header . ": " . $value;
        }

        $contextOptions = ["http" =>
            [
                "header" => $headerStrings,
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
            return new Response($stream, $stream->getResponseCode(), new Headers($stream->getResponseHeaders()), $request);


        } catch (StreamException $e) {

            // Detect and throw in timeout circumstance
            if ($e->getMessage() == "Request timed out for stream") {
                throw new HttpRequestTimeoutException();
            } else {
                throw ($e);
            }
        }


    }


}