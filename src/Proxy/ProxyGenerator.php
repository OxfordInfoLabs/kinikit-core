<?php


namespace Kinikit\Core\Proxy;

use Kinikit\Core\Exception\RecursiveDependencyException;
use Kinikit\Core\Reflection\ClassInspector;


/**
 * Generates proxy classes dynamically which extends the class in question in the same namespace.
 *
 * @noProxy
 *
 * @package Kinikit\Core\DependencyInjection
 */
class ProxyGenerator {

    /**
     * Generate a dynamic proxy class for a given class name using the suffix for the extended class
     * Any included traits are added to the proxy.  A __call method must be supplied in at least one of
     * the traits which will be called by all methods with function arguments as an array.
     *
     * This returns the classname for the new proxy.
     *
     * @param string $className
     * @param string $proxySuffix
     * @param string[] $includedTraits
     *
     * @return string
     */
    public function generateProxy($className, $proxySuffix, $includedTraits, $blankConstructor = false) {

        $classInspector = new ClassInspector($className);

        $shortClass = $classInspector->getShortClassName();
        $proxyClassName = $shortClass . $proxySuffix;

        if (class_exists($className . $proxySuffix)) {
            return $className . $proxySuffix;
        }

        $namespace = $classInspector->getNamespace();

        $constructorParams = $classInspector->getConstructor() ? $this->getMethodParamsString($classInspector->getConstructor()) : "";

        $extensionType = $classInspector->isInterface() ? "implements" : "extends";

        $classString = "";

        if ($namespace)
            $classString = "
        namespace $namespace;";

        $classString .= "
        class $proxyClassName $extensionType $shortClass {
            
        ";
        foreach ($includedTraits as $trait) $classString .= "use \\" . ltrim($trait, "\\") . ";\n";

        if ($classInspector->getConstructor()) {
            $classString .= "
            public function __construct($constructorParams){";
            if (!$blankConstructor)
                $classString .= "(new \ReflectionClass(\$this))->getParentClass()->getMethod('__construct')->invokeArgs(\$this, func_get_args());";
            $classString .= "
            }
          
        ";
        }

        // Loop through all public methods and reimplement.
        foreach ($classInspector->getPublicMethods() as $method) {

            if ($method->isStatic() || $method->isFinal() || $method->getMethodName() == "__call")
                continue;


            $paramsString = $this->getMethodParamsString($method);
            $returnType = $method->getReturnType() && $method->getReturnType()->isExplicitlyTyped() ? ":" . $method->getReturnType()->getType() : "";

            $returnInstruction = ($method->getReturnType() && $method->getReturnType() !== "void") ? "return" : "";

            $classString .= "
            public function {$method->getMethodName()}($paramsString)$returnType{
                $returnInstruction \$this->__call('{$method->getMethodName()}', func_get_args());
            }
            
            ";
        }

        $classString .= "
        }";


        eval($classString);

        return $className . $proxySuffix;


    }


    // Get method params for a method object
    private function getMethodParamsString($method) {

        $params = [];
        foreach ($method->getParameters() as $parameter) {
            $param = ($parameter->isExplicitlyTyped() ? $parameter->getType() : "");

            if ($parameter->isVariadic()) {
                $param .= " ...$" . $parameter->getName();
            } else if ($parameter->isPassedByReference()) {
                $param .= " &$" . $parameter->getName();
            } else {
                $param .= " $" . $parameter->getName();
            }

            if (!$parameter->isRequired()) {

                $defaultValueString = $parameter->getDefaultValue();
                if (is_string($defaultValueString)) {
                    $defaultValueString = '"' . str_replace('"', '\\"', $defaultValueString) . '"';
                } else if (is_bool($defaultValueString)) {
                    $defaultValueString = $defaultValueString ? "true" : "false";
                } else if ($defaultValueString === null) {
                    $defaultValueString = "null";
                } else if (is_array($defaultValueString)) {
                    $defaultValueString = "[]";
                }


                $param .= ' = ' . $defaultValueString;
            }
            $params[] = $param;
        }
        $paramsString = join(",", $params);


        return $paramsString;

    }

}
