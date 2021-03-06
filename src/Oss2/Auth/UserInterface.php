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
     * set the password for the user.
     *
     * @param string $hashedPassword
     */
    public function setAuthPassword( $hashedPassword );

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
    public function authGetResponse();


    /**
     * Add an authentication token to the user.
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
    public function authAddToken( $name, $token, $expires = 0, $max = 0 );

    /**
     * Get an indexed preference for the user.
     *
     * We need to store preferences / tokens for some features such as password reset.
     * For this, we need the user entiity to allow the fetching of indexed preferences:
     *
     * @param string $name    The name of the indexed preference. E.g. `oss2/auth.password-reset.tokens`
     * @return array The indexed preferences
     */
    public function authGetTokens( $name );


    /**
     * Validate a token for the user.
     *
     * @param string $name         The name of the indexed preference. E.g. `oss2/auth.password-reset.tokens`
     * @param string $token        The token
     * @param bool   $clearIfValid If true (default), clear the tokens if a valid one is presented
     * @return bool
     */
    public function authValidateToken( $name, $token, $clearIfValid = true );

    /**
     * Clear an indexed preference to the user.
     *
     * @param string $name    The name of the indexed preference. E.g. `oss2/auth.password-reset.tokens`
     */
    public function authClearTokens( $name );

    /**
     * Get the user's email address so that reset tokens and other communication cab
     * be sent to him/her.
     *
     * @return string
     */
    public function authGetEmail();

    /**
     * Get the user's fullname (can return an empty string if you wish)
     *
     * @return string
     */
    public function authGetFullname();


    /**
     * Check if the user has two-factor authentication (2FA) enabled.
     *
     * Returns with false for no 2FA, otherwise the 2FA handler name
     *
     * @return string|bool
     */
    public function authGet2FA();


}
