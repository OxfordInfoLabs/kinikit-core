<?php


namespace Kinikit\Core\DependencyInjection;


class TestObjectInterceptor extends ObjectInterceptor {

    public $afterCreates = array();
    public $beforeCalls = array();
    public $afterCalls = array();
    public $exceptionCalls = array();

    public function afterCreate($objectInstance, $classInspector) {
        $this->afterCreates[] = get_class($objectInstance);
    }


    public function beforeMethod($objectInstance, $methodName, $params, $classInspector) {
        $this->beforeCalls[] = array(get_class($objectInstance), $methodName);
        return $params;
    }

    public function afterMethod($objectInstance, $methodName, $params, $returnValue, $classInspector) {
        $this->afterCalls[] = array(get_class($objectInstance), $methodName);
        return $returnValue;
    }

    public function onException($objectInstance, $methodName, $params, $exception, $classInspector) {
        $this->exceptionCalls[] = array(get_class($objectInstance), $methodName);
    }


}
