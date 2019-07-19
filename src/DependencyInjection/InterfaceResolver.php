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

    private $classInspectorProvider;

    /**
     * InterfaceResolver constructor.  Requires an injected class inspector provider.
     *
     * @param ClassInspectorProvider $classInspectorProvider
     */
    public function __construct($classInspectorProvider) {
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
    public function getCurrentlyConfiguredImplementationClass($interfaceClass) {

        // Get the annotations for the class
        $classInspector = $this->classInspectorProvider->getClassInspector($interfaceClass);
        $classAnnotations = $classInspector->getClassAnnotations();

        $className = null;

        // Look for implementation configs and parameter
        $configValue = null;
        if (isset($classAnnotations["implementationConfigParam"]) &&
            isset($classAnnotations["implementation"])) {
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
     * @parm string $interfaceClass
     * @param string $implementationKey
     * @throws MissingInterfaceImplementationException
     */
    public function getImplementationClassForKey($interfaceClass, $implementationKey = null) {

        $classInspector = $this->classInspectorProvider->getClassInspector($interfaceClass);
        $classAnnotations = $classInspector->getClassAnnotations();

        if ($implementationKey) {

            $implementations = $classAnnotations["implementation"] ?? [];
            foreach ($implementations as $implementation) {
                $explodedImp = explode(" ", trim($implementation->getValue()));
                if ($explodedImp[0] == $implementationKey) {
                    return ltrim(trim($explodedImp[1]), "\\");
                }
            }

            // if no mapping found, simply return the value as explicit class mapping.
            $className = ltrim(trim($implementationKey), "\\");
            if (class_exists($className))
                return $className;
            else
                throw new MissingInterfaceImplementationException("No interface implementation exists of type $interfaceClass for key $implementationKey");

        } else {
            // Otherwise, check for default implementation
            if (isset($classAnnotations["defaultImplementation"])) {
                return ltrim(trim($classAnnotations["defaultImplementation"][0]->getValue()), "\\");
            } else {
                throw new MissingInterfaceImplementationException("No default implementation exists of type $interfaceClass");
            }
        }

    }


}
