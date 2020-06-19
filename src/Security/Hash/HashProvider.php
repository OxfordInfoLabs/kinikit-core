<?php

namespace Kinikit\Core\Security\Hash;

/**
 * Interface HashProvider
 *
 * @defaultImplementation Kinikit\Core\Security\Hash\SHA512HashProvider
 *
 * @implementationConfigParam hash.provider
 * @implementation sha512 Kinikit\Core\Security\Hash\SHA512HashProvider
 *
 */
interface HashProvider {

    /**
     * Generate a hash for the supplied string value
     *
     * @param string $value
     * @return string mixed
     */
    public function generateHash($value);

}
