<?php

namespace Kinikit\Core\DependencyInjection;

class DependencyContainerInterceptor extends ContainerInterceptor {

    public function __construct(private SimpleService $simpleService) {
    }
}