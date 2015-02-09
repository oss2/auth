<?php namespace Oss2\Auth\User;

/**
 * Oss2/Auth
 *
 * Class to represent a user's API Key for Laravel authentication.
 *
 * The only purpose of this class is for testing the authentication system. See the
 * tests directory in this package.
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
class FixedApiKey implements \Oss2\Auth\ApiKeyInterface
{
    /** @var Unique identifier */
    public $id = null;

    /** @var User ID */
    public $user_id = null;

    /** @var API Key */
    public $api_key = null;

    /** @var Expires at (\DateTime) */
    public $expires = null;

    /** @var Allowed IPs */
    public $allowed_ips = null;

    /** @var Allowed routes */
    public $allowed_routes = null;

    /** @var Created */
    public $created = null;





}
