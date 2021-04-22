<?php


namespace Kinikit\Core\HTTP\Dispatcher;


use Kinikit\Core\HTTP\HttpRequestTimeoutException;
use Kinikit\Core\HTTP\Request\Request;
use Kinikit\Core\HTTP\Response\Headers;
use Kinikit\Core\HTTP\Response\Response;


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
        if (!sizeof($headers) && $request->getMethod() != Request::METHOD_GET) {
            $headers = ["Content-Type" => "application/x-www-form-urlencoded"];
        }

        $contextOptions = ["http" =>
            [
                "header" => $headers,
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

        $response = file_get_contents($request->getEvaluatedUrl(), false, $context);

        // Detect and throw in timeout circumstance
        if ($response === false && count($http_response_header) === 0) {
            throw new HttpRequestTimeoutException();
        }

        return $this->processResponse($request, $response, $http_response_header);


    }


    /**
     * Get the response headers for the last request
     */
    private function processResponse($request, $responseBody, $headersObject) {

        $headers = array();
        $responseCode = 0;

        foreach ($headersObject as $k => $v) {
            $t = explode(':', $v, 2);
            if (isset($t[1]))
                $headers[trim($t[0])] = trim($t[1]);
            else if ($k == 0) {
                if (preg_match("#HTTP/[0-9\.]+\s+([0-9]+)#", $v, $out))
                    $responseCode = intval($out[1]);
            }
        }
        return new Response($responseBody, $responseCode, new Headers($headers), $request);
    }


}