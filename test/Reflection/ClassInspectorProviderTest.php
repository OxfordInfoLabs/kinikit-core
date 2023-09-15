<?php

namespace Kinikit\Core\Reflection;
use Kinikit\Core\Binding\SimpleGetterSetterObj;
use Kinikit\Core\Binding\SimpleNullableObject;

include_once "autoloader.php";

class ClassInspectorProviderTest extends \PHPUnit\Framework\TestCase {
    public function testCanProvideObjectClass() {
        $classInspectorProvider = new ClassInspectorProvider();
        $inspector = $classInspectorProvider->getClassInspector(SimpleGetterSetterObj::class);
        $this->assertEquals(new ClassInspector(SimpleGetterSetterObj::class), $inspector);
    }
    public function testCanProvideNullableObjectClass() {
        $classInspectorProvider = new ClassInspectorProvider();
        $inspector = $classInspectorProvider->getClassInspector("?".SimpleNullableObject::class);
        $this->assertEquals(new ClassInspector(SimpleNullableObject::class), $inspector);
    }
}