<?php


namespace Kinikit\Core\DependencyInjection;

class ContainerInterceptors {

    private $interceptors = array();

    /**
     * Add an interceptor to this collection.
     *
     * @param ContainerInterceptor $interceptor
     */
    public function addInterceptor($interceptor) {
        if (!in_array($interceptor, $this->interceptors))
            $this->interceptors[] = $interceptor;
    }

    /**
     * Return all iterators
     */
    public function getInterceptors() {
        return $this->interceptors;
    }
}
