<?php


namespace Kinikit\Core\DependencyInjection;

enum ExampleEnum : string {
    case GREAT = "great";
    case FANTASTIC = "fantastic";
}

enum SimpleEnum {
    case CASE_1;
    case CASE_2;
}

class ServiceWithExplicitType {

    private $simpleService;
    private $secondaryService;
    private $complexService;

    /**
     * Construct with explicitly typed arguments
     *
     * @param SimpleService $simpleService
     * @param SecondaryService $secondaryService
     */
    public function __construct(SimpleService $simpleService, SecondaryService $secondaryService) {
        $this->simpleService = $simpleService;
        $this->secondaryService = $secondaryService;
    }


    /**
     * Test method.
     *
     * @return string
     */
    public function hello() {
        return "HELLO";
    }

    /**
     * @return SimpleService
     */
    public function getSimpleService(): SimpleService {
        return $this->simpleService;
    }

    /**
     * @return SecondaryService
     */
    public function getSecondaryService(): SecondaryService {
        return $this->secondaryService;
    }
    public function enumParameterMethod(SimpleEnum $simple, ExampleEnum $state = ExampleEnum::GREAT,
                                        \Kinikit\Core\DependencyInjection\ExampleEnum $uselessValue = \Kinikit\Core\DependencyInjection\ExampleEnum::FANTASTIC) : int {
        return strlen( $state->value);
    }

    public function enumReturnMethod(int $x) : ExampleEnum {
        if ($x == 100) {
            return ExampleEnum::FANTASTIC;
        }else{
            return ExampleEnum::GREAT;
        }
    }


}
