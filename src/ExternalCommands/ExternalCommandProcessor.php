<?php

namespace Kinikit\Core\ExternalCommands;

use Exception;
use Kinikit\Core\Logging\Logger;

class ExternalCommandProcessor {
    const WhiteListedCommands = ["rsync", "echo", "false", "docker", "date", "mkdir"];

    /**
     * Beware!! Do not allow user input to this (or risk unlimited access to your server)
     *
     * @param string $command
     * @param bool $throwOnError
     * @return int Result Code
     * @throws ExternalCommandException
     */
    public function process(string $command, bool $throwOnError = true){
        $commandName = explode(" ", $command)[0];
        if (!$commandName || !in_array($commandName, self::WhiteListedCommands)
            || str_contains($command, "|")
            || str_contains($command, "&&")
        ){
            throw new ExternalCommandException("Command is not whitelisted");
        }

        exec($command, $outputArray, $resultCode);
        if ($throwOnError && $resultCode != 0){
            $time = microtime(true);
            $output = join("\n", $outputArray);
            Logger::log("$time External Command Processor failed on $command with error code $resultCode | Command output:\n$output");
            throw new ExternalCommandException("Command returned error code $resultCode at time $time. See logs for details.");
        }
        return $resultCode;
    }
}