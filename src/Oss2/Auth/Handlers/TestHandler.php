<?php namespace Oss2\Auth\Handlers;

/**
 * Handler to throw exceptions during testing.
 *
 * Used to ensures handlers are called.
 *
 */
class TestHandler
{
    /**
     */
    public function handle( $user, $data = null ) {
        if( \App::runningUnitTests() )
            throw new \Oss2\Auth\Handlers\TestException( 'Test exception to ensure handler is invoked!', $user, $data );
    }
}
