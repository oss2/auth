<?php

/**
 * Oss2/Auth
 *
 * Test the authentication extension: MaxFailed
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
class MaxFailedTest extends Oss2\Auth\Testbench\TestCase
{
    public function setUp()
    {
        parent::setUp();

        \Config::set( 'oss2/auth::extensions.maxFailed.enabled',       true );

        \Auth::addExtension( 'maxFailed', \Config::get( 'oss2/auth::extensions.maxFailed' ) );
    }

    /**
     * Ensure we get locked out after sequential failed logins
     */
    public function testLimit()
    {
        // The default config locks after 5. We use 8 here to also test that the
        // extension continues incrementing the count.

        for( $i = 0; $i < 8; $i++ ) {
            $this->assertHTTPExceptionStatus( 403, function( $_this ) {
                // good username, bad password
                $_this->call( 'POST', 'auth', [ 'username' => 'testusername', 'password' => 'badpassword' ] );
            });

            $this->assertEquals( $i + 1, $this->getUsers( 0 )->getAuthAttempts() );
        }

        // should not be able to log in with a good password now
        $this->assertHTTPExceptionStatus( 403, function( $_this ) {
            $_this->call( 'POST', 'auth', [ 'username' => 'testusername', 'password' => 'testpassword' ] );
        });
    }

    /**
     * Ensure we reset the failed logins to zero after a successful login
     */
    public function testLimitReset()
    {
        // good username, bad password
        for( $i = 0; $i < 4; $i++ ) {
            $this->assertHTTPExceptionStatus( 403, function( $_this ) {
                $_this->call( 'POST', 'auth', [ 'username' => 'testusername', 'password' => 'badpassword' ] );
            });

            $this->assertEquals( $i + 1, $this->getUsers( 0 )->getAuthAttempts() );
        }

        // should still be able to log in with a good password now
        $this->assertEquals( 4, $this->getUsers( 0 )->getAuthAttempts() );
        $this->call( 'POST', 'auth', [ 'username' => 'testusername', 'password' => 'testpassword' ] );
        $this->assertResponseOk(); // 200

        // and bad password count should be reset:
        $this->assertEquals( 0, $this->getUsers( 0 )->getAuthAttempts() );
    }

    /**
     * Ensure we reset the failed logins to zero after a successful password reset
     */
    public function testPasswordReset()
    {
        // good username, bad password - lock the user out
        for( $i = 0; $i < 5; $i++ ) {
            $this->assertHTTPExceptionStatus( 403, function( $_this ) {
                $_this->call( 'POST', 'auth', [ 'username' => 'testusername', 'password' => 'badpassword' ] );
            });

            $this->assertEquals( $i + 1, $this->getUsers( 0 )->getAuthAttempts() );
        }

        // should still be able to log in with a good password now
        $this->assertEquals( 5, $this->getUsers( 0 )->getAuthAttempts() );

        // should not be able to log in with a good password now
        $this->assertHTTPExceptionStatus( 403, function( $_this ) {
            $_this->call( 'POST', 'auth', [ 'username' => 'testusername', 'password' => 'testpassword' ] );
        });

        // simulate a password reset and verify that can can log in again:
        \Event::fire( 'oss2/auth::password_reset', [ [ 'user' => $this->getUsers( 0 ) ] ] );
        $this->call( 'POST', 'auth', [ 'username' => 'testusername', 'password' => 'testpassword' ] );
        $this->assertResponseOk(); // 200
    }

}
