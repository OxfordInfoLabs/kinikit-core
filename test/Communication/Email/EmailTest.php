<?php


namespace Kinikit\Core\Communication\Email;


use Kinikit\Core\DependencyInjection\Container;
use Kinikit\Core\Security\Hash\SHA512HashProvider;

class EmailTest extends \PHPUnit\Framework\TestCase {


    public function testCanGetDefaultHashUsingMatchingRecipientsSubjectAndContent() {

        $hashProvider = Container::instance()->get(SHA512HashProvider::class);

        $email = new Email("test@hello.com", ["mark@test.com"], "My First Email", "Some example content");
        $expectedHash = $hashProvider->generateHash("mark@test.comMy First EmailSome example content");
        $this->assertEquals($expectedHash, $email->getHash());

        $email = new Email("test@hello.com", ["mark@test.com", "james@home.net", "adam@me.com"], "My First Email", "Some example content");
        $expectedHash = $hashProvider->generateHash("mark@test.com,james@home.net,adam@me.comMy First EmailSome example content");
        $this->assertEquals($expectedHash, $email->getHash());

    }

}