<?php

namespace Cityware\Security\Passwords\Strength;

/**
 * Strength adapter interface
 */
interface Adapter
{
    /**
     * Return the calculated entropy.
     *
     * @param  string  $password
     *                           The string to check.
     * @return integer
     *                          Returns the calculated string entropy.
     */
    public function check($password);
}
