<?php

namespace Kinikit\Core\Util\Multithreading;

/**
 * Created by JetBrains PhpStorm.
 * User: mark
 * Date: 15/11/2012
 * Time: 10:53
 * To change this template use File | Settings | File Templates.
 */
class ExampleThread extends Thread {
    /**
     * Main process method which contains the sub logic for the thread.
     *
     * @return mixed
     */
    protected function process($parameters) {
        file_put_contents("/tmp/thread", "BOO", FILE_APPEND);

        if ($parameters){
            file_put_contents("/tmp/thread",",".$parameters["param1"].",".$parameters["param2"], FILE_APPEND);
        }

        sleep(0.3);
    }
}
