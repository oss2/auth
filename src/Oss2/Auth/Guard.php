<?php namespace Oss2\Auth;

/**
 * Oss2/Auth
 *
 * Extend Laravel's own guard class with additional functionality.
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
class Guard extends \Illuminate\Auth\Guard
{

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * Copied from Laravel but overridden as we want to fire additional events:
     *
     * * `oss2/auth::pre_credentials_lookup` - fired before we lookup credentials. Primary
     *       use case is for login / reset throttling.
     * * `oss2/auth::credentials_retrieved` - fired when a called has been made to the user
     *       provider's `retrieveByCredentials()` method. Passes an array containing the
     *       original `credentials` and a user object (or null) `user`.
     * * `oss2/auth::credentials_valid` - fired if the credentials were valid. As
     *       `credentials_retrieved` above but will have a user object.
     * * `'oss2/auth::credentials_invalid` - first if the credentials were invalid. As
     *       above but will only have a `user` object if valid search parameters were
     *       provided (i.e. a valid username).
     *
     * @param  array  $credentials
     * @param  bool   $remember
     * @param  bool   $login
     * @return bool
     */
    public function attempt(array $credentials = array(), $remember = false, $login = true)
    {
        $this->fireAttemptEvent($credentials, $remember, $login);

        \Event::fire( 'oss2/auth::pre_credentials_lookup', $credentials );

        $this->lastAttempted = $user = $this->provider->retrieveByCredentials($credentials);

        \Event::fire( 'oss2/auth::credentials_retrieved', [ [ 'credentials' => $credentials, 'user' => $user ] ] );

        // If an implementation of UserInterface was returned, we'll ask the provider
        // to validate the user against the given credentials, and if they are in
        // fact valid we'll log the users into the application and return true.
        if ($this->hasValidCredentials($user, $credentials))
        {
            \Event::fire( 'oss2/auth::credentials_valid', [ [ 'credentials' => $credentials, 'user' => $user ] ] );
            if ($login) $this->login($user, $remember);

            return true;
        }

        \Event::fire( 'oss2/auth::credentials_invalid', [ [ 'credentials' => $credentials, 'user' => $user ] ] );
        return false;
    }


    /** @var array Collection of registered authentication extensions */
    private $oss2Extensions = [];

    /**
     * Add a new extension with a name and a configuration. The configuration must contain
     * keys as follows:
     *
     * * `enabled` - which must be set to true to the extension to be loaded
     * * `class`   - the extension class to instaniate (see `Oss2\Auth\Extensions\Extension`)
     *
     * The named class will be instaniated and the config passed to its constructor.
     *
     * @param string $name
     * @param array $config
     * @return \Illuminate\Support\Collection
     */
    public function oss2AddExtension( $name, $config )
    {
        if( isset( $this->oss2Extensions[ $name ] ) )
            return $this->oss2Extensions;

        if( !isset( $config['enabled'] ) || !$config['enabled'] )
            return $this->oss2Extensions;

        if( !isset( $config['class'] ) || !class_exists( $config['class'] ) )
            throw new \Oss2\Auth\Extensions\Exception( 'No extension class defined or found for: ' . $name );

        foreach( $config['class']::mustImplement() as $interface ) {
            if( !in_array( $interface, class_implements( $this->provider->authGetUserClassName() ) ) )
                throw new \Oss2\Auth\Extensions\Exception( "To use this auth extension ({$name}), your user class must implement: {$interface}" );
        }

        $this->oss2Extensions[ $name ] = new $config['class']( $config );
        return $this->oss2Extensions;
    }

    /**
     * Get a named extension
     * @return \Oss2\Auth\Extensions\Extension
     */
    public function oss2GetExtension( $name )
    {
        return isset( $this->oss2Extensions[ $name ] ) ? $this->oss2Extensions[ $name ] : null;
    }

    /**
     * Persist the user (last attempted) to persistant storage.
     *
     * I.e. do a database UPDATE for example.
     */
    public function oss2Persist()
    {
        if( $this->lastAttempted )
            $this->provider->authPersist( $this->lastAttempted );
    }
}
