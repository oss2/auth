<?php namespace Oss2\Auth\Handlers;

/**
 * Oss2/Auth
 *
 * Exception for handlers.
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
class TestException extends \Oss2\Auth\Exception {

    /**
     * @var Oss2\Auth\UserInterface
     */
    private $user;

    /**
     * @var array
     */
    private $data;

    function __construct( $message, $user, $data ) {
        parent::__construct( $message );
        $this->setUser( $user );
        $this->setData( $data );
    }



    /**
     * Get the value of User
     *
     * @return Oss2\Auth\UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set the value of User
     *
     * @param Oss2\Auth\UserInterface user
     *
     * @return self
     */
    public function setUser( $value)
    {
        $this->user = $value;

        return $this;
    }

    /**
     * Get the value of Data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the value of Data
     *
     * @param array data
     *
     * @return self
     */
    public function setData(array $value)
    {
        $this->data = $value;

        return $this;
    }

}
