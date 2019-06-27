<?php


namespace Kinikit\Core\Reflection;

use Kinikit\Core\Annotation\Annotation;
use Kinikit\Core\Exception\AccessDeniedException;
use Kinikit\Core\DependencyInjection\ObjectInterceptor;


class TestTypedPOPO {

    private $id;
    private $name;
    private $dob;

    /**
     * TestPOPO constructor.
     */
    public function __construct(int $id, string $name, string $dob = "01/01/2016") {
        $this->id = $id;
        $this->name = $name;
        $this->dob = $dob;
    }


    /**
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     */
    public function setName(string $name) {
        $this->name = $name;
    }

    /**
     */
    public function setDob(string $dob) {
        $this->dob = $dob;
    }

    public function isSpecial(){
        return true;
    }

    /**
     * Clone method testing a class type
     *
     */
    public function clone(TestTypedPOPO $otherPOPO): TestTypedPOPO {
        return clone $otherPOPO;
    }


}
