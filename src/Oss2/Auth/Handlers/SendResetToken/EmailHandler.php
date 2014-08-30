<?php namespace Oss2\Auth\Handlers\SendResetToken;

/**
 * Handler to send email reset tokens.
 *
 * Override this to provide your own functionality.
 *
 * Commented code below shows how to send an email with an included template.
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
    public function handle( \Oss2\Auth\UserInterface $user, $token, $data = null ) {
        // You could send an email as follows:
        \Mail::send( 'oss2/auth::send-reset-token.email', [ 'user' => $user, 'token' => $token ], function( $message ) use ( $user ){
            $message->to( $user->authGetEmail(), $user->authGetFullname() )
                ->subject( 'Your password reset token' );
        });
    }
}
