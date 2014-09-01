<?php namespace Oss2\Auth\Handlers;

/**
 * Oss2/Auth
 *
 * Handler for password reset events - e.g. to send an email confirmation.
 *
 * Create your own to provide your own functionality and bind as:
 *
 * App::bind( 'Oss2\Auth\Handlers\ResetHandler', 'Your\Namespace\Auth\Handlers\ResetHandler' );
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
class ResetHandler
{
    /**
     * Just throw an expection for testing
     */
    public function handle( \Oss2\Auth\UserInterface $user, $data = null ) {
    }
}
