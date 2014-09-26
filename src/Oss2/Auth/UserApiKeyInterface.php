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

interface UserApiKeyInterface extends \Illuminate\Auth\UserInterface
{
    /**
     * Create an API key
     *
     * @param int $expires Seconds from now for when this key should expire
     * @param array $allowed_ips Allowed IP addresses
     * @param array $allowed_routes Routes that this API key is allowed to access
     * @return \Oss2\Auth\ApiKeyInterface
     */
    public function apiKeyCreate( $expires = null, $allowed_ips = null, $allowed_routes = null );
}
