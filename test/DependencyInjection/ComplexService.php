<?php


namespace Kinikit\Core\DependencyInjection;


class ComplexService {

    private $simpleService;
    private $secondaryService;

    /**
     * ComplexService constructor.
     *
     * @param \Kinikit\Core\DependencyInjection\SimpleService $simpleService
     * @param \Kinikit\Core\DependencyInjection\SecondaryService $secondaryService
     */
    public function __construct($simpleService, $secondaryService) {
        $this->simpleService = $simpleService;
        $this->secondaryService = $secondaryService;
    }

    /**
     * Get the name
     *
     * @return string
     */
    public function getTitle() {
        return "Hello wonderful world of fun";
    }


    /**
     * Echo the params
     *
     * @param $a
     * @param $b
     * @param $c
     * @param $d
     */
    public function echoComplexParams($a, $b, $c, $d) {
        return array($a, $b, $c, $d);
    }

    /**
     * @return SimpleService
     */
    public function getSimpleService() {
        return $this->simpleService;
    }

    /**
     * @return SecondaryService
     */
    public function getSecondaryService() {
        return $this->secondaryService;
    }


}
