<?php

namespace Kinikit\Core\Logging;

use Google\Cloud\Logging\LoggingClient;
use Google\Cloud\Logging\PsrLogger;
use Kinikit\Core\Binding\ObjectBinder;
use Kinikit\Core\Configuration\Configuration;
use Kinikit\Core\DependencyInjection\Container;

class GCloudLoggingProvider implements LoggingProvider {

    private PsrLogger $logger;

    private ObjectBinder $objectBinder;

    public function __construct() {

        $logging = new LoggingClient([
            'projectId' => Configuration::readParameter("log.gcloud.project")
        ]);

        $loggerName = Configuration::readParameter("log.name") ?? "app";
        $this->logger = $logging::psrBatchLogger("$loggerName");

        $this->objectBinder = Container::instance()->get(ObjectBinder::class);
    }

    public function log($message, int $severity = 7): void {

        if ($message instanceof \Exception) {

            $e = $message;
            if ($severity > 4) $severity = 4;

            $log["message"] = $e->getMessage();
            $log["exception_type"] = get_class($e);
            $log["file"] = $e->getFile();
            $log["line"] = $e->getLine();
            // TODO Deal with trace logging!
//            $log["trace"] = $e->getTrace();

            $logString = json_encode($log);

            $this->writeLog($logString, $severity);

        } else if (is_object($message)) {

            // Do object-y things
            $log["type"] = get_class($message);
            $log["object"] = $this->objectBinder->bindToArray($message);

            $logString = json_encode($log);
            $this->writeLog($logString, $severity);

        } else if (is_array($message)) {

            $logString = json_encode($message);

            $this->writeLog($logString, $severity);

        } else {

            $this->writeLog($message, $severity);

        }
    }

    /**
     * Log at the appropriate level
     *
     * @param string $entry
     * @param int $severity
     * @return void
     */
    private function writeLog(string $entry, int $severity): void {

        switch ($severity) {
            case 0:
                $this->logger->emergency($entry);
                break;
            case 1:
                $this->logger->alert($entry);
                break;
            case 2:
                $this->logger->critical($entry);
                break;
            case 3:
                $this->logger->error($entry);
                break;
            case 4:
                $this->logger->warning($entry);
                break;
            case 5:
                $this->logger->notice($entry);
                break;
            case 6:
                $this->logger->info($entry);
                break;
            case 7:
                $this->logger->debug($entry);
                break;
        }

    }

}
