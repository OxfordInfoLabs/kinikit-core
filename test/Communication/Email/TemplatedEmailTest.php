<?php

namespace Kinikit\Core\Communication\Email;


use Kinikit\Core\Communication\Email\Attachment\StringEmailAttachment;
use Kinikit\Core\Validation\ValidationException;


include_once __DIR__ . "/../../autoloader.php";


class TemplatedEmailTest extends \PHPUnit\Framework\TestCase {


    public function testIfMissingTemplateFileExceptionRaised() {

        try {
            new TemplatedEmail("bad-email", []);
            $this->fail("Should have thrown here");
        } catch (MissingEmailTemplateException $e) {
            // Success
            $this->assertTrue(true);
        }

    }

    public function testIfTemplateWithMissingRequiredDataSuppliedValidationExceptionRaised() {

        try {

            new TemplatedEmail("test-email", []);
            $this->fail("Should have thrown here");

        } catch (ValidationException $e) {
            // Success

        }


        try {

            new TemplatedEmail("test-email", [], ["james@test.com"]);
            $this->fail("Should have thrown here");

        } catch (ValidationException $e) {
            // Success

        }


        try {

            new TemplatedEmail("test-email", [], ["james@test.com"], "mark@bingo.com");
            $this->fail("Should have thrown here");

        } catch (ValidationException $e) {
            // Success

        }


        $this->assertTrue(true);
    }


    public function testSimpleHTMLTemplateEmailIsParsedWhenValidAdditionalData() {

        $templatedEmail = new TemplatedEmail("test-email", ["name" => "Bob"], ["james@test.com"], "mark@bingo.com", "Test example", ["admin@test.com"],
            ["bcc@test.com"], "replyto@test.com", [new StringEmailAttachment("PINEAPPLE", "pineapple.txt")]);

        $this->assertEquals("Test example", $templatedEmail->getSubject());
        $this->assertEquals(["james@test.com"], $templatedEmail->getRecipients());
        $this->assertEquals("mark@bingo.com", $templatedEmail->getFrom());
        $this->assertEquals(["admin@test.com"], $templatedEmail->getCc());
        $this->assertEquals(["bcc@test.com"], $templatedEmail->getBcc());
        $this->assertEquals("replyto@test.com", $templatedEmail->getReplyTo());
        $this->assertEquals([new StringEmailAttachment("PINEAPPLE", "pineapple.txt")], $templatedEmail->getAttachments());


        $this->assertTrue(strpos($templatedEmail->getTextBody(), "Hello, Bob, welcome to my email thread") > 0);

    }


    public function testTemplateWithHeaderFieldsIsParsedCorrectly() {

        $templatedEmail = new TemplatedEmail("test-with-header", ["name" => "Dave", "domainName" => "test.fun", "toEmail" => "dave@pickmeup.com"]);

        $this->assertEquals("Welcome Dave", $templatedEmail->getSubject());
        $this->assertEquals(["dave@pickmeup.com"], $templatedEmail->getRecipients());
        $this->assertEquals("jane@test.fun", $templatedEmail->getFrom());
        $this->assertEquals(["cc@test.fun", "cc2@test.fun"], $templatedEmail->getCc());
        $this->assertEquals(["bcc@test.fun", "bcc2@test.fun"], $templatedEmail->getBcc());
        $this->assertEquals("admin@test.fun", $templatedEmail->getReplyTo());

        $this->assertTrue(strpos($templatedEmail->getTextBody(), "Welcome Dave") > 0);
        $this->assertTrue(strpos($templatedEmail->getTextBody(), "I am welcoming you to the test.fun domain.") > 0);
    }


    public function testMetaDataMadeAvailableToBody() {

        $templatedEmail = new TemplatedEmail("test-with-meta", ["name" => "Dave", "domainName" => "test.fun", "toEmail" => "dave@pickmeup.com"]);

        $this->assertEquals("Welcome Dave", $templatedEmail->getSubject());
        $this->assertEquals(["dave@pickmeup.com"], $templatedEmail->getRecipients());
        $this->assertEquals("jane@test.fun", $templatedEmail->getFrom());
        $this->assertEquals(["cc@test.fun", "cc2@test.fun"], $templatedEmail->getCc());
        $this->assertEquals(["bcc@test.fun", "bcc2@test.fun"], $templatedEmail->getBcc());
        $this->assertEquals("admin@test.fun", $templatedEmail->getReplyTo());

        // Now check meta data evaluated in body
        $this->assertTrue(strpos($templatedEmail->getTextBody(), "Subject: Welcome Dave") > 0);
        $this->assertTrue(strpos($templatedEmail->getTextBody(), "From: jane@test.fun") > 0);
        $this->assertTrue(strpos($templatedEmail->getTextBody(), "To: dave@pickmeup.com") > 0);
        $this->assertTrue(strpos($templatedEmail->getTextBody(), "ReplyTo: admin@test.fun") > 0);
        $this->assertTrue(strpos($templatedEmail->getTextBody(), "CC: cc@test.funcc2@test.fun") > 0);
        $this->assertTrue(strpos($templatedEmail->getTextBody(), "BCC: bcc@test.funbcc2@test.fun") > 0);

    }


    public function testBodyWithIncludeIsResolvedCorrectlyRelativeToEmailTemplatesDirectory() {

        $templatedEmail = new TemplatedEmail("test-with-include", ["name" => "Bob"], ["james@test.com"], "mark@bingo.com", "Test example", ["admin@test.com"],
            ["bcc@test.com"], "replyto@test.com", [new StringEmailAttachment("PINEAPPLE", "pineapple.txt")]);

        $this->assertTrue(strpos($templatedEmail->getTextBody(), "Hello, Bob, welcome to my email thread") > 0);


    }


}
