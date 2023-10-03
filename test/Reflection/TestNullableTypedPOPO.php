<?php

namespace Kinikit\Core\Reflection;

enum TestEnum {
    case ON;
    case OFF;
}

class TestNullableTypedPOPO {
    private ?string $hat;
    private ?array $socks;
    /**
     * @var ?TestTypedPOPO
     */
    private ?TestTypedPOPO $testTypedPOPO;

    /**
     * @param ?string $hat
     * @param ?array $socks
     * @param ?TestTypedPOPO $testTypedPOPO
     */
    public function __construct(?string $hat, ?array $socks = [], ?TestTypedPOPO $testTypedPOPO = null) {
        $this->hat = $hat;
        $this->socks = $socks;
        $this->testTypedPOPO = $testTypedPOPO;
    }

    public function getHat(): ?string {
        return $this->hat;
    }

    public function setHat(?string $hat): void {
        $this->hat = $hat;
    }

    public function getSocks(): ?array {
        return $this->socks;
    }

    public function setSocks(?array $socks): void {
        $this->socks = $socks;
    }

    public function getTestTypedPOPO(): ?TestTypedPOPO {
        return $this->testTypedPOPO;
    }

    public function setTestTypedPOPO(?TestTypedPOPO $testTypedPOPO): void {
        $this->testTypedPOPO = $testTypedPOPO;
    }

}