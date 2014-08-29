<?php namespace Oss2\Auth;

/**
 * Oss2/Auth
 *
 * User provider interface
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

interface UserInterface extends \Illuminate\Auth\UserInterface
{
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
    public function getAuthResponse();




}
