<?php

namespace Kinikit\Core\Util;

class TaskManager {

    static function getProcessId(): int {
        return getmypid();
    }

    static function getParentProcessId(): int {
        return posix_getppid();
    }

    public function killProcess($processId, $signalId = SIGKILL) {
        return posix_kill($processId, $signalId);
    }

}