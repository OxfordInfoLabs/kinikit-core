<?php


namespace Kinikit\Core\Proxy;

use Kinikit\Core\DependencyInjection\ContainerInterceptors;
use Kinikit\Core\DependencyInjection\Proxy;
use Kinikit\Core\DependencyInjection\ServiceWithExplicitType;
use Kinikit\Core\DependencyInjection\SimpleService;
use Kinikit\Core\Reflection\ClassInspector;

include_once "autoloader.php";

class ProxyGeneratorTest extends \PHPUnit\Framework\TestCase {


    public function testCanCreateProxyForSimpleClassWithNoConstructor() {

        $proxyGenerator = new ProxyGenerator();

        $className = $proxyGenerator->generateProxy(SimpleService::class, "Proxy", [Proxy::class]);

        // Check the class name is a proxy
        $this->assertEquals("Kinikit\Core\DependencyInjection\SimpleServiceProxy", $className);

        // Check the class exists.
        $this->assertTrue(class_exists("Kinikit\Core\DependencyInjection\SimpleServiceProxy"));

        // Check we are extending the base class
        $reflectionClass = new \ReflectionClass("Kinikit\Core\DependencyInjection\SimpleServiceProxy");
        $this->assertEquals(SimpleService::class, $reflectionClass->getParentClass()->getName());

        // Check that the constructor has no arguments in this case.
        $this->assertEquals(null, $reflectionClass->getConstructor());

        // Check that there are inherited methods for all methods in service.
        $getName = $reflectionClass->getMethod("getName");
        $this->assertEquals($getName->getDeclaringClass()->getName(), "Kinikit\Core\DependencyInjection\SimpleServiceProxy");
        $this->assertEquals(0, sizeof($getName->getParameters()));

        $echoParams = $reflectionClass->getMethod("echoParams");
        $this->assertEquals($echoParams->getDeclaringClass()->getName(), "Kinikit\Core\DependencyInjection\SimpleServiceProxy");
        $this->assertEquals(4, sizeof($echoParams->getParameters()));

        // Check that the methods can be called as usual on the proxy
        $proxy = new \Kinikit\Core\DependencyInjection\SimpleServiceProxy();
        $proxy->__populate(new ContainerInterceptors(), new ClassInspector(SimpleService::class));

        $this->assertEquals("Hello wonderful world of fun", $proxy->getName());
        $this->assertEquals(array("A", "B", "C", "D"), $proxy->echoParams("A", "B", "C", "D"));


    }


    public function testCanCreateProxyForClassWithExplicitTypeConstructor() {

        $proxyGenerator = new ProxyGenerator();

        $className = $proxyGenerator->generateProxy(ServiceWithExplicitType::class, "Extended", [Proxy::class]);

        // Check the class name is a proxy
        $this->assertEquals("Kinikit\Core\DependencyInjection\ServiceWithExplicitTypeExtended", $className);


        // Check the class exists.
        $this->assertTrue(class_exists("Kinikit\Core\DependencyInjection\ServiceWithExplicitTypeExtended"));

        // Check we are extending the base class
        $reflectionClass = new \ReflectionClass("Kinikit\Core\DependencyInjection\ServiceWithExplicitTypeExtended");
        $this->assertEquals(ServiceWithExplicitType::class, $reflectionClass->getParentClass()->getName());

        // Check that the constructor has no arguments in this case.
        $this->assertEquals(2, sizeof($reflectionClass->getConstructor()->getParameters()));

        $proxy = new \Kinikit\Core\DependencyInjection\ServiceWithExplicitTypeExtended(new SimpleService(), new \Kinikit\Core\DependencyInjection\SecondaryService(null));
        $proxy->__populate(new ContainerInterceptors(), new ClassInspector(ServiceWithExplicitType::class));

        $this->assertEquals("HELLO", $proxy->hello());
        $this->assertTrue($proxy->getSecondaryService() instanceof \Kinikit\Core\DependencyInjection\SecondaryService);
        $this->assertTrue($proxy->getSimpleService() instanceof \Kinikit\Core\DependencyInjection\SimpleService);


    }

}
