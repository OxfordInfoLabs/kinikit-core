<?php


namespace Kinikit\Core\DependencyInjection;


class SecondaryService {

    private $complexService;

    /**
     * SecondaryService constructor.
     * @param \Kinikit\Core\DependencyInjection\ComplexService $complexService
     */
    public function __construct($complexService) {
        $this->complexService = $complexService;
    }


    public function ok() {
        return "OK";
    }

    /**
     * @return ComplexService
     */
    public function getComplexService() {
        return $this->complexService;
    }


    public function throwException() {
        throw new \Exception("BINGO BONGO");
    }


}
