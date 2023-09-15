<?php

namespace Kinikit\Core\Reflection;

class TestNullableTypedPOPO {
    private ?string $hat;
    private ?array $socks;

    /**
     * @param ?string $hat
     * @param ?array $socks
     */
    public function __construct(?string $hat, ?array $socks = []) {
        $this->hat = $hat;
        $this->socks = $socks;
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



}