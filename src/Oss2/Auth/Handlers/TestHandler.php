<?php namespace Oss2\Auth\Handlers;

/**
 * Handler to send email reset tokens.
 *
 * Override this to provide your own functionality.
 *
 * Commented code below shows how to send an email with an included template.
 */
class TestHandler
{
    /**
     */
    public function handle( \Oss2\Auth\UserInterface $user, $data = null ) {
        if( \App::runningUnitTests() )
            throw new \Oss2\Auth\Handlers\TestException( 'Test exception to ensure handler is invoked!' );
    }
}
