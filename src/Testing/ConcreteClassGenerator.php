<?php


namespace Kinikit\Core\Testing;


use Kinikit\Core\DependencyInjection\Container;
use Kinikit\Core\DependencyInjection\ContainerInterceptors;
use Kinikit\Core\DependencyInjection\Proxy;
use Kinikit\Core\Proxy\ProxyGenerator;
use Kinikit\Core\Reflection\ClassInspectorProvider;

class ConcreteClassGenerator {

    /**
     * @var ProxyGenerator
     */
    private $proxyGenerator;

    /**
     * @var ClassInspectorProvider
     */
    private $classInspectorProvider;

    /**
     * MockObjectProvider constructor.
     *
     * @param ProxyGenerator $proxyGenerator
     * @param ClassInspectorProvider $classInspectorProvider
     */
    public function __construct($proxyGenerator, $classInspectorProvider) {
        $this->proxyGenerator = $proxyGenerator;
        $this->classInspectorProvider = $classInspectorProvider;
    }

    /**
     * Singleton instance for testing convenience
     *
     * @return ConcreteClassGenerator
     */
    public static function instance() {
        return Container::instance()->get(ConcreteClassGenerator::class);
    }


    /**
     * Generate a concrete class instance typically from an abstract class
     * for testing base functionality
     *
     * @param string $baseClassName
     * @param string $newClassSuffix
     */
    public function generateInstance($baseClassName, $newClassSuffix = "Extension") {
        $proxyClass = $this->proxyGenerator->generateProxy($baseClassName, $newClassSuffix, [Proxy::class], true);
        $newInstance = new $proxyClass();

        // Populate with class inspector
        $classInspector = $this->classInspectorProvider->getClassInspector($baseClassName);
        $newInstance->__populate(new ContainerInterceptors(), $classInspector);

        return $newInstance;
    }


}