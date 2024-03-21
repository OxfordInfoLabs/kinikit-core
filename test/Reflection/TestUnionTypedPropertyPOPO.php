<?php

namespace Kinikit\Core\Reflection;

class TestUnionTypedPropertyPOPO {
    /**
     * @var int[]|bool|array<string,string>
     */
    private $annoProp;

    public function __construct(
        public int|string $unpredictableType,
        private TestNullableTypedPOPO|string|null $nully,
        private TestTypedPOPO|TestEnum $typedOrEnum,
        $annoProp = false
    ) {
        $this->annoProp = $annoProp;
    }

    public function getNully(): TestNullableTypedPOPO|string|null {
        return $this->nully;
    }

    public function getTypedOrEnum(): TestTypedPOPO|TestEnum {
        return $this->typedOrEnum;
    }

    public function setNully(TestNullableTypedPOPO|string|null $nully): void {
        $this->nully = $nully;
    }

    public function setTypedOrEnum(TestTypedPOPO|TestEnum $typedOrEnum): void {
        $this->typedOrEnum = $typedOrEnum;
    }

    /**
     * @return int[]|bool|array<string,string>
     */
    public function getAnnoProp() {
        return $this->annoProp;
    }

    /**
     * @param int[]|bool|array<string,string> $annoProp
     * @return void
     */
    public function setAnnoProp($annoProp) {
        $this->annoProp = $annoProp;
    }
}