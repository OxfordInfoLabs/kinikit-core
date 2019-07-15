<?php


namespace Kinikit\Core\Reflection;


class TestExtendedPOPO extends TestAnnotatedPOPO {


    /**
     * @param TestExtendedPOPO $other
     * @return TestExtendedPOPO
     */
    public function clone($other) {
        return clone $other;
    }

}
