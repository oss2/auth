<?php namespace Oss2\Auth\Providers;

use Illuminate\Hashing\HasherInterface;

/**
 * Oss2/Auth
 *
 * Class to provide an array of User\FixedUser objects for Laravel authentication.
 *
 * The only purpose of this class is for testing the authentication system. See the
 * tests directory in this package.
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
class FixedProvider implements \Oss2\Auth\UserProviderInterface
{
    /**
     * The hasher implementation.
     *
     * @var \Illuminate\Hashing\HasherInterface
     */
    protected $hasher;

    /**
     * The repository (array) containing the users.
     *
     * @var \Oss2\Auth\User\FixedUser[]
     */
    protected $array = [];


    /**
      * Create a new array user provider.
      *
      * @param  \Doctrine\ORM\EntityRepository       $d2repository The Doctrine2 repository (table) containing the users.
      * @param  \Illuminate\Hashing\HasherInterface  $hasher The hasher implementation
      * @return void
      */
    public function __construct( array $array, HasherInterface $hasher )
    {
        $this->array   = $array;
        $this->hasher  = $hasher;
    }

    /**
     * Set the repository contents
     * @param array $a
     */
    public function setArray( array $a )
    {
        return $this->array = $a;
    }

    /**
     * Get the repository contents
     * @return array
     */
    public function getArray()
    {
        return $this->array;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \Oss2\Auth\UserProviderInterface|null
     */
    public function retrieveById( $identifier )
    {
        return isset( $this->array[ $identifier ] ) ? $this->array[ $identifier ] : null;
    }


    /**
     * Retrieve a user by the given credentials.
     *
     * Note the parameter `$oneOnly` - your code should ensure that sufficient
     * *credentials* are used to **ensure** a single user result. E.g. unique
     * index on a username column.
     *
     * However, for the `find-usernames` function, you may accept, for example,
     * an email for which a user has multiple accounts. In this case, call the
     * function **expecting** an array response.
     *
     * If $oneOnly is false, you **must** return an array response (even an empty one)
     *
     * @param  array  $credentials
     * @param  bool   $oneOnly Only return one user (defaults to true)
     * @return \Oss2\Auth\UserProviderInterface|array|null
     */
    public function retrieveByCredentials( array $credentials, $oneOnly = true )
    {
        $filter = $this->array;

        foreach( $credentials as $k => $v ) {

            if( $k == 'password' ) continue;

            foreach( $filter as $id => $user ) {
                if( !isset( $user->$k ) || $user->$k != $v ) {
                    unset( $filter[$id] );
                    continue;
                }
            }
        }

        if( !$oneOnly )
            return is_array( $filter ) ? $filter : [];

        return count( $filter ) ? array_shift( $filter ) : null;
    }

    /**
     * Get the name of a individual user class
     * @return string
     */
    public function authGetUserClassName()
    {
        return '\Oss2\Auth\User\FixedUser';
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Oss2\Auth\UserProviderInterface  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials( \Illuminate\Auth\UserInterface $user, array $credentials )
    {
        return $this->hasher->check( $credentials['password'], $user->getAuthPassword() );
    }


    /**
     * Retrieve a user by their unique "remember me" token.
     *
     * @param mixed $identifier
     * @param string $token
     * @return \Oss2\Auth\UserProviderInterface|null
     */
    public function retrieveByToken( $identifier, $token )
    {
        if( isset( $this->array[ $identifier ] ) && $this->array[ $identifier ]->token == $token )
            return $this->array[ $identifier ];

        return null;
    }


    /**
     * Updates the "remember me" token for the given user in storage.
     *
     * @param \Oss2\Auth\UserProviderInterface $user
     * @param string $token
     * @return void
     */
    public function updateRememberToken( \Illuminate\Auth\UserInterface $user, $token )
    {
        $user->setRememberToken( $token );
    }

    /**
     * Save a user object
     */
    public function authPersist( \Oss2\Auth\UserInterface $user )
    {}

}
