<?php namespace Oss2\Auth;

/**
 * Oss2/Auth
 *
 * User provider interface
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

interface UserInterface extends \Illuminate\Auth\UserInterface
{
    /**
     * On a successful login, the controller returns a 200 response with
     * a JSON document. You can use this function to return a custom
     * array. But, at the very least, it MUST return:
     *
     *     [
     *         'user': [
     *             'authIdentifier' => $this->getAuthIdentifier()
     *         ]
     *     ]
     *
     * The returned array is encoded to JSON.
     *
     * @return array
     */
    public function getAuthResponse();


    /**
     * Add an indexed preference to the user.
     *
     * We need to store preferences / tokens for some features such as password reset.
     * For this, we need the user entiity to allow the storing of indexed preferences:
     *
     * Implementations can decide themselves if they implement expiry and max. Note
     * that without max you expose yourself to a DOS attack.
     *
     * @param string $name    The name of the indexed preference. E.g. `oss2/auth.password-reset.tokens`
     * @param string $token   The 'item' to store in string representation
     * @param int    $expires The UNIX timestamp of when this preference should expire.
     * @param int    $max     The maximum number of such preferences
     * @return bool Boolean to indicate if the operation was successful or not.
     */
    public function addIndexedPreference( $name, $token, $expires = 0, $max = 0 );

    /**
     * Get an indexed preference for the user.
     *
     * We need to store preferences / tokens for some features such as password reset.
     * For this, we need the user entiity to allow the fetching of indexed preferences:
     *
     * @param string $name    The name of the indexed preference. E.g. `oss2/auth.password-reset.tokens`
     * @return array The indexed preferences
     */
    public function getIndexedPreference( $name );


}
