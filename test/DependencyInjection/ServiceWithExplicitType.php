<?php


namespace Kinikit\Core\DependencyInjection;


class ServiceWithExplicitType {

    private $simpleService;
    private $complexService;

    /**
     * Construct with explicitly typed arguments
     *
     * @param SimpleService $simpleService
     * @param ComplexService $complexService
     */
    public function __construct(SimpleService $simpleService, ComplexService $complexService) {
        $this->simpleService = $simpleService;
        $this->complexService = $complexService;
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
    public function getSimpleService() {
        return $this->simpleService;
    }

    /**
     * @return ComplexService
     */
    public function getComplexService() {
        return $this->complexService;
    }


}
