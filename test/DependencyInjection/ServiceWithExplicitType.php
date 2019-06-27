<?php


namespace Kinikit\Core\DependencyInjection;


class ServiceWithExplicitType {

    private $simpleService;
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


}
