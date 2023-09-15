<?php

namespace Kinikit\Core\Binding;

use Kinikit\Core\Reflection\TestTypedPOPO;

class SimpleNullableObject {
    private ?int $year;
    private ?array $parties;
    private ?TestTypedPOPO $testPOPO;

    /**
     * @param ?int $year
     * @param ?array $parties
     * @param ?TestTypedPOPO $testPOPO
     */
    public function __construct(?int $year, ?array $parties, ?TestTypedPOPO $testPOPO = null) {
        $this->year = $year;
        $this->parties = $parties;
        $this->testPOPO = $testPOPO;
    }


    public function getYear(): ?int {
        return $this->year;
    }

    public function setYear(?int $year): void {
        $this->year = $year;
    }

    public function getParties(): ?array {
        return $this->parties;
    }

    public function setParties(?array $parties): void {
        $this->parties = $parties;
    }

    public function getTestPOPO(): ?TestTypedPOPO {
        return $this->testPOPO;
    }

    public function setTestPOPO(?TestTypedPOPO $testPOPO): void {
        $this->testPOPO = $testPOPO;
    }




}