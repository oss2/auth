<?php namespace Oss2\Auth\TwoFactorAuthentication;

/**
 * Oss2/Auth
 *
 * 2FA interface
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.md.
 *
 * @category   Authentication
 * @package    Oss2\Auth\TwoFactorAuthentication
 * @copyright  Copyright (c) 2014, Open Source Solutions Limited, Dublin, Ireland
 */

interface TwoFactorAuthenticationInterface
{
    /**
     * Initiate 2FA during a login attempt
     *
     * @param \Oss2\Auth\UserInterface $user
     * @return \Oss2\Auth\TwoFactorAuthenticatio\TwoFactorAuthenticationInterface
     */
    public function init( $user );

    /**
     * Verify a 2FA code for a given user
     *
     * @param \Oss2\Auth\UserInterface $user
     * @param string $token The 2FA code from the user
     * @return bool
     */
    public function verify( $user, $token );



}
