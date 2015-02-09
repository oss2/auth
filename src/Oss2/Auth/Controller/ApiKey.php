<?php

namespace Oss2\Auth\Controller;

use \App;
use \Config;
use \Event;
use \Input;
use \Response;

/**
 * Oss2/Auth
 *
 * Generic authentication controller.
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
class ApiKey extends \Oss2\Auth\Controller
{

    /**
     * Create an API key for the user.
     *
     * API keys take a number of parameters (some optional):
     *
     * * `expires` - when the API key expires. Integer to represent number of seconds from 'now'.
     * * `allowed_ips` - JSON array of IP addresses / networks in CIDR format. v4 and v6 supported.
     *   If not set, all IP addresses are allowed.
     * * `allowed_routes` - JSON array of regex strings of allowed routes that this API key can access.
     *   If not set, all routes are accessible.
     *
     *
     */
    public function postCreate()
    {
        // make sure we're logged in
        if( !\Auth::check() )
            return Response::make( '', 404 );

        $params = $this->filterAndValidateFor( 'api-key/create' );

        // FIXME process IP addresses and routes
        

    }

}
