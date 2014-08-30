<?php namespace Oss2\Auth\Handlers;

/**
 * Handler to send email reset tokens.
 *
 * Override this to provide your own functionality.
 *
 * Commented code below shows how to send an email with an included template.
 */
class SendResetTokenHandler
{
    /**
     * Just log the code by default.
     */
    public function handle( \Oss2\Auth\UserInterface $user, $token, $data = null ) {
        \Log::info( 'oss2/auth reset token for ' . $user->getAuthIdentifier() . ': ' . $token );
    }
}
