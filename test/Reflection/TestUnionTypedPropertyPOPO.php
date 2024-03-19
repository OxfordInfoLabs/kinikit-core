<?php

namespace Kinikit\Core\Reflection;

class TestUnionTypedPropertyPOPO {
    public function __construct(
        public int|string $unpredictableType,
        private TestNullableTypedPOPO|string|null $nully,
        private TestTypedPOPO|TestEnum $typedOrEnum
    ) {
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
}