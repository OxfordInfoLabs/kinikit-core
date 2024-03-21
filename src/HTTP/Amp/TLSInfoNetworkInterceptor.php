<?php

namespace Kinikit\Core\HTTP\Amp;

use Amp\Cancellation;
use Amp\Http\Client\Connection\Stream;
use Amp\Http\Client\NetworkInterceptor;
use Amp\Http\Client\Request;
use Amp\Http\Client\Response;
use Amp\Socket\TlsInfo;

class TLSInfoNetworkInterceptor implements NetworkInterceptor {
    public ?TlsInfo $tlsInfo;

    public function requestViaNetwork(Request $request, Cancellation $cancellation, Stream $stream): Response {
        $response = $stream->request($request, $cancellation);
        $tlsInfo = $stream->getTlsInfo();
        $this->tlsInfo = $tlsInfo;
        return $response;
    }
}