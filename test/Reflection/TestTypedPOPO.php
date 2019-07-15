<?php


namespace Kinikit\Core\Reflection;

use Kinikit\Core\Annotation\Annotation;
use Kinikit\Core\Exception\AccessDeniedException;
use Kinikit\Core\DependencyInjection\ContainerInterceptor;


class TestTypedPOPO {

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    protected $dob;

    /**
     * @var TestTypedPOPO
     */
    public $publicPOPO;

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
    public function setName(string $name): void {
        $this->name = $name;
    }

    /**
     */
    public function setDob(string $dob): void {
        $this->dob = $dob;
    }

    public function isSpecial(): bool {
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
