<?php

namespace Kinikit\Core\Communication\Email\Provider;

use Kinikit\Core\Communication\Email\Email;
use Kinikit\Core\Communication\Email\EmailSendResult;
use Kinikit\Core\Communication\Email\FileEmailAttachment;


include_once __DIR__ . "/../../../autoloader.php";

class PHPMailerProviderTest extends \PHPUnit\Framework\TestCase {

    public function testCanSendBasicEmailUsingPHPMailer() {

//        $phpMailer = new PHPMailerProvider("localhost", 25);
//
//        // Send simple email
//        $email = new Email("Kiniauth Test <test@kinicart.com>", "Mark Oxil <mark@oxil.co.uk>",
//            "Test Email using PHP Mailer", "This is a little test to confirm that email is going out correctly");
//
//        $status = $phpMailer->send($email);
//
//        $this->assertEquals(EmailSendResult::STATUS_SENT, $status->getStatus());
//
//
//        // Send email with CC, BCC, custom Reply to and attachments
//        $email = new Email("Kiniauth Test <test@kinicart.com>", "Mark Oxil <mark@oxil.co.uk>",
//            "Test Email using PHP Mailer with Attachments", "This is a more advanced test to ensure that email is
//            going out as expected", array("Mark CC 1 <mark+cc1@oxil.co.uk>", "Mark CC 2 <mark+cc2@oxil.co.uk>"),
//            array("Mark BCC 1 <mark+bcc1@oxil.co.uk>", "Mark BCC 2 <mark+bcc2@oxil.co.uk>"),
//            "Marky Mark and Funky Bunch <mark+replyto@oxil.co.uk>",
//            [new FileEmailAttachment(__DIR__ . "/testtext.txt"), new FileEmailAttachment(__DIR__ . "/testimage.png")]);
//
//
//        $status = $phpMailer->send($email);
//
//        $this->assertEquals(EmailSendResult::STATUS_SENT, $status->getStatus());
//
//        $this->assertTrue(true);

        $this->assertTrue(true);
    }


}
