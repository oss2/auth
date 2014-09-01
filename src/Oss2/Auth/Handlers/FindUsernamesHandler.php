<?php namespace Oss2\Auth\Handlers;

/**
 * Oss2/Auth
 *
 * Handler for find usernames requests - e.g. to send an email with usernames.
 *
 * Create your own to provide your own functionality and bind as:
 *
 * App::bind( 'Oss2\Auth\Handlers\FindUsernamesHandler', 'Your\Namespace\Auth\Handlers\FindUsernamesHandler' );
 *
 * Alternativiely, register event listeners.
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
class FindUsernamesHandler
{
    /**
     */
    public function handle( $user, $data = null ) {
    }
}
