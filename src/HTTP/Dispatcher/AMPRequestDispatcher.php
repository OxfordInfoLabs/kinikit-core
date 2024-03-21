<?php

namespace Kinikit\Core\HTTP\Dispatcher;

use Amp\Http\Client\HttpClientBuilder;
use Kinikit\Core\HTTP\Request\Request;
use Kinikit\Core\HTTP\Response\Headers;
use Kinikit\Core\HTTP\Response\Response;
use Kinikit\Core\Reflection\Method;
use Kinikit\Core\Stream\String\ReadOnlyStringStream;

class AMPRequestDispatcher implements HttpRequestDispatcher { //TODO NOT FINISHED!!

    /**
     * @param Request $request
     * @return Response
     * @throws \Amp\ByteStream\BufferException
     * @throws \Amp\ByteStream\StreamException
     * @throws \Amp\Http\Client\HttpException
     * //TODO DO NOT USE WITHOUT WRITING PROPER TESTS!!!!
     */
    public function dispatch($request): Response {
        $client = HttpClientBuilder::buildDefault();
        $url = $request->getUrl();

        $toQueryParams = function(array $params) : string {
            if (!$params) {return "";}
            foreach ($params as $paramName => $paramValue){
                $equations[] = "$paramName=$paramValue";
            }
            return "?".join("&", $equations);
        };

        $ampRequest = match ($request->getMethod()) {
            Request::METHOD_GET => new \Amp\Http\Client\Request(
                $url . $toQueryParams($request->getParameters()), $request->getMethod() ?? Request::METHOD_GET, $request->getBody() ?? ""),
                //TODO Deal with POST requests
        };
        $ampResponse = $client->request($ampRequest);
        $ampHeaders = $ampResponse->getHeaders();
        $headers = [];
        foreach ($ampHeaders as $key => $headerArray){
            $headers[$key] = join(";", $headerArray);
        }
        $response = new Response(
            new ReadOnlyStringStream($ampResponse->getBody()->buffer()),
            $ampResponse->getStatus(),
            new Headers($headers),
            $request
        );

        return $response;
    }
}