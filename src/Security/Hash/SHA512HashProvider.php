<?php

namespace Kinikit\Core\Security\Hash;

/**
 * SHA 256 Hash provider
 *
 * Class SHA256HashProvider
 */
class SHA512HashProvider implements HashProvider {

    /**
     * Generate a hash for the supplied string value
     *
     * @param string $value
     * @return string mixed
     */
    public function generateHash($value) {
        return hash("sha512", $value);
    }
}
