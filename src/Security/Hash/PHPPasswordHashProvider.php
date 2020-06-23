<?php


namespace Kinikit\Core\Security\Hash;


class PHPPasswordHashProvider implements HashProvider {

    /**
     * Generate a hash for the supplied string value
     *
     * @param string $value
     * @return string mixed
     */
    public function generateHash($value) {
        return password_hash($value, PASSWORD_DEFAULT);
    }

    /**
     * Verify a value against a hash
     *
     * @param $value
     * @param $hash
     * @return boolean
     */
    public function verifyHash($value, $hash) {
        return password_verify($value, $hash);
    }
}
