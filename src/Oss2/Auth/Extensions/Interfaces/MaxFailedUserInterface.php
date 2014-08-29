<?php namespace Oss2\Auth\Extensions\Interfaces;

/**
 * Oss2/Auth
 *
 * Interface for the MaxFailed extension
 *
 * User classes must implement these functions for this extension.
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.md.
 *
 * @category   Authentication
 * @package    Oss2\Auth
 * @copyright  Copyright (c) 2014, Open Source Solutions Limited, Dublin, Ireland
 */
interface MaxFailedUserInterface
{
    /**
     * Return the current counter for sequential failed authentication attempts
     * @return int
     */
    public function getAuthAttempts();

    /**
     * Set the counter for failed auth attempts.
     *
     * Usually just used to reset to zero. Use increment to increment the
     * counter by one.
     *
     * @param int
     */
    public function setAuthAttempts( $i );

    /**
     * Increment the failed authentication counter.
     *
     * You should implement this in a transactin safe manner.
     *
     * @return int The new auth attempt counter value
     */
    public function incrementAuthAttempts();

}
