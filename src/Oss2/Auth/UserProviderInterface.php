<?php namespace Oss2\Auth;

/**
 * Oss2/Auth
 *
 * User interface
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
interface UserProviderInterface extends \Illuminate\Auth\UserProviderInterface {

    /**
     * Persist user data to permanent storgae (e.g. save user object to the database).
     *
     * Different extensions may alter / update a user's data. Before the controllor or
     * events abort / return, they should always call \Auth::persist() which in turn
     * will call this method to all you to persist as appropriate for your provider.
     *
     * @param \Oss2\Auth\UserProviderInterface $user The user to store
     */
    public function authPersist( \Oss2\Auth\UserInterface $user );

    /**
     * Get the name of a individual user class
     * @return string
     */
    public function authGetUserClassName();


}
