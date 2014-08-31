<?php namespace Oss2\Auth\Handlers;

/**
 * Handler to (for example) send a password reset confirmation email.
 *
 * Override this to provide your own functionality.
 *
 */
class ResetHandler
{
    /**
     * Just throw an expection for testing
     */
    public function handle( \Oss2\Auth\UserInterface $user, $data = null ) {
    }
}
