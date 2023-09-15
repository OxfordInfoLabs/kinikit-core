<?php

namespace Kinikit\Core\HTTP\Dispatcher;

use Kinikit\Core\HTTP\Response\Response;

/**
 * @implementationConfigParam http.multirequest.provider
 * @implementation curl \Kinikit\Core\HTTP\Dispatcher\CurlMultiRequestDispatcher
 * @defaultImplementation \Kinikit\Core\HTTP\Dispatcher\CurlMultiRequestDispatcher
 */
interface HttpMultiRequestDispatcher {

    /**
     * Pass multiple requests and process these to generate and return multiple responses (one for each request)
     *
     * @param Request[] $requests
     * @return Response[]
     */
    public function dispatch($requests);

}