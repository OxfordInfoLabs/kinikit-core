<?php


namespace Kinikit\Core\Reflection;


use Kinikit\Core\Util\Primitive;
use ReflectionParameter;

class Parameter {


    /**
     * The reflection parameter object
     *
     * @var ReflectionParameter
     */
    private ReflectionParameter $reflectionParameter;

    /**
     * The type for this parameter
     *
     * @var string
     */
    private string $type;

    /**
     * An indicator as to whether or not this parameter is explicitly typed.
     *
     * @var bool
     */
    private bool $explicitlyTyped;

    /**
     * Construct with the reflection parameter and the ownning method inspector.
     *
     * Parameter constructor.
     * @param ReflectionParameter $reflectionParameter
     * @param Method $method
     */
    public function __construct(ReflectionParameter $reflectionParameter, Method $method) {
        $this->reflectionParameter = $reflectionParameter;

        $declaredNamespaceClasses = $method->getDeclaringClassInspector()->getDeclaredNamespaceClasses();

        // Evaluate the parameter type according to whether or not this is an explicitly typed param or annotated.
        $type = "mixed";
        $nullablePrefix = "";
        $arraySuffix = "";

        $this->explicitlyTyped = false;
        $reflectionType = $reflectionParameter->getType();
        if ($reflectionType instanceof \ReflectionUnionType) {
            $type = implode("|", $reflectionType->getTypes());
            if ($reflectionType->allowsNull()) {
                $nullablePrefix = "?";
            }
        } else if ($reflectionParameter->getType() &&
            !str_contains($reflectionType->getName() ?? "", "array")
        ) {
            if ($reflectionParameter->getType() instanceof \ReflectionNamedType) {
                [$type, $arraySuffix] = $this->stripArrayTypeSuffix($reflectionParameter->getType()->getName());

                if (!Primitive::isStringPrimitiveType($type))
                    $type = "\\" . ltrim(trim($type), "\\");
            } else {
                [$type, $arraySuffix] = $this->stripArrayTypeSuffix($reflectionParameter->getType());
            }
            $this->explicitlyTyped = true;
        } else { // Untyped or array - refer to annotations

            $methodAnnotations = $method->getMethodAnnotations()["param"] ?? [];

            foreach ($methodAnnotations as $annotation) {
                if (preg_match("/.+?\\$" . $reflectionParameter->getName() . "($|\\[| )/", $annotation->getValue())) {

                    // Knock off the parameter name and use the first word to derive the type
                    $type = trim(str_replace('$' . $reflectionParameter->getName(), "", $annotation->getValue()));
                    $type = explode(" ", $type)[0];

                    // Deal with union types in annotations
                    $possibleTypes = explode("|", $type);

                    $prependNamespacesFn = function($possibleType) use ($declaredNamespaceClasses, $method) {
                        [$possibleType, $arraySuffix] = $this->stripArrayTypeSuffix($possibleType);
                        $namespacedType = $possibleType;
                        if ($possibleType !== "null" && !Primitive::isStringPrimitiveType($possibleType)) {
                            if (isset($declaredNamespaceClasses[$possibleType])) {
                                $namespacedType = $declaredNamespaceClasses[$possibleType];
                            }
                            else if (!str_starts_with($possibleType, "\\")) { // Prepend namespace
                                $namespacedType = "\\" . $method->getReflectionMethod()->getDeclaringClass()->getNamespaceName() . "\\" . $possibleType;
                            }

                        }
                        return $namespacedType . $arraySuffix;
                    };

                    $possibleTypes = array_map($prependNamespacesFn, $possibleTypes);

                    $type = implode("|", $possibleTypes);

                    break;
                }
            }
        }

        if ($this->explicitlyTyped && $reflectionParameter->allowsNull()) {
            $nullablePrefix = "?";
        }

        if ($type === "mixed") {    // Type mixed is nullable but cannot be prefixed with a '?'
            $nullablePrefix = "";
        }
        $this->type = $nullablePrefix . $type . $arraySuffix;

    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->reflectionParameter->getName();
    }

    /**
     * @return string
     */
    public function getType(): string {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isNullable(): bool {
        return str_starts_with(trim($this->type), "?");
    }

    /**
     * Is this parameter an array type
     */
    public function isArray(): bool {
        return preg_match("/\[.*\]$/", $this->type) || str_contains($this->type, "array");
    }

    /**
     * @return bool
     */
    public function isRequired(): bool {
        return (!$this->reflectionParameter->isVariadic()) && ((!$this->reflectionParameter->isOptional()) || (!$this->reflectionParameter->isDefaultValueAvailable()));
    }

    /**
     * @return mixed
     */
    public function getDefaultValue(): mixed {
        if (!$this->isRequired()) {
            return $this->reflectionParameter->isDefaultValueAvailable() ? $this->reflectionParameter->getDefaultValue() : null;
        }

        return null;
    }


    /**
     * @return bool
     */
    public function isVariadic(): bool {
        return $this->reflectionParameter->isVariadic();
    }


    /**
     * @return bool
     */
    public function isPassedByReference(): bool {
        return $this->reflectionParameter->isPassedByReference();
    }

    /**
     * @return bool
     */
    public function isExplicitlyTyped(): bool {
        return $this->explicitlyTyped;
    }


    /**
     * @return bool
     */
    public function isPrimitive(): bool {
        $type = trim($this->getType(), "?");
        return in_array($type, Primitive::TYPES);
    }


    // Strip Array type suffix
    // If "string[int]" is the input, output is ["string", "[int]"]
    private function stripArrayTypeSuffix($type): array {
        $strippedType = trim(preg_replace("/\[.*\]$/", "", $type));
        $arraySuffix = substr($type, strlen($strippedType));
        return [$strippedType, $arraySuffix];
    }


}
