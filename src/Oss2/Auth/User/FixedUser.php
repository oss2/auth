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

    /** @var AuthAttempts */
    public $authAttempts = 0;


	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
    {
        return $this->id;
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
    public function getAuthResponse()
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
    public function getAuthAttempts()
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
    public function incrementAuthAttempts()
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
    public function setAuthAttempts( $i )
    {
        $this->authAttempts = $i;
    }
}
