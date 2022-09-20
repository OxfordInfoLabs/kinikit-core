<?php

namespace Kinikit\Core\Reflection;

class TestOverloadedPropertyPOPO extends TestPropertyPOPO {

    /**
     * @var integer
     */
    protected $withGetter;


    /**
     * @var TestExtendedPOPO
     */
    protected $withSetter;


    /**
     * @var integer
     */
    protected $withSetterAndGetter;


}