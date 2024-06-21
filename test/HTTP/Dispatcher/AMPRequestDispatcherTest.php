<?php

namespace Kinikit\Core\HTTP\Dispatcher;

use Kinikit\Core\HTTP\Request\Request;
use Kinikit\Core\HTTP\Response\Headers;
use PHPUnit\Framework\TestCase;
use Kinikit\Core\HTTP\Dispatcher\AMPRequestDispatcher;

include_once "autoloader.php";

class AMPRequestDispatcherTest extends TestCase {
    private $dispatcher;

    public function setUp(): void {
        $this->dispatcher = new AMPRequestDispatcher();
    }

    public function testCanGetValidResponseForSimpleGETRequest() {

        $request = new Request("https://jsonplaceholder.typicode.com/posts", Request::METHOD_GET);

        $response = $this->dispatcher->dispatch($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue(strpos($response->getBody(), '"userId": 1') > 0);


        // Check headers populated
        $this->assertEquals("true", $response->getHeaders()->get(Headers::ACCESS_CONTROL_ALLOW_CREDENTIALS));


        $request = new Request("https://jsonplaceholder.typicode.com/comments", Request::METHOD_GET,
            [
                "postId" => 1
            ]);

        $response = $this->dispatcher->dispatch($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue(strpos($response->getBody(), '"postId": 1') > 0);
        $this->assertTrue(sizeof($response->getHeaders()->getHeaders()) > 5);
        $this->assertEquals("Express", $response->getHeaders()->get("x-powered-by"));


    }


    public function testCanIssueValidPOSTRequestWithPayload() {
        $request = new Request("https://jsonplaceholder.typicode.com/posts", Request::METHOD_POST, [],
            "{
    title: 'foo',
    body: 'bar',
    userId: 1,
  }");

        $response = $this->dispatcher->dispatch($request);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue(strpos($response->getBody(), '"id": 101') > 0);

    }


    public function testInvalidEndpointReturnsCorrectResponse() {

        $request = new Request("https://jsonplaceholder.typicode.com/posts/101", Request::METHOD_GET);

        $response = $this->dispatcher->dispatch($request);
        $this->assertEquals(404, $response->getStatusCode());

    }

    public function testHeadersAffectTheResultOfRequest(){
        $toHttpBinReq = fn($headers) => new Request(
            "https://httpbin.org/headers",
            Request::METHOD_GET,
            [],
            '',
            new Headers($headers));
        $request = $toHttpBinReq([
            "accept" => "text/html",
            "user-agent" => "A Computer somewhere",
            "ASDF" => 1
        ]);

        $rawResponse = $this->dispatcher->dispatch($request);
        $response = json_decode($rawResponse->getBody(), true);
        $this->assertEquals($response["headers"]["Accept"], "text/html");
        $this->assertEquals($response["headers"]["User-Agent"], "A Computer somewhere");
        // Header keys are Changed to camelcase
        $this->assertEquals($response["headers"]["Asdf"], 1);

        $respHeaders = $rawResponse->getHeaders()->getHeaders();
        $this->assertEquals("*", $respHeaders["access-control-allow-origin"]);
        $this->assertEquals("application/json", $respHeaders["content-type"]);
    }

    public function testPostRequest() {
        $request = new Request("https://httpbin.org/post", Request::METHOD_POST, payload: "my beautiful letter");
        $result = $this->dispatcher->dispatch($request);
        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals(json_decode($result->getBody(), true)["data"], "my beautiful letter");
    }

    public function testGzippedResponse() {
        $request = new Request("https://httpbin.org/gzip", Request::METHOD_GET);
        $result = $this->dispatcher->dispatch($request);
        $array = json_decode($result->getBody(), true);
        $this->assertTrue($array["gzipped"]);
    }
}