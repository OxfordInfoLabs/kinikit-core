<?php

namespace Kinikit\Core\Binding;

use Kinikit\Core\Reflection\TestTypedPOPO;

class SimpleNullableObject {
    private ?int $year;
    private ?array $parties;
    private ?TestTypedPOPO $testPOPO;
    private array $testTypedPopos;

    /**
     * @param ?int $year
     * @param ?array $parties
     * @param ?TestTypedPOPO $testPOPO
     * @param TestTypedPOPO[] $testTypedPopos
     */
    public function __construct(?int $year, ?array $parties, ?TestTypedPOPO $testPOPO = null, array $testTypedPopos = []) {
        $this->year = $year;
        $this->parties = $parties;
        $this->testPOPO = $testPOPO;
        $this->testTypedPopos = $testTypedPopos;
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

    public function getTestTypedPopos(): array {
        return $this->testTypedPopos;
    }

    public function setTestTypedPopos(array $testTypedPopos): void {
        $this->testTypedPopos = $testTypedPopos;
    }


}