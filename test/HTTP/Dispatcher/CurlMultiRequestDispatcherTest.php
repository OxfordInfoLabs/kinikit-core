<?php

namespace Kinikit\Core\HTTP\Dispatcher;

use Kinikit\Core\HTTP\Request\Headers;
use Kinikit\Core\HTTP\Request\Request;

include_once "autoloader.php";

class CurlMultiRequestDispatcherTest extends \PHPUnit\Framework\TestCase {

    /**
     * @var CurlMultiRequestDispatcher
     */
    private $dispatcher;


    public function setUp(): void {
        $this->dispatcher = new CurlMultiRequestDispatcher();
    }


    public function testMultipleSuccessfulRequestsExecutedAndReturnedAsSingleArrayForSimpleGETRequests() {


        $request1 = new Request("https://jsonplaceholder.typicode.com/posts", Request::METHOD_GET);
        $request2 = new Request("https://jsonplaceholder.typicode.com/comments", Request::METHOD_GET);
        $request3 = new Request("https://jsonplaceholder.typicode.com/users", Request::METHOD_GET);

        $responses = $this->dispatcher->dispatch([$request1, $request2, $request3]);
        $this->assertEquals(3, sizeof($responses));

        // Check first response
        $this->assertEquals(200, $responses[0]->getStatusCode());
        $this->assertTrue(strpos($responses[0]->getBody(), '"userId": 1') > 0);
        $this->assertTrue(sizeof($responses[0]->getHeaders()->getHeaders()) > 5);
        $this->assertEquals("Express", $responses[0]->getHeaders()->get("x-powered-by"));
        $this->assertEquals($request1, $responses[0]->getRequest());

        // Check second response
        $this->assertEquals(200, $responses[1]->getStatusCode());
        $this->assertTrue(strpos($responses[1]->getBody(), '"postId": 1') > 0);
        $this->assertTrue(sizeof($responses[1]->getHeaders()->getHeaders()) > 5);
        $this->assertEquals("Express", $responses[1]->getHeaders()->get("x-powered-by"));
        $this->assertEquals($request2, $responses[1]->getRequest());

        // Check third response
        $this->assertEquals(200, $responses[2]->getStatusCode());
        $this->assertTrue(strpos($responses[2]->getBody(), '"username": "Bret"') > 0);
        $this->assertTrue(sizeof($responses[2]->getHeaders()->getHeaders()) > 5);
        $this->assertEquals("Express", $responses[2]->getHeaders()->get("x-powered-by"));
        $this->assertEquals($request3, $responses[2]->getRequest());


    }


    public function testMultipleRequestsExecutedAndReturnedAsSingleArrayForRequestIncludingFailingGETRequests() {


        $request1 = new Request("https://jsonplaceholder.typicode.com/posts", Request::METHOD_GET);
        $request2 = new Request("https://idontexist.xyz/bingo", Request::METHOD_GET);
        $request3 = new Request("https://jsonplaceholder.typicode.com/posts/101", Request::METHOD_GET);

        $responses = $this->dispatcher->dispatch([$request1, $request2, $request3]);
        $this->assertEquals(3, sizeof($responses));

        // Check first response
        $this->assertEquals(200, $responses[0]->getStatusCode());
        $this->assertTrue(strpos($responses[0]->getBody(), '"userId": 1') > 0);
        $this->assertTrue(sizeof($responses[0]->getHeaders()->getHeaders()) > 5);
        $this->assertEquals("Express", $responses[0]->getHeaders()->get("x-powered-by"));
        $this->assertEquals($request1, $responses[0]->getRequest());

        // Check second response
        $this->assertEquals(0, $responses[1]->getStatusCode());
        $this->assertEquals("", $responses[1]->getBody());
        $this->assertEquals($request2, $responses[1]->getRequest());

        // Check third response
        $this->assertEquals(404, $responses[2]->getStatusCode());
        $this->assertTrue(sizeof($responses[2]->getHeaders()->getHeaders()) > 5);
        $this->assertEquals("Express", $responses[2]->getHeaders()->get("x-powered-by"));
        $this->assertEquals($request3, $responses[2]->getRequest());


    }


    public function testMultiplePostRequestsWithPayloadsCanBeSent() {


        $request1 = new Request("https://jsonplaceholder.typicode.com/posts", Request::METHOD_POST, [],
            '{"title": "foo", "body": "bar", "userId": 1}', new Headers([Headers::CONTENT_TYPE => "application/json"]));


        $request2 = new Request("https://jsonplaceholder.typicode.com/posts", Request::METHOD_POST, [],
            '{"title": "bingo", "body": "bango", "userId": 1}', new Headers([Headers::CONTENT_TYPE => "application/json"]));


        $responses = $this->dispatcher->dispatch([$request1, $request2]);
        $this->assertEquals(2, sizeof($responses));



        $this->assertEquals(201, $responses[0]->getStatusCode());
        $this->assertTrue(strpos($responses[0]->getBody(), '"title": "foo"') > 0);
        $this->assertTrue(sizeof($responses[0]->getHeaders()->getHeaders()) > 5);
        $this->assertEquals("Express", $responses[0]->getHeaders()->get("x-powered-by"));
        $this->assertEquals($request1, $responses[0]->getRequest());


        $this->assertEquals(201, $responses[1]->getStatusCode());
        $this->assertTrue(strpos($responses[1]->getBody(), '"title": "bingo"') > 0);
        $this->assertTrue(sizeof($responses[1]->getHeaders()->getHeaders()) > 5);
        $this->assertEquals("Express", $responses[1]->getHeaders()->get("x-powered-by"));
        $this->assertEquals($request2, $responses[1]->getRequest());

    }


}