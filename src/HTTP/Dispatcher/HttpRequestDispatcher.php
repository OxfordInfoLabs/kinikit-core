<?php


namespace Kinikit\Core\HTTP\Dispatcher;


use Kinikit\Core\HTTP\Request\Request;
use Kinikit\Core\HTTP\Response\Response;

/**
 *
 * @implementationConfigParam http.request.provider
 * @implementation php \Kinikit\Core\HTTP\Dispatcher\PHPRequestDispatcher
 * @implementation amp \Kinikit\Core\HTTP\Dispatcher\AMPRequestDispatcher
 * @defaultImplementation \Kinikit\Core\HTTP\Dispatcher\PHPRequestDispatcher
 *
 * Interface HttpRequestDispatcher
 * @package Kinikit\Core\HTTP\Dispatcher
 */
interface HttpRequestDispatcher {

    /**
     * Dispatch the request and return a response object
     *
     * @param Request $request
     * @return Response
     */
    public function dispatch($request);
}