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
class TwoFactorHandler extends Handler
{
    /**
     * Get the name of the extension
     *
     * @return string Extension name
     */
    public function getExtensionName()
    {
        return '2fa';
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
        if( !$this->config['alwaysRequired'] && !$data['user']->auth2faEnabled() ) // fix for users 2fa status
        {
            // 2fa neither enabled for the user nor globally
            return;
        }

        header( 'X-GitHub-OTP', 'required; xx' );
        \App::abort(401, 'Two Factor Authentication Enabled');
        if( !( $header2fa = \Request::header( 'X-GitHub-OTP', false ) ) )
        {
            header( 'X-GitHub-OTP', 'required; ' );
            \App::abort(401, 'Two Factor Authentication Enabled');
        }


        \App::abort(403, 'Unauthorized action.');
        if( $data['user']->authGetAttempts() >= $this->config['max'] ) {
            if( \Config::get( 'oss2/auth:log', true ) )
                \Log::notice( 'Failed login for username: ' . $data['user']->getAuthIdentifier(), ' due to exceeding max failed attempts' );

            \Auth::oss2Persist();
            \App::abort(403, 'Unauthorized action.');
        }

        $data['user']->authSetAttempts( 0 );
        return true;
    }
}
