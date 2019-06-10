<?php


namespace Kinikit\Core\DependencyInjection;

class ObjectInterceptors {

    private $interceptors = array();

    /**
     * Add an interceptor to this collection.
     *
     * @param ObjectInterceptor $interceptor
     */
    public function addInterceptor($interceptor) {
        $this->interceptors[] = $interceptor;
    }

    /**
     * Return all iterators
     */
    public function getInterceptors() {
        return $this->interceptors;
    }
}
