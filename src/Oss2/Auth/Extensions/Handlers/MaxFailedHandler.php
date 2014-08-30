<?php namespace Oss2\Auth\Extensions\Handlers;

/**
 * Oss2/Auth
 *
 * Handler fo the MaxFailed extension
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
class MaxFailedHandler extends Handler
{
    /**
     * Get the name of the extension
     *
     * @return string Extension name
     */
    public function getExtensionName()
    {
        return 'maxFailed';
    }

    /**
     * Increment the failed attempts counter.
     *
     * Also fires a `oss2/auth.extension.max-failed.locked` event which could be
     * used, for example, to alert the user by email that their account was locked.
     */
    public function handleCredentialsInvalid( $data )
    {
        if( !isset( $data['user'] ) || !is_object( $data['user'] ) )
            return true;

        $attempts = $data['user']->authIncrementAttempts();

        if( $attempts == $this->config['max'] ) {
            \Event::fire( 'oss2/auth.extension.max-failed.locked', [ [ 'user' => $data['user'] ] ] );
        }

        return true;
    }

    /**
     * Check the failed attempts counter on a valid login.
     *
     * Aborts with a 403 error if the account has been locked. Note that this
     * generic abort does not leak whether the username is correct or not.
     *
     * Also resets the users count to zero on a seccessful login.
     */
    public function handleCredentialsValid( $data )
    {
        if( !isset( $data['user'] ) || !is_object( $data['user'] ) )
            return true;

        if( $data['user']->authGetAttempts() >= $this->config['max'] ) {
            if( \Config::get( 'oss2/auth:log', true ) )
                \Log::notice( 'Failed login for username: ' . $data['user']->getAuthIdentifier(), ' due to exceeding max failed attempts' );

            \Auth::oss2Persist();
            \App::abort(403, 'Unauthorized action.');
        }

        $data['user']->authSetAttempts( 0 );
        return true;
    }

    /**
     * Hand the password reset event to set the counter to zero
     */
    public function handlePasswordReset( $data )
    {
       if( !isset( $data['user'] ) || !is_object( $data['user'] ) )
           return true;

       $data['user']->authSetAttempts( 0 );
       return true;
    }
}
