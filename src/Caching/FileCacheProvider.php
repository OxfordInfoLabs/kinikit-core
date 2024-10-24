<?php

namespace Kinikit\Core\Caching;

use Kinikit\Core\Binding\ObjectBinder;
use Kinikit\Core\Configuration\Configuration;
use Kinikit\Core\DependencyInjection\Container;

class FileCacheProvider implements CacheProvider {

    private string $cacheDir;

    private ObjectBinder $objectBinder;

    public function __construct() {
        $this->cacheDir = Configuration::readParameter("files.root") . "/cache";
        $this->objectBinder = Container::instance()->get(ObjectBinder::class);

        if (!file_exists($this->cacheDir)) {
            mkdir($this->cacheDir);
        }
    }

    public function lookup(string $key, callable $generatorFunction, int $ttl, array $params = [], ?string $returnClass = null) {

        // Check if it exists in the cache
        $value = $this->readCache($key, $returnClass);

        // If so, return the output
        if ($value) {
            return $value;
        }

        // Execute the callable
        $value = $generatorFunction(...$params);

        if ($value instanceof \Throwable) {
            throw $value;
        }

        // Cache the output
        $this->writeToCache($key, $value, $ttl);

        return $value;

    }

    private function readCache(string $key, ?string $returnClass): mixed {

        foreach (glob($this->cacheDir . "/$key*") as $file) {
            if (str_starts_with(basename($file), "$key-")) {
                $now = date("YmdHis");
                $expiryTime = explode('-', substr(basename($file), 0, -4))[1];

                if ($expiryTime < $now) {
                    unlink($file); // Remove the expired file
                    return false;
                }

                $valueString = file_get_contents($file);
                if ($returnClass) {
                    $json = json_decode($valueString, true);
                    return $this->objectBinder->bindFromArray($json, $returnClass, false);
                }

                return $valueString;
            }
        }

        return false;
    }

    private function writeToCache(string $key, mixed $value, int $ttl): void {

        // Write the output to the cache
        $expiry = date_create("+{$ttl} seconds")->format("YmdHis");

        if (is_object($value)) {
            $arr = $this->objectBinder->bindToArray($value, false, [], true);
            $value = json_encode($arr);
        }

        file_put_contents($this->cacheDir . "/$key-$expiry.txt", $value);

    }

    public function clearCache(): void {

        foreach (glob($this->cacheDir . "/*") as $file) {
            unlink($file);
        }

    }

}