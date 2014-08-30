<?php namespace Oss2\Auth\User;

/**
 * Oss2/Auth
 *
 * Class to represent a user for Laravel authentication.
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
class FixedUser implements \Oss2\Auth\UserInterface, \Oss2\Auth\Extensions\Interfaces\MaxFailedUserInterface
{
    /** @var Unique identifier */
    public $id = null;

    /** @var Username */
    public $username = null;

    /** @var Password */
    public $password = null;

    /** @var Token */
    public $token = null;

    /** @var Email */
    public $email = null;

    /** @var Firstname */
    public $firstname = null;

    /** @var Surname */
    public $surname = null;

    /** @var AuthAttempts */
    public $authAttempts = 0;


	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
    {
        return $this->username;
    }

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
    {
        return $this->password;
    }

	/**
	 * Get the token value for the "remember me" session.
	 *
	 * @return string
	 */
	public function getRememberToken()
    {
        return $this->token;
    }

	/**
	 * Set the token value for the "remember me" session.
	 *
	 * @param  string  $value
	 * @return void
	 */
	public function setRememberToken($value)
    {
        $this->token = $value;
    }

	/**
	 * Get the column name for the "remember me" token.
	 *
	 * @return string
	 */
	public function getRememberTokenName()
    {
        return 'token';
    }

    /**
     * On a successful login, the controller returns a 200 response with
     * a JSON document. You can use this function to return a custom
     * array. But, at the very least, it MUST return:
     *
     *     [
     *         'user': [
     *             'authIdentifier' => $this->getAuthIdentifier()
     *         ]
     *     ]
     *
     * The returned array is encoded to JSON.
     *
     * @return array
     */
    public function authGetResponse()
    {
        return [
            'user' => [
                'authIdentifier' => $this->getAuthIdentifier()
            ]
        ];
    }


    /**
     * Return the current counter for sequential failed authentication attempts
     * @return int
     */
    public function authGetAttempts()
    {
        return $this->authAttempts;
    }

    /**
     * Set the counter for failed auth attempts.
     *
     * Usually just used to reset to zero. Use increment to increment the
     * counter by one.
     *
     * @param int
     */
    public function authIncrementAttempts()
    {
        return ++$this->authAttempts;
    }

    /**
     * Increment the failed authentication counter.
     *
     * You should implement this in a transactin safe manner.
     *
     * @return int The new auth attempt counter value
     */
    public function authSetAttempts( $i )
    {
        $this->authAttempts = $i;
    }


    /** @var array Indexed preferences for the user */
    private $tokens = [];

    /**
     * Tokens accessor - just for testing
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    /**
     * Tokens setter - just for testing
     */
    public function setTokens( $t )
    {
        $this->tokens = $t;
    }


    /**
     * Add an indexed preference to the user.
     *
     * We need to store preferences / tokens for some features such as password reset.
     * For this, we need the user entiity to allow the storing of indexed preferences:
     *
     * @param string $name    The name of the indexed preference. E.g. `oss2/auth.password-reset.tokens`
     * @param string $token   The 'item' to store in string representation
     * @param int    $expires The UNIX timestamp of when this preference should expire.
     * @param int    $max     The maximum number of such preferences
     * @return bool Boolean to indicate if the operation was successful or not.
     */
    public function authAddToken( $name, $token, $expires = 0, $max = 0 )
    {
        if( !isset( $this->tokens[ $name ] ) )
            $this->tokens[ $name ] = [];

        $this->authExpireTokens( $name );

        if( $max != 0 && count( $this->tokens[ $name ] ) >= $max )
            return false;

        if( $expires != 0 && $expires < time() )
            return false;

        $this->tokens[ $name ][] = [
            'value'  => $token,
            'expiry' => $expires
        ];

        return true;
    }

    /**
     * Expire a named indexed preference that has expired
     */
    public function authExpireTokens( $name )
    {
        if( !isset( $this->tokens[ $name ] ) )
            return;

        foreach( $this->tokens[ $name ] as $i => $p ) {
            if( isset( $p['expiry'] ) && $p['expiry'] != 0 && $p['expiry'] < time() )
                unset( $this->tokens[$name][$i] );
        }
    }

    /**
     * Get tokens for the user.
     *
     * We need to store preferences / tokens for some features such as password reset.
     * For this, we need the user entiity to allow the fetching of indexed preferences:
     *
     * @param string $name    The name of the indexed preference. E.g. `oss2/auth.password-reset.tokens`
     * @return array The indexed preferences
     */
    public function authGetTokens( $name )
    {
        if( !isset( $this->tokens[ $name ] ) )
            return [];

        $this->authExpireTokens( $name );

        $tokens = [];
        foreach( $this->tokens[ $name ] as $t )
            $tokens[] = $t['value'];

        return $tokens;
    }

    /**
     * Get the users email address so that reset tokens and other communication cab
     * be sent to him/her.
     *
     * @return string
     */
    public function authGetEmail()
    {
        return $this->email;
    }

    /**
     * Get the user's fullname (can return an empty string if you wish)
     *
     * @return string
     */
    public function authGetFullname()
    {
        return $this->firstname . ' ' . $this->surname;
    }


}
