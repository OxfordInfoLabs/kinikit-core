<?php


namespace Kinikit\Core\DependencyInjection;


class ComplexService {

    private $simpleService;
    private $secondaryService;
    private $complexService;

    /**
     * ComplexService constructor.
     *
     * @param \Kinikit\Core\DependencyInjection\SimpleService $simpleService
     * @param \Kinikit\Core\DependencyInjection\SecondaryService $secondaryService
     * @param ComplexService $complexService
     */
    public function __construct($simpleService, $secondaryService, $complexService) {
        $this->simpleService = $simpleService;
        $this->secondaryService = $secondaryService;
        $this->complexService = $complexService;
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
