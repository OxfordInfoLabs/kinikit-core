<?php

namespace Kinikit\Core\HTTP\Request;

include_once "autoloader.php";

class RequestTest extends \PHPUnit\Framework\TestCase {


    public function testGETRequestURLsAreEvaluatedCorrectlyWithParameters() {

        // Check plain url
        $newRequest = new Request("https://google.com", Request::METHOD_GET);
        $this->assertEquals("https://google.com", $newRequest->getEvaluatedUrl());

        // Add params
        $newRequest = new Request("https://google.com", Request::METHOD_GET, [
            "test1" => "Babykins",
            "test2" => "Boogie baby"
        ]);
        $this->assertEquals("https://google.com?test1=Babykins&test2=Boogie+baby", $newRequest->getEvaluatedUrl());

        // Now try one with an existing parameter
// Add params
        $newRequest = new Request("https://google.com?param1=mark", Request::METHOD_GET, [
            "test1" => "Babykins",
            "test2" => "Boogie baby"
        ]);
        $this->assertEquals("https://google.com?param1=mark&test1=Babykins&test2=Boogie+baby", $newRequest->getEvaluatedUrl());

    }

    public function testPOSTRequestBodyIsConstructedForNonPayloadRequest() {


        // Check plain url with no params has blank body
        $newRequest = new Request("https://google.com");
        $this->assertEquals("https://google.com", $newRequest->getEvaluatedUrl());
        $this->assertEquals("", $newRequest->getBody());


        // Check POST request with params are added as body
        $newRequest = new Request("https://google.com?param1=test", Request::METHOD_POST, [
            "test1" => "Babykins",
            "test2" => "Boogie baby"
        ]);

        $this->assertEquals("https://google.com?param1=test", $newRequest->getEvaluatedUrl());
        $this->assertEquals("test1=Babykins&test2=Boogie+baby", $newRequest->getBody());


    }

    public function testPOSTRequestBodyIsSetToPayloadAndParametersAppendedToURLIfPayloadSupplied() {

        // Check plain url with no params has blank body
        $newRequest = new Request("https://google.com", Request::METHOD_POST, [], "HELLO WORLD");
        $this->assertEquals("https://google.com", $newRequest->getEvaluatedUrl());
        $this->assertEquals("HELLO WORLD", $newRequest->getBody());

        // Check with parameters supplied
        $newRequest = new Request("https://google.com?param1=test", Request::METHOD_POST, [
            "test1" => "Babykins",
            "test2" => "Boogie baby"
        ], "HELLO WORLD");
        $this->assertEquals("https://google.com?param1=test&test1=Babykins&test2=Boogie+baby", $newRequest->getEvaluatedUrl());
        $this->assertEquals("HELLO WORLD", $newRequest->getBody());
    }

}