<?php

namespace Kinikit\Core\Configuration;

use Exception;

/**
 * A generic configuration file handling class.  This handles the reading of parameters
 * defined within a standard properties type file format.
 * The constructor takes the path to the file to be read.
 *
 */
class ConfigFile {

    private ?string $configFilePath;
    private array $parameters = [];

    /**
     * Construct a config file object using the passed file name.
     *
     * @param string|null $configFilePath
     * @return void
     */
    public function __construct(?string $configFilePath = null) {

        if (file_exists($configFilePath)) {
            $this->parseFile($configFilePath);
        }

        $this->configFilePath = $configFilePath;
    }

    /**
     * Get the parameter matching the passed key
     * or null if non existent
     *
     * @param string $key
     * @return string|null
     */
    public function getParameter(string $key): ?string {
        return $this->parameters[$key] ?? null;
    }

    /**
     * Add a parameter by key and value
     *
     * @param string $key
     * @param string $value
     */
    public function addParameter(string $key, string $value): void {
        $this->parameters[$key] = $value;
    }

    /**
     * Remove a parameter by key
     *
     * @param string $key
     */
    public function removeParameter(string $key): void {
        unset ($this->parameters[$key]);
    }

    /**
     * Reset the parameters array with a new full array
     *
     * @param array $parameters
     */
    public function setParameters(array $parameters): void {
        $this->parameters = $parameters;
    }

    /**
     * Get all the parameters
     *
     * @return array
     */
    public function getAllParameters(): array {
        return $this->parameters;
    }


    /**
     * Get all parameters which match the supplied prefix.  If strip key prefixes is
     * supplied the params will be returned with keys stripped of the prefix otherwise
     * keys will be intact.
     *
     * @param $prefix
     * @param bool $stripKeyPrefixes
     *
     * @return string[]
     */
    public function getParametersMatchingPrefix($prefix, bool $stripKeyPrefixes = false): array {

        $matchingParams = [];
        foreach ($this->parameters as $key => $param) {
            if (strncmp($key, $prefix, strlen($prefix)) === 0) {
                if ($stripKeyPrefixes) {
                    $key = substr($key, strlen($prefix));
                }
                $matchingParams[$key] = $param;
            }
        }
        return $matchingParams;

    }


    /**
     * Return the actual text which would be written out by the save method.
     * This is particularly useful in situations like the PropertiesResource
     * where the output needs to be injected into a resource.
     *
     * @return string
     */
    public function getConfigFileText(): string {
        $configFileText = "";
        foreach ($this->parameters as $key => $value) {
            $configFileText .= $key . "=" . $value . "\n";
        }
        return $configFileText;
    }

    /**
     * Save the config file back out.  If null supplied for filepath, the constructed configFilePath is used
     * @param string|null $filePath
     */
    public function save(?string $filePath = null): void {

        // Determine the correct file path.
        $filePath = $filePath ?? $this->configFilePath;

        // Store the file
        file_put_contents($filePath, $this->getConfigFileText());
    }

    /**
     * Parse function.  Splits the config file into lines and
     * then looks for key value pairs of the form key=value
     */
    private function parseFile($configFilePath): void {
        $configFileText = file_get_contents($configFilePath);

        // Now split each line on carriage return
        $lines = explode("\n", $configFileText);

        // Now loop through each line, attempting a split by equals sign to get key value pairs out.
        foreach ($lines as $line) {

            // Firstly split lines on # to ensure that comments are ignored
            $splitComment = explode("#", $line);

            $propertyLine = trim($splitComment [0]);

            //  if the first entry is zero length when trimmed we know the line is a comment so we ignore the whole line
            // Otherwise continue and use the bit before any potential comment
            if ($propertyLine !== '') {

                // Now split into key, value on =
                $positionOfFirstEquals = strpos($propertyLine, "=");

                // If there are not 2 or more array entries at this point we should complain unless we meet a blank line.
                if ($positionOfFirstEquals) {
                    $value = trim(substr($propertyLine, $positionOfFirstEquals + 1));

                    // Convert boolean strings to boolean values
                    if ($value === "true") {
                        $value = true;
                    } else if ($value === "false") {
                        $value = false;
                    }

                    $this->parameters [trim(substr($propertyLine, 0, $positionOfFirstEquals))] = $value;
                } else {
                    throw new Exception("Error in config file: Parameter '" . $propertyLine . "' Does not have a value");
                }

            }
        }
    }

}