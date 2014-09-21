<?php

namespace Oss2\Auth;

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
abstract class Controller extends \Controller
{
    /** @var string The last username for which an autAttempt() was called for */
    protected $lastUsername = null;

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
    protected function filterAndValidateFor( $action )
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
    protected function resolveCredentials( $input )
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
    protected function authAttempt( $input )
    {
        $inputRemember = Config::get('oss2/auth::inputParamNames.remember', 'remember' );
        $remember = isset( $input[ $inputRemember ] ) ? $input[ $inputRemember ] : false;

        return \Auth::attempt( $this->resolveCredentials( $input ), $remember, true );
    }

    /**
     * Wrapper to ensure we call persist() on a response
     */
    protected function sendResponse( $response )
    {
        \Auth::oss2Persist();
        return $response;
    }

    /**
     * Generate a random token (without confusing letters / numbers)
     * @param int $len Length of token
     * @return string The random token
     */
    protected function randomToken( $len = 20 )
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
    protected function log( $msg, $pri = 'info' )
    {
        if( Config::get('oss2/auth:log', true ) )
            \Log::$pri( $msg );
    }

}
