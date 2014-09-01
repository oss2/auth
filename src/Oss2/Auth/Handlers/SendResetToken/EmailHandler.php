<?php namespace Oss2\Auth\Handlers\SendResetToken;

/**
 * Oss2/Auth
 *
 * Handler to send email reset tokens.
 *
 * Override this to provide your own functionality.
 *
 * Code below shows how to send an email with an included template.
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
class EmailHandler
{
    /**
     * Email the token
     *
     * Activate via:
     *
     *     App::bind( 'Oss2\Auth\Handlers\SendResetTokenHandler', 'Oss2\Auth\Handlers\SendResetToken\EmailHandler' );
     */
    public function handle( \Oss2\Auth\UserInterface $user, $data = null ) {
        // You could send an email as follows:
        \Mail::send(
            'oss2/auth::send-reset-token.email',
            [ 'user' => $user, 'token' => $data['token'] ],
            function( $message ) use ( $user ) {
                $message->to( $user->authGetEmail(), $user->authGetFullname() )
                    ->subject( 'Your password reset token' );
            }
        );
    }
}
