<?php

namespace Kinikit\Core\Asynchronous\Processor;


use Kinikit\Core\Asynchronous\Asynchronous;
use Kinikit\Core\Binding\ObjectBinder;
use Kinikit\Core\Reflection\ClassInspectorProvider;

/**
 * Class AsynchronousProcessor
 *
 * Designed to execute and wait for multiple Asynchronous instances to complete in the background.  It manages the state
 * transparently such that the parent has full access to the underlying child data.
 *
 * @package Kinikit\Core\Asynchronous
 */
class PCNTLAsynchronousProcessor implements AsynchronousProcessor {

    /**
     * @var ObjectBinder
     */
    private $objectBinder;

    /**
     * @var ClassInspectorProvider
     */
    private $classInspectorProvider;


    /**
     * AsynchronousProcessor constructor.
     *
     * @param ObjectBinder $objectBinder
     * @param ClassInspectorProvider $classInspectorProvider
     */
    public function __construct($objectBinder, $classInspectorProvider) {
        $this->objectBinder = $objectBinder;
        $this->classInspectorProvider = $classInspectorProvider;
    }

    /**
     * Execute multiple asynchronous instances in the background and wait for them to complete.
     *
     * @param $asynchronousInstances Asynchronous[]
     * @return Asynchronous[]
     */
    public function executeAndWait($asynchronousInstances) {

        $instancesByFile = [];
        foreach ($asynchronousInstances as $instance) {

            $tempFile = tempnam(sys_get_temp_dir(), "async-data");
            $instancesByFile[$tempFile] = $instance;

            $newThreadId = pcntl_fork();

            $classInspector = $this->classInspectorProvider->getClassInspector(get_class($instance));

            // If child process, run the process and serialise to a file
            if (!$newThreadId) {

                try {
                    // Run the instance
                    $returnValue = $instance->run();

                    // Update the status and return value
                    $classInspector->setPropertyData($instance, Asynchronous::STATUS_COMPLETED, "status", false);
                    $classInspector->setPropertyData($instance, $returnValue, "returnValue", false);

                } catch (\Exception $e) {

                    $exceptionArray = $this->objectBinder->bindToArray($e);

                    if (is_array($exceptionArray)) {
                        unset($exceptionArray["file"]);
                        unset($exceptionArray["line"]);
                        unset($exceptionArray["previous"]);
                        unset($exceptionArray["trace"]);
                        unset($exceptionArray["traceAsString"]);
                    }


                    // Update status and exception
                    $classInspector->setPropertyData($instance, Asynchronous::STATUS_FAILED, "status", false);
                    $classInspector->setPropertyData($instance, $exceptionArray, "exceptionData", false);

                }


                // Write the file to the temp file.
                file_put_contents($tempFile, serialize($instance));

                // Prevent resources from being removed on child exit by killing via posix functions.
                register_shutdown_function(function () {
                    posix_kill(getmypid(), SIGKILL);
                });

                exit(0);

            } else {
                $classInspector->setPropertyData($instance, Asynchronous::STATUS_RUNNING, "status", false);
            }

        }

        // Pop them off as they complete
        while (count($instancesByFile) > 0) {
            foreach ($instancesByFile as $file => $instance) {

                if (file_exists($file) && $contents = file_get_contents($file)) {

                    // Unserialised file contents
                    $unserialised = unserialize($contents);

                    $classInspector = $this->classInspectorProvider->getClassInspector(get_class($instance));

                    // Set the property data from serialised version
                    $propertyData = $classInspector->getPropertyData($unserialised, null, false);
                    $classInspector->setPropertyData($instance, $propertyData, null, false);

                    // Unset the instances by file reference.
                    unset($instancesByFile[$file]);

                    // Remove the file
                    unlink($file);

                }
            }

            // Wait quarter of a second before trying again
            usleep(250000);
        }

        return $asynchronousInstances;


    }

}