<?php

namespace Kinikit\Core\ExternalCommands;

use Exception;

class ExternalCommandProcessor {
    const WhiteListedCommands = ["rsync", "echo", "false"];

    /**
     * Beware!! Do not allow user input to this (or risk unlimited access to your server)
     *
     * @param string $command
     * @return int Result Code
     * @throws Exception
     */
    public function process(string $command){
        $commandName = explode(" ", $command)[0];
        if (!$commandName || !in_array($commandName, self::WhiteListedCommands) || str_contains($command, "|")){
            throw new Exception("Command is not whitelisted");
        }

        exec($command, $a, $resultCode);
        return $resultCode;
    }
}