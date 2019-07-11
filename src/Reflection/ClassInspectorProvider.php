<?php


namespace Kinikit\Core\Reflection;

/**
 * Store for class inspectors:  Designed to be injected as required by other objects.
 *
 * @noProxy
 * @package Kinikit\Core\Util\Reflection
 */
class ClassInspectorProvider {

    private $classInspectors;

    /**
     * @param $className
     *
     * @return ClassInspector
     */
    public function getClassInspector($className) {

        $normalisedClassName = "\\" . ltrim(trim($className), "\\");

        if (!isset($this->classInspectors[$normalisedClassName])) {
            $this->classInspectors[$normalisedClassName] = new ClassInspector($className);
        }

        return $this->classInspectors[$normalisedClassName];

    }


}
