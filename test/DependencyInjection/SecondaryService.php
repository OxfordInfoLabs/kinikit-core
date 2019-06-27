<?php


namespace Kinikit\Core\DependencyInjection;


class SecondaryService {

    private $simpleService;

    /**
     * SecondaryService constructor.
     * @param SimpleService $simpleService
     */
    public function __construct($simpleService) {
        $this->simpleService = $simpleService;
    }


    public function ok() {
        return "OK";
    }

    /**
     * @return SimpleService
     */
    public function getSimpleService() {
        return $this->simpleService;
    }


    public function throwException() {
        throw new \Exception("BINGO BONGO");
    }


}
