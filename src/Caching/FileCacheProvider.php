<?php

namespace Kinikit\Core\Caching;

use Kinikit\Core\Configuration\Configuration;

class FileCacheProvider extends BaseCachingProvider {

    private string $cacheDir;

    public function __construct() {
        $this->cacheDir = Configuration::readParameter("files.root") . "/cache";

        if (!file_exists($this->cacheDir)) {
            mkdir($this->cacheDir);
        }
    }

    public function get(string $key) {

        $keyHash = md5($key);

        foreach (glob($this->cacheDir . "/$keyHash*") as $file) {
            if (str_starts_with(basename($file), "$keyHash-")) {
                $now = date("YmdHis");
                $expiryTime = explode('-', substr(basename($file), 0, -4))[1];

                if ($expiryTime < $now) {
                    unlink($file); // Remove the expired file
                    return false;
                }

                $valueString = file_get_contents($file);

                return unserialize($valueString);
            }
        }

        return false;
    }

    public function set(string $key, mixed $value, int $ttl): void {

        $keyHash = md5($key);

        // Write the output to the cache
        $expiry = date_create("+{$ttl} seconds")->format("YmdHis");

        file_put_contents($this->cacheDir . "/$keyHash-$expiry.txt", serialize($value));

    }

    public function clearCache(): void {

        foreach (glob($this->cacheDir . "/*") as $file) {
            unlink($file);
        }

    }
}