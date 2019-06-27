<?php


namespace Kinikit\Core\DependencyInjection;

use Kinikit\Core\Exception\RecursiveDependencyException;
use Kinikit\Core\Reflection\ClassInspector;
use Kinikit\Core\Reflection\Parameter;

/**
 * Generates proxy classes dynamically which extends the class in question in the same namespace.
 *
 * @package Kinikit\Core\DependencyInjection
 */
class ProxyGenerator {

    /**
     * Generate a dynamic proxy class for a given class name.  This returns the classname for the new proxy.
     *
     * @param $className
     * @return string
     */
    public function generateProxy($className) {

        $classInspector = new ClassInspector($className);

        $shortClass = $classInspector->getShortClassName();
        $proxyClassName = $shortClass . "Proxy";

        if (class_exists($className . "Proxy")) {
            return $className."Proxy";
        }

        $namespace = $classInspector->getNamespace();

        $constructorParams = $classInspector->getConstructor() ? $this->getMethodParamsString($classInspector->getConstructor()) : "";


        $classString = "
        namespace $namespace;
        
        class $proxyClassName extends $shortClass {
        
            use Proxy;
        
        ";

        if ($classInspector->getConstructor()) {
            $classString .= "
            public function __construct($constructorParams){
                (new \ReflectionClass(\$this))->getParentClass()->getMethod('__construct')->invokeArgs(\$this, func_get_args());
            }
          
        ";
        }

        // Loop through all public methods and reimplement.
        foreach ($classInspector->getPublicMethods() as $method) {

            $paramsString = $this->getMethodParamsString($method);
            $returnType = $method->getReturnType() && $method->getReturnType()->isExplicitlyTyped() ? ":" . $method->getReturnType()->getType() : "";

            $classString .= "
            public function {$method->getMethodName()}($paramsString)$returnType{
                return \$this->__call('{$method->getMethodName()}', func_get_args());
            }
            
            ";
        }

        $classString .= "
        }";


        eval($classString);

        return $className . "Proxy";


    }


    // Get method params for a method object
    private function getMethodParamsString($method) {

        $params = [];
        foreach ($method->getParameters() as $parameter) {
            $param = ($parameter->isExplicitlyTyped() ? $parameter->getType() : "") . " $" . $parameter->getName();
            if ($parameter->getDefaultValue() != Parameter::NO_DEFAULT_VALUE) {
                $param .= ' = ' . is_string($parameter->getDefaultValue()) ? '"' . $parameter->getDefaultValue() . '"' : $parameter->getDefaultValue();
            }
            $params[] = $param;
        }
        $paramsString = join(",", $params);

        return $paramsString;

    }

}
