<?php


namespace Kinikit\Core\Reflection;


use Kinikit\Core\Util\Primitive;

class ReturnType {

    /**
     * @var string
     */
    private $type;

    /**
     * @var bool
     */
    private $explicitlyTyped;


    /**
     * ReturnType constructor.
     *
     * @param Method $methodInspector
     *
     */
    public function __construct($methodInspector) {

        $reflectionMethod = $methodInspector->getReflectionMethod();
        $methodAnnotations = $methodInspector->getMethodAnnotations();
        $declaredNamespaceClasses = $methodInspector->getDeclaringClassInspector()->getDeclaredNamespaceClasses();

        $type = "void";
        $this->explicitlyTyped = false;
        if ($reflectionMethod->getReturnType()) {
            $type = $reflectionMethod->getReturnType();
            if ($type instanceof \ReflectionNamedType) {
                $type = $type->getName();
                if (!in_array($type, Primitive::TYPES))
                    $type = "\\" . ltrim($type, "\\");
            }
            $this->explicitlyTyped = true;
        } else {
            if (isset($methodAnnotations["return"])) {
                $type = trim($methodAnnotations["return"][0]->getValue());
                if (!in_array($type, Primitive::TYPES)) {
                    if (isset($declaredNamespaceClasses[$type]))
                        $type = $declaredNamespaceClasses[$type];
                    else
                        $type = "\\" . $reflectionMethod->getDeclaringClass()->getNamespaceName() . "\\" . $type;
                }
            }
        }

        $this->type = $type;
    }


    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }


    /**
     * Return a boolean if this class is an instance of another class
     *
     * @param string $otherClassName
     */
    public function isInstanceOf($otherClassName) {
        $type = "\\" . trim($this->type, "\\");
        $otherClassName = "\\" . trim($otherClassName, "\\");

        return is_subclass_of($type, $otherClassName) || $otherClassName == $type;
    }

    /**
     * @return bool
     */
    public function isExplicitlyTyped() {
        return $this->explicitlyTyped;
    }


}
