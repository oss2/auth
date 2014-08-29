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
abstract class Extension
{
    /** @var int The priority of the registered events */
    protected $priority = 100;

    /** @var array The default and minimal configuration required. Overridden by the configuration file. */
    protected $config = [
        'enabled'             => false
    ];

    /**
     * Construct the extension
     *
     * @param array $defaultConfig The extensions default configuration which contains all essential values
     * @param array $config The configuration options added / overridden by the user configuration file
     * @param int $priority The registered events' priorities
     */
    public function __construct( array $defaultConfig, array $config, $priority )
    {
        if( is_array( $defaultConfig ) )
            $this->config = array_replace( $this->config, $defaultConfig );

        if( is_array( $config ) )
            $this->config = array_replace( $this->config, $config );

        if( !$this->getConfig( 'enabled' ) )
            throw new \Oss2\Auth\Extensions\Exception( 'Instantiated extension that is disabled!!' );

        $this->setPriority( $priority );
    }

    /**
     * Override to enforce use of an interface(s).
     *
     * If your extension relies on new fucntionality / methods in the user objects, define an
     * interface and return its name as a string in this array.
     *
     * @return array Names of required interface(s) 
     */
    public static function mustImplement()
    {
        return [];
    }

    /**
     * Get a configuration option (or the entire array)
     *
     * @param string|null $key If set, a specific config option. Otherwise the entire array.
     * @return mixed
     */
    public function getConfig( $key = null )
    {
        if( $key == null )
            return $this->config;

        return isset( $this->config[ $key ] ) ? $this->config[ $key ] : null;
    }

    /**
     * Set a configuration option (or the entire array)
     *
     * @param string|null $key If set, a specific config option. Otherwise the entire array.
     * @param mixed $value The new option(s)
     */
    public function setConfig( $key = null, $value )
    {
        if( $key == null )
            $this->config = $value;

        $this->config[ $key ] = $value;
    }

    /**
     * Set the priority for events
     * @param int $p The priority
     */
    public function setPriority( $p )
    {
        $this->priority = $p;
    }

    /**
     * Get the priority for events
     * @return int The priority
     */
    public function getPriority()
    {
        return $this->priority;
    }

}
