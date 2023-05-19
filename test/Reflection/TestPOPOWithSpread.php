<?php

namespace Kinikit\Core\Reflection;

class TestPOPOWithSpread {


    /**
     * Test Method with a spread operator
     *
     * @param string $test1
     * @param integer $test2
     * @param string[] ...$test3
     * @return array
     */
    public function example($test1, $test2 = null, ...$test3) {
        return [$test1, $test2, $test3];
    }

}