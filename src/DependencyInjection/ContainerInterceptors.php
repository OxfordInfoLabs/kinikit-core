<?php


namespace Kinikit\Core\DependencyInjection;

class ContainerInterceptors {

    private array $interceptors = [];

    /**
     * Add an interceptor to this collection.
     *
     * @param ContainerInterceptor $interceptor
     * @param string[] $applicableClasses
     */
    public function addInterceptor(ContainerInterceptor $interceptor, array $applicableClasses = []): void {

        // Ensure we assign to global if set
        if (!$applicableClasses) {
            $applicableClasses = ["GLOBAL"];
        }

        // Add interceptor to all applicable classes
        foreach ($applicableClasses as $applicableClass) {

            $applicableClass = ltrim($applicableClass, "\\");

            if (!isset($this->interceptors[$applicableClass])) {
                $this->interceptors[$applicableClass] = [];
            }
            if (!in_array($interceptor, $this->interceptors[$applicableClass], true)) {
                $this->interceptors[$applicableClass][] = $interceptor;
            }
        }

    }

    /**
     * Return all global interceptors applied to all proxies in the container
     */
    public function getGlobalInterceptors() {
        return $this->interceptors["GLOBAL"] ?? [];
    }


    /**
     * Get interceptors defined for an underlying class
     *
     * @param string $className
     * @return array
     */
    public function getInterceptorsForClass(string $className): array {
        $className = ltrim($className, "\\");
        return array_merge($this->interceptors["GLOBAL"] ?? [], $this->interceptors[$className] ?? []);
    }

}
