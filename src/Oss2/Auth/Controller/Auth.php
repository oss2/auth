<?php

namespace Oss2\Auth\Controller;

use \App;
use \Config;
use \Event;
use \Input;
use \Response;

/**
 * Oss2/Auth
 *
 * Generic authentication controller.
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
class Auth extends \Controller
{
    /** @var string The last username for which an autAttempt() was called for */
    private $lastUsername = null;

    /**
     * Perform an \Auth::attempt()
     *
     * We allow a lot of things to be configured. As such, this function resolves
     * those options and performs an \Auth::attempt();
     *
     * @return bool
     */
    private function authAttempt( $input )
    {
        $inputUsername = Config::get('oss2/auth::inputParamNames.username', 'username' );
        $inputPassword = Config::get('oss2/auth::inputParamNames.password', 'password' );
        $inputRemember = Config::get('oss2/auth::inputParamNames.remember', 'remember' );

        $this->lastUsername = $username = isset( $input[ $inputUsername ] ) ? $input[ $inputUsername ] : null;
        $password = isset( $input[ $inputPassword ] ) ? $input[ $inputPassword ] : null;
        $remember = isset( $input[ $inputRemember ] ) ? $input[ $inputRemember ] : false;

        $credentialUsername = Config::get('oss2/auth::credentialParamNames.username', 'username' );
        $credentialPassword = Config::get('oss2/auth::credentialParamNames.password', 'password' );

        return \Auth::attempt( [ $credentialUsername => $username, $credentialPassword => $password ], $remember, true );
    }

    /**
     * Send a login request.
     *
     * Required parameters:
     *
     * * `username` => the user's username (parameter name configurable)
     * * `password` => the user's password (parameter name configurable)
     *
     * Optional parameters:
     *
     * *`remember` => non-false if the user wants the session remembered (parameter name configurable)
     *
     */
    public function postIndex()
    {
        if( !$this->authAttempt( \Input::all() ) ) {
            $this->log( 'Failed login for username: ' . $this->lastUsername, 'notice' );
            \Auth::persist();
            App::abort(403, 'Unauthorized action.');
        }

        $this->log( 'Login successful for ' . \Auth::user()->getAuthIdentifier() . '/' . $this->lastUsername );
        \Auth::persist();
        return Response::json( \Auth::user()->getAuthResponse() );
    }

    /**
     * A wrapper to the \Log facade to determine if logged is enabled or disabled
     * in this package.
     *
     * @param string $msg The message
     * @param string $pri The log priority
     */
    private function log( $msg, $pri = 'info' )
    {
        if( Config::get('oss2/auth:log', true ) )
            \Log::$pri( $msg );
    }
}
