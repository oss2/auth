<?php namespace Oss2\Auth\Extensions;

/**
 * Oss2/Auth
 *
 * MaxFailed extension
 *
 * Extension to lock a user out after sequential failed logins.
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
class MaxFailedExtension extends Extension
{
    /** @var int The priority of the registered events */
    public $priority = 100;

    /** @var array The default and minimal configuration required. Overridden by the configuration file. */
    public static $DEFAULT_CONFIG = [
        'enabled'             => false,
        'handler'             => 'Oss2\\Auth\\Extensions\\Handlers\\MaxFailedHandler',
        'retrieved_function'  => 'handleCredentialsInvalid',
        'valid_function'      => 'handleCredentialsValid',
        'reset_function'      => 'handlePasswordReset',
        'max'                 => 5
    ];

    /**
     * This extension just registers event listeners:
     */
    public function __construct( array $config = [], $priority = 100 )
    {
        parent::__construct( self::$DEFAULT_CONFIG, $config, $priority );

        \Event::listen( 'oss2/auth::credentials_invalid',
            $this->config['handler'] . '@' . $this->config['retrieved_function'],
            $this->priority
        );

        \Event::listen( 'oss2/auth::credentials_valid',
            $this->config['handler'] . '@' . $this->config['valid_function'],
            $this->priority
        );

        \Event::listen( 'oss2/auth::password_reset',
            $this->config['handler'] . '@' . $this->config['reset_function'],
            $this->priority
        );
    }

    /**
     * Enforce use of our interface to provide access to get and set the failed
     * login counts.
     *
     * @return array Names of required interface(s) 
     */

    public static function mustImplement()
    {
        return [ 'Oss2\Auth\Extensions\Interfaces\MaxFailedUserInterface' ];
    }

}
