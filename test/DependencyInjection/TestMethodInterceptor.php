<?php


namespace Kinikit\Core\DependencyInjection;


class TestMethodInterceptor extends MethodInterceptor {

    public $beforeCalls = array();
    public $afterCalls = array();
    public $exceptionCalls = array();


    public function beforeMethod($objectInstance, $methodName, $params, $classAnnotations) {
        $this->beforeCalls[] = array(get_class($objectInstance), $methodName);
    }

    public function afterMethod($objectInstance, $methodName, $params, $returnValue, $classAnnotations) {
        $this->afterCalls[] = array(get_class($objectInstance), $methodName);
    }

    public function onException($objectInstance, $methodName, $params, $exception, $classAnnotations) {
        $this->exceptionCalls[] = array(get_class($objectInstance), $methodName);
    }


}
