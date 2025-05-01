<?php

namespace Kinikit\Core\Util;

use PHPUnit\Framework\TestCase;

include_once "autoloader.php";

class TaskManagerTest extends TestCase {

    public function testCanGetPid() {
        $this->assertEquals(getmypid(), TaskManager::getProcessId());
    }

    public function testCanGetPPid() {
        $this->assertEquals(posix_getppid(), TaskManager::getParentProcessId());
    }

}