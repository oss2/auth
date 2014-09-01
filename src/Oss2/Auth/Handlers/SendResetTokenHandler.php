<?php namespace Oss2\Auth\Handlers;

/**
 * Oss2/Auth
 *
 * Handler for password reset token requests - e.g. to send by email.
 *
 * Create your own to provide your own functionality and bind as:
 *
 * App::bind( 'Oss2\Auth\Handlers\SendResetTokenHandler', 'Your\Namespace\Auth\Handlers\SendResetTokenHandler' );
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
class SendResetTokenHandler
{
    /**
     * Just log the code by default.
     */
    public function handle( \Oss2\Auth\UserInterface $user, $data = null ) {
    }
}
