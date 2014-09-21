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


    public function __construct() {
        App::error( function( \Oss2\Auth\Validation\Exception $exception ) {
            return Response::json( [ 'errors' => $exception->getApiErrors() ], 422 );
        });
    }


    /**
     * Filter the input and validate. Called by each action.
     *
     * @param string $action The action (matching the configuration file section)
     * @return array The filetred and validated parameters
     * @throws \Oss2\Auth\Validation\Exception
     */
    private function filterAndValidateFor( $action )
    {
        $params = \Input::only( \Config::get( "oss2/auth::{$action}.paramFilter" ) );
        $rules  = \Config::get( "oss2/auth::{$action}.paramRules" );

        App::make(
                \Config::get( "oss2/auth::{$action}.validator", '\Oss2\Auth\Validation\DefaultValidator' ), [ $params, $rules ]
            )->validate();

        return $params;
    }

    /**
     * Get credentials from request
     *
     * We allow a lot of things to be configured. As such, this function resolves
     * those options and returns an array
     *
     * @return array
     */
    private function resolveCredentials( $input )
    {
        $inputUsername = Config::get('oss2/auth::inputParamNames.username', 'username' );
        $inputPassword = Config::get('oss2/auth::inputParamNames.password', 'password' );

        $this->lastUsername = $username = isset( $input[ $inputUsername ] ) ? $input[ $inputUsername ] : null;
        $password = isset( $input[ $inputPassword ] ) ? $input[ $inputPassword ] : null;

        $credentialUsername = Config::get('oss2/auth::credentialParamNames.username', 'username' );
        $credentialPassword = Config::get('oss2/auth::credentialParamNames.password', 'password' );

        return [ $credentialUsername => $username, $credentialPassword => $password ];
    }

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
        $inputRemember = Config::get('oss2/auth::inputParamNames.remember', 'remember' );
        $remember = isset( $input[ $inputRemember ] ) ? $input[ $inputRemember ] : false;

        return \Auth::attempt( $this->resolveCredentials( $input ), $remember, true );
    }

    /**
     * Wrapper to ensure we call persist() on a response
     */
    private function sendResponse( $response )
    {
        \Auth::oss2Persist();
        return $response;
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
        $params = $this->filterAndValidateFor( 'login' );

        if( !$this->authAttempt( $params ) ) {
            \Event::fire( 'oss2/auth::auth_failed', $params );
            $this->log( 'Failed login for username: ' . $this->lastUsername, 'notice' );
            return $this->sendResponse( Response::make('',403) );
        }

        // 2FA enabled?
        if( \Config::get( 'oss2/auth::login.2fa_enabled' ) && $twoFA = \Auth::user()->authGet2FA() ) {

            $token = $this->randomToken( 30 );
            $handler2fa = App::make( $twoFA )->init( \Auth::user(), $token );

            \Auth::user()->authAddToken( 'oss2/auth.2fa.tokens', $token,
                strtotime( \Config::get( 'oss2/auth::login.2faTokenLifetime', '+10 minutes' ) ), 20
            );

            $this->log( 'Login redirect to 2FA for ' . \Auth::user()->getAuthIdentifier() . '/' . $this->lastUsername );
            \Auth::logout();
            return $this->sendResponse( Response::json( [ 'username' => $this->lastUsername, 'twofatoken' => $token,
                'url' => action( 'Oss2\Auth\Controller\Auth@postLogin2fa') ], 300 ) );
        }

        $this->log( 'Login successful for ' . \Auth::user()->getAuthIdentifier() . '/' . $this->lastUsername );
        return $this->sendResponse( Response::json( \Auth::user()->authGetResponse() ) );
    }

    /**
     * Alias for login
     */
    public function postLogin()
    {
        return $this->postIndex();
    }

    public function getLogout()
    {
        if( \Auth::check() ) {
            $this->log( 'Logout for ' . \Auth::user()->getAuthIdentifier() );
            \Auth::oss2Persist();
        }

        \Auth::logout();
        return Response::make('',204);
    }

    /**
     * If 2FA is enabled, this performs the second stage.
     *
     * In stage one,  a 2fa token was generated with a short lifetime. To have a
     * successful login, we need to get this code back as well as the 2FA code.
     */
    public function postLogin2fa()
    {
        $params = $this->filterAndValidateFor( 'login-2fa' );

        \Event::fire( 'oss2/auth::pre_credentials_lookup', $params );
        $user = \Auth::getProvider()->retrieveByCredentials( array_intersect_key( $params, array_flip( \Config::get( 'oss2/auth::login-2fa.paramsForLookup' ) ) ) );

        if( !$user || !$user->authValidateToken( 'oss2/auth.2fa.tokens', $params['twofatoken'], true ) ) {
            \Event::fire( 'oss2/auth::2fa_auth_failed_2fatoken', $params );
            $this->log( '2FA login request with invalid username / token: ' . implode( '|', $params ) );
            return $this->sendResponse( Response::make('',403) );
        }

        $this->log( '2FA login request with valid credentials for: ' . $user->getAuthIdentifier() );

        if( App::make( $user->authGet2FA() )->verify( $user, $params['token'] ) ) {
            \Auth::login( $user );
            $this->log( 'Login successful for ' . \Auth::user()->getAuthIdentifier() . '/' . $this->lastUsername );
            return $this->sendResponse( Response::json( \Auth::user()->authGetResponse() ) );
        }

        \Event::fire( 'oss2/auth::2fa_auth_failed_token', $params );
        $this->log( '2FA login failed - bad token - username: ' . $this->lastUsername, 'notice' );
        return $this->sendResponse( Response::make('',403) );
    }


    public function postSendResetToken()
    {
        $params = $this->filterAndValidateFor( 'send-reset-token' );

        \Event::fire( 'oss2/auth::pre_credentials_lookup', $params );
        $user = \Auth::getProvider()->retrieveByCredentials( $params );

        if( !$user ) {
            $this->log( 'Send reset token request with invalid username: ' . implode( '|', $params ) );
            return $this->sendResponse( Response::make('',\Config::get('oss2/auth::send-reset-token.invalidCredentialsResponse', 204)) );
        }

        $this->log( 'Send reset token request with valid credentials for: ' . $user->getAuthIdentifier() );

        $token = $this->randomToken( 20 );

        $user->authAddToken( 'oss2/auth.password-reset.tokens', $token,
            strtotime( \Config::get( 'oss2/auth::send-reset-token.tokenLifetime', '+2 days' ) ),
            \Config::get( 'oss2/auth::send-reset-token.maxTokens', 5 )
        );

        App::make( 'Oss2\Auth\Handlers\SendResetTokenHandler' )->handle( $user, [ 'token' => $token, 'params' => $params ] );

        return $this->sendResponse( Response::make('',204) );
    }

    //public function getReset() {}

    public function postReset()
    {
        $params = $this->filterAndValidateFor( 'reset' );

        \Event::fire( 'oss2/auth::pre_credentials_lookup', $params );
        $user = \Auth::getProvider()->retrieveByCredentials( array_intersect_key( $params, array_flip( \Config::get( 'oss2/auth::reset.paramsForLookup' ) ) ) );

        if( !$user || !$user->authValidateToken( 'oss2/auth.password-reset.tokens', $params['token'], true ) ) {
            $this->log( 'Reset token request with invalid username / token: ' . implode( '|', $params ) );
            return $this->sendResponse( Response::make('',403) );
        }

        $this->log( 'Reset token request with valid credentials for: ' . $user->getAuthIdentifier() );
        $user->setAuthPassword( \Hash::make( $params['password'] ) );
        \Auth::oss2Persist(); // persist before handler so the handler **knows** the password was reset

        App::make( 'Oss2\Auth\Handlers\ResetHandler' )->handle( $user, [ 'params' => $params ] );

        return Response::make('',204);
    }

    public function postFindUsernames()
    {
        $params = $this->filterAndValidateFor( 'find-usernames' );

        \Event::fire( 'oss2/auth::pre_credentials_lookup', $params );
        $users = \Auth::getProvider()->retrieveByCredentials( $params, false );   // return an ARRAY of possible users!

        if( !count( $users ) ) {
            $this->log( 'Find usernames request with no results: ' . implode( '|', $params ) );
            return $this->sendResponse( Response::make('',\Config::get('oss2/auth::find-usernames.invalidCredentialsResponse', 204)) );
        }

        $this->log( 'Find usernames request with valid credentials: ' . implode( '|', $params ) );

        App::make( 'Oss2\Auth\Handlers\FindUsernamesHandler' )->handle( null, [ 'users' => $users, 'params' => $params ] );

        return Response::make('',204);
    }


    /**
     * Generate a random token (without confusing letters / numbers)
     * @param int $len Length of token
     * @return string The random token
     */
    private function randomToken( $len = 20 )
    {
        $str = 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $repeat = ceil( ( 1 + ( $len / strlen( $str ) ) ) );
        return substr( str_shuffle( str_repeat( $str, $repeat ) ), 0, $len );
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
