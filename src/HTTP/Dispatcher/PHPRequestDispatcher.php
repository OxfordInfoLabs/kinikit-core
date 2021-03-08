<?php


namespace Kinikit\Core\HTTP\Dispatcher;


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

        $contextOptions = ["http" =>
            [
                "header" => $request->getHeaders()->getHeaders(),
                "method" => $request->getMethod(),
                "content" => $request->getBody()
            ]
        ];

        $context = stream_context_create($contextOptions);

        $response = file_get_contents($request->getEvaluatedUrl(), false, $context);

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