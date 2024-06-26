<?php

namespace Kinikit\Core\HTTP\Dispatcher;

use Amp\Http\Client\HttpClientBuilder;
use Kinikit\Core\Configuration\Configuration;
use Kinikit\Core\Configuration\MissingConfigurationParameterException;
use Kinikit\Core\HTTP\HttpRequestErrorException;
use Kinikit\Core\HTTP\Request\Request;
use Kinikit\Core\HTTP\Response\Headers;
use Kinikit\Core\HTTP\Response\Response;

use Kinikit\Core\Logging\Logger;
use Kinikit\Core\Stream\String\ReadOnlyStringStream;

class AMPRequestDispatcher implements HttpRequestDispatcher {

    /**
     * @param Request $request
     * @return Response
     * @throws \Amp\ByteStream\BufferException
     * @throws \Amp\ByteStream\StreamException
     * @throws \Amp\Http\Client\HttpException
     */
    public function dispatch($request): Response {
        $client = HttpClientBuilder::buildDefault();
        $url = $request->getEvaluatedUrl();
        $ampRequest = new \Amp\Http\Client\Request(
            $url,
            $request->getMethod(),
            $request->getPayload() ?? '',
        );
        $ampRequest->setHeaders($request->getHeaders()->getHeaders());
        $ampRequest->setTransferTimeout(120);
        $ampRequest->setInactivityTimeout(120);

        $maxResponseBytes = Configuration::readParameter("amp.http.max.response.bytes");
        if (!$maxResponseBytes){
            $e = new MissingConfigurationParameterException("amp.http.max.response.bytes");
            Logger::log($e->getMessage(), Logger::WARNING);
            $maxResponseBytes = 10_000_000;
        }

        $ampRequest->setBodySizeLimit($maxResponseBytes);

        $ampResponse = $client->request($ampRequest);
        $ampHeaders = $ampResponse->getHeaders();
        $headers = [];
        foreach ($ampHeaders as $key => $headerArray){
            $headers[$key] = join(";", $headerArray);
        }
        // TODO properly stream result
        // See the Amp/ByteStream/Payload interface
        // It should be possible by reading characters until you have at least
        // enough to draw $limit characters or are at the end of stream.
        $response = new Response(
            new ReadOnlyStringStream($ampResponse->getBody()->buffer()),
            $ampResponse->getStatus(),
            new Headers($headers),
            $request
        );

        return $response;
    }
}