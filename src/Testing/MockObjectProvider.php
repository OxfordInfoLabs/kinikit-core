<?php

namespace Kinikit\Core\Testing;

use Kinikit\Core\DependencyInjection\Container;
use Kinikit\Core\Proxy\ProxyGenerator;
use Kinikit\Core\Reflection\ClassInspectorProvider;
use Kinikit\Core\Util\Primitive;

/**
 * Provide a mock instance proxy of a class as a subclass for testing purposes.
 *
 * Class MockObjectProvider
 */
class MockObjectProvider {

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
     * @return MockObjectProvider
     */
    public static function instance() {
        return Container::instance()->get(MockObjectProvider::class);
    }

    /**
     * @template T
     * @psalm-param class-string<T> $className
     * @return MockObject|T
     */
    public static function mock(string $className) {
        return MockObjectProvider::instance()->getMockInstance($className);
    }


    /**
     * Get a mock instance for a given class name
     *
     * All injected dependencies will be stubbed out with mock instances for convenience.
     * Constructor params if passed will be used instead of autodetection
     *
     * @template T
     * @psalm-param class-string<T> $className
     * @param array|null $constructorParams
     * @return MockObject|T
     */
    public function getMockInstance($className, $constructorParams = null) {

        // Make a mockery.
        $mockClass = $this->proxyGenerator->generateProxy($className, "Mock", [MockObject::class], true);

        // Get inspector for the new mock class
        $classInspector = $this->classInspectorProvider->getClassInspector($className);

        $constructor = $classInspector->getConstructor();


        if (!is_array($constructorParams)) {
            if ($constructor) {
                $constructorParams = $constructor->getIndexedParameters();
                $params = [];
                foreach ($constructorParams as $key => $value) {
                    if (!Primitive::isStringPrimitiveType($value->getType())) {
                        // For mocking an array, just use []
                        if ($value->isArray()) {
                            $params[$key] = [];
                        } else {
                            $params[$key] = $this->getMockInstance($value->getType());
                        }
                    }
                }
            } else {
                $params = [];
            }
        } else {
            $params = $constructorParams;
        }


        $mockInspector = $this->classInspectorProvider->getClassInspector($mockClass);
        $instance = $mockInspector->createInstance($params);

        // Set the underlying class inspector on the mock object for use to verify calls.
        $mockInspector->setPropertyData($instance, $classInspector, "underlyingClassInspector", false);

        return $instance;
    }

}
