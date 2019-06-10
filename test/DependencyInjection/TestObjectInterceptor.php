<?php


namespace Kinikit\Core\DependencyInjection;


class TestObjectInterceptor extends ObjectInterceptor {

    public $afterCreates = array();
    public $beforeCalls = array();
    public $afterCalls = array();
    public $exceptionCalls = array();

    public function afterCreate($objectInstance) {
        $this->afterCreates[] = get_class($objectInstance);
    }


    public function beforeMethod($objectInstance, $methodName, $params, $classAnnotations) {
        $this->beforeCalls[] = array(get_class($objectInstance), $methodName);
        return $params;
    }

    public function afterMethod($objectInstance, $methodName, $params, $returnValue, $classAnnotations) {
        $this->afterCalls[] = array(get_class($objectInstance), $methodName);
        return $returnValue;
    }

    public function onException($objectInstance, $methodName, $params, $exception, $classAnnotations) {
        $this->exceptionCalls[] = array(get_class($objectInstance), $methodName);
    }


}
