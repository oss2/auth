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
class FixedUser implements
            \Oss2\Auth\UserInterface,
            \Oss2\Auth\Extensions\Interfaces\MaxFailedUserInterface,
            \Oss2\Auth\Extensions\Interfaces\TwoFactorUserInterface
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

    /** @var 2FA */
    public $twoFA = false;


    /** @var two factor authentication */
    public $twofa = false;

    /** 2fa fallback */
    public $twofaFallback = false;


    /**
     * Is 2fa enabled?
     *
     * return bool
     */
    public function auth2faEnabled()
    {
        return $this->twofa != null;
    }

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
     * set the password for the user.
     *
     * @param string $hashedPassword
     */
    public function setAuthPassword( $hashedPassword )
    {
        $this->password = $hashedPassword;
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
     * Clear an indexed preference to the user.
     *
     * @param string $name    The name of the indexed preference. E.g. `oss2/auth.password-reset.tokens`
     */
    public function authClearTokens( $name )
    {
        if( isset( $this->tokens[ $name ] ) )
            unset( $this->tokens[ $name ] );
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
     * Validate a token for the user.
     *
     * @param string $name         The name of the indexed preference. E.g. `oss2/auth.password-reset.tokens`
     * @param string $token        The token
     * @param bool   $clearIfValid If true (default), clear the tokens if a valid one is presented
     * @return bool
     */
    public function authValidateToken( $name, $token, $clearIfValid = true )
    {
        if( in_array( $token, $this->authGetTokens( $name ) ) ) {
            if( $clearIfValid )
                $this->authClearTokens( $name );

            return true;
        }

        return false;
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

    /**
     * Check if the user has two-factor authentication (2FA) enabled.
     *
     * Returns with false for no 2FA, otherwise the 2FA handler name
     *
     * @return string|bool
     */
    public function authGet2FA()
    {
        return $this->twoFA;
    }


    /** API Keys for this user */
    public $apikeys = [];

    /**
     * Create an API key
     *
     * @param int $expires Seconds from now for when this key should expire
     * @param array $allowed_ips Allowed IP addresses
     * @param array $allowed_routes Routes that this API key is allowed to access
     * @return \Oss2\Auth\ApiKeyInterface
     */
    public function apiKeyCreate( $expires = null, $allowed_ips = null, $allowed_routes = null )
    {}




}
