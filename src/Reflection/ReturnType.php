<?php


namespace Kinikit\Core\Reflection;


use Kinikit\Core\Util\Primitive;

class ReturnType {

    /**
     * @var string
     */
    private string $type;

    /**
     * @var bool
     */
    private bool $explicitlyTyped;


    /**
     * ReturnType constructor.
     *
     * @param Method $methodInspector
     *
     */
    public function __construct(Method $methodInspector) {

        $reflectionMethod = $methodInspector->getReflectionMethod();
        $methodAnnotations = $methodInspector->getMethodAnnotations();
        $declaredNamespaceClasses = $methodInspector->getDeclaringClassInspector()->getDeclaredNamespaceClasses();

        $type = "void";
        $this->explicitlyTyped = false;
        if ($reflectionMethod->getReturnType()) {
            $type = $reflectionMethod->getReturnType();
            if ($type instanceof \ReflectionNamedType) {
                $type = $type->getName();
                if (!Primitive::isStringPrimitiveType($type)) {
                    $type = "\\" . ltrim($type, "\\");
                }

                $type = $reflectionMethod->getReturnType()->allowsNull() ? "?".$type : $type;
            }
            $this->explicitlyTyped = true;
        } else if (isset($methodAnnotations["return"])) {
            $type = trim($methodAnnotations["return"][0]->getValue());
            if (!in_array($type, Primitive::TYPES)) {
                if (isset($declaredNamespaceClasses[$type])) {
                    $type = $declaredNamespaceClasses[$type];
                } else if (str_starts_with($type, "?")) { //If nullable
                    $type = "?\\" . $reflectionMethod->getDeclaringClass()->getNamespaceName() . "\\" . strpos($type, 1);
                } else {
                    $type = "\\" . $reflectionMethod->getDeclaringClass()->getNamespaceName() . "\\" . $type;
                }

            }
        }

        $this->type = $type;
    }


    /**
     * @return string
     */
    public function getType(): string {
        return $this->type;
    }


    /**
     * Return a boolean if this class is an instance of another class
     *
     * @param string $otherClassName
     */
    public function isInstanceOf(string $otherClassName): bool {
        $type = "\\" . trim($this->type, "\\");
        $otherClassName = "\\" . trim($otherClassName, "\\");

        return is_subclass_of($type, $otherClassName) || $otherClassName === $type;
    }

    /**
     * @return bool
     */
    public function isExplicitlyTyped(): bool {
        return $this->explicitlyTyped;
    }


}
