<?php


namespace Kinikit\Core\DependencyInjection;

use Kinikit\Core\Configuration\Configuration;
use Kinikit\Core\Reflection\ClassInspectorProvider;

/**
 * Manage the resolution of interfaces to concrete implementations
 * for autowiring / configuration driven instances.
 *
 * Class InterfaceResolver
 * @package Kinikit\Core\DependencyInjection
 */
class InterfaceResolver {

    /**
     * Explicit implementations set by the add method
     *
     * @var array
     */
    private array $explicitImplementations = [];


    private ClassInspectorProvider $classInspectorProvider;

    /**
     * InterfaceResolver constructor.  Requires an injected class inspector provider.
     *
     * @param ClassInspectorProvider $classInspectorProvider
     */
    public function __construct(ClassInspectorProvider $classInspectorProvider) {
        $this->classInspectorProvider = $classInspectorProvider;
    }


    /**
     * Get the configured implementation for a passed interface using a config parameter if possible.
     * If no config parameter is supplied, a default implementation class will be returned.  Otherwise a
     * MissingInterfaceImplementationException will be thrown.
     *
     * @param string $interfaceClass
     * @return string
     * @throws MissingInterfaceImplementationException
     */
    public function getCurrentlyConfiguredImplementationClass(string $interfaceClass): string {

        // Get the annotations for the class
        $classInspector = $this->classInspectorProvider->getClassInspector($interfaceClass);
        $classAnnotations = $classInspector->getClassAnnotations();

        // Look for implementation configs and parameter
        $configValue = null;
        if (isset($classAnnotations["implementationConfigParam"], $classAnnotations["implementation"])) {
            $configParam = $classAnnotations["implementationConfigParam"][0]->getValue();
            $configValue = Configuration::readParameter($configParam);

        }

        return $this->getImplementationClassForKey($interfaceClass, $configValue);

    }


    /**
     * Get an implementation class for a key as defined by an @implementation annotation within the interface.  If null passed
     * for the key the @defaultImplementation will be returned instead if defined.
     *
     * Throws a missing interface implementation exception if none exists for the
     * passed key.
     *
     * @param string $interfaceClass
     * @param string|null $implementationKey
     * @return string
     * @throws MissingInterfaceImplementationException
     */
    public function getImplementationClassForKey(string $interfaceClass, ?string $implementationKey = null): string {

        $interfaceClass = ltrim($interfaceClass, "\\");
        $classInspector = $this->classInspectorProvider->getClassInspector($interfaceClass);
        $classAnnotations = $classInspector->getClassAnnotations();

        if ($implementationKey) {

            // if an explicit implementation set, return it straight away
            if (isset($this->explicitImplementations[$interfaceClass][$implementationKey])) {
                return $this->explicitImplementations[$interfaceClass][$implementationKey];
            }

            $implementations = $classAnnotations["implementation"] ?? [];
            foreach ($implementations as $implementation) {
                $explodedImp = explode(" ", trim($implementation->getValue()));
                if ($explodedImp[0] === $implementationKey) {
                    return ltrim(trim($explodedImp[1]), "\\");
                }
            }

            // if no mapping found, simply return the value as explicit class mapping.
            $className = ltrim(trim($implementationKey), "\\");

            if (class_exists($className)){
                return $className;
            }

            if (class_exists("\\$className")) {
                return "\\$className";
            }

            throw new MissingInterfaceImplementationException("No interface implementation exists of type $interfaceClass for key $implementationKey. Classname: $className");
        }

        if (isset($classAnnotations["defaultImplementation"])) {
            return ltrim(trim($classAnnotations["defaultImplementation"][0]->getValue()), "\\");
        }

        throw new MissingInterfaceImplementationException("No default implementation exists of type $interfaceClass");

    }


    /**
     * Add an implementation class for the supplied key
     *
     * @param string $interfaceClass
     * @param string $implementationKey
     * @param $implementationClass
     */
    public function addImplementationClassForKey(string $interfaceClass, string $implementationKey, $implementationClass): void {
        $interfaceClass = ltrim($interfaceClass, "\\");
        if (!isset($this->explicitImplementations[$interfaceClass])) {
            $this->explicitImplementations[$interfaceClass] = [];
        }
        $this->explicitImplementations[$interfaceClass][$implementationKey] = $implementationClass;
    }


}
