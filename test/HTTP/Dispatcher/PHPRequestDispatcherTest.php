<?php

namespace Kinikit\Core\HTTP\Dispatcher;

use Kinikit\Core\HTTP\HttpRequestTimeoutException;
use Kinikit\Core\HTTP\Request\Request;
use Kinikit\Core\HTTP\Response\Headers;

include_once "autoloader.php";

class PHPRequestDispatcherTest extends \PHPUnit\Framework\TestCase {

    // Dispatcher
    private $dispatcher;

    public function setUp(): void {
        $this->dispatcher = new PHPRequestDispatcher();
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


    public function testIfTimeoutPassedInRequestThisIsPassedToDispatcher() {

        $request = new Request("https://httpstat.us/200?sleep=5000", Request::METHOD_GET, [], null, null, 0.25);

        try {
            $this->dispatcher->dispatch($request);
            $this->fail("Should have thrown here");
        } catch (HttpRequestTimeoutException $e) {
            $this->assertTrue(true);
        }

    }

}