<?php namespace Oss2\Auth;

/**
 * Oss2/Auth
 *
 * API Key interface
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

interface ApiKeyInterface
{
    /**
     * Get the unique ID of the API key
     *
     * This is typically the auto-incremeting primary key
     *
     * @return int The primary key
     */
    public function getId();



    /**
     * Get the API key element. Note this is not what is shared externally.
     * See getExternalApiKey() for that.
     *
     * @return string The API key element.
     */
    public function getKey();

    /**
     * Set the API key element.
     *
     * @param string $key The API key element
     * @return ApiKeyInterface (for fluent coding)
     */
    public function setKey( $key );


    /**
     * Get the expiry \DateTime.
     *
     * @return null|\DateTime When the key expires (or null for never)
     */
    public function getExpiresAt();

    /**
     * Set the expiry \DateTime.
     *
     * @param \DateTime|null $expires The \DateTime of expiry (null for never)
     * @return ApiKeyInterface (for fluent coding)
     */
    public function setExpiresAt( $expires = null );

    /**
     * Check is the key has expired
     * @return bool
     */
    public function isExpired();

    /**
     * Get the array of allowed IPs
     *
     * @return null|string[] IP addresses allowed to access with this API key
     */
    public function getAllowedIps();

    /**
     * Set the allowed IP addresses
     *
     * @param string[]|null $ips Array of IP addresses allowed
     * @return ApiKeyInterface (for fluent coding)
     */
    public function setAllowedIps( $ips = null );

    public function isIpAllowed( $ip );

    /** @var Allowed routes */
    public $allowed_routes = null;

    /** @var Created */
    public $created = null;




}
