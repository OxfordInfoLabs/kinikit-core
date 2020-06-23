<?php

namespace Kinikit\Core\Security\Hash;

/**
 * Interface HashProvider
 *
 * @defaultImplementation Kinikit\Core\Security\Hash\PHPPasswordHashProvider
 *
 * @implementationConfigParam hash.provider
 * @implementation sha512 Kinikit\Core\Security\Hash\SHA512HashProvider
 * @implementation php Kinikit\Core\Security\Hash\PHPPasswordHashProvider
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


    /**
     * Verify a value against a hash
     *
     * @param $value
     * @param $hash
     * @return boolean
     */
    public function verifyHash($value, $hash);

}
