<?php namespace Oss2\Auth\Hashing;

/**
 * Oss2/Auth
 *
 * Plaintext Hasher
 *
 * Hasher implementation for plaintext hashing (i.e. no hashing).
 *
 * **SHOULD BE USED FOR TESTING PURPOSES ONLY**
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
class PlaintextHasher implements \Illuminate\Hashing\HasherInterface
{

	/**
	 * Hash the given value.
	 *
	 * @param  string  $value
	 * @param  array   $options Not used here
	 * @return string  `$value` unaltered.
	 */
	public function make( $value, array $options = array() )
	{
		return $value;
	}

	/**
	 * Check the given plain value against a hash.
	 *
	 * @param  string  $value
	 * @param  string  $hashedValue
	 * @param  array   $options Not used
	 * @return bool    `$value == $hashedValue`
	 */
	public function check($value, $hashedValue, array $options = array())
	{
		return $value == $hashedValue;
	}

	/**
	 * Check if the given hash has been hashed using the given options.
	 *
	 * @param  string  $hashedValue
	 * @param  array   $options
	 * @return bool    Always false
	 */
	public function needsRehash($hashedValue, array $options = array())
	{
		return false;
	}

}
