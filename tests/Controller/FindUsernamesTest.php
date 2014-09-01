<?php

/**
 * Oss2/Auth
 *
 * Test the authentication controller's password reset method(s)
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
class FindUsernamesTest extends Oss2\Auth\Testbench\TestCase
{
    /**
     * Test the response for no users found
     */
    public function testUnknownUserResponse204()
    {
        $response = $this->call( 'POST', 'auth/find-usernames', [ 'email' => 'bademail@example.com' ] );
        $this->assertEquals( 204, $response->getStatusCode() );
    }

    /**
     * Test the response for no users found
     */
    public function testUnknownUserResponse404()
    {
        Config::set( 'oss2/auth::find-usernames.invalidCredentialsResponse', 404 );
        $response = $this->call( 'POST', 'auth/find-usernames', [ 'email' => 'bademail@example.com' ] );
        $this->assertEquals( 404, $response->getStatusCode() );
        Config::set( 'oss2/auth::find-usernames.invalidCredentialsResponse', 204 );
    }

    /**
     * Test the response for an unknown user
     */
    public function testCredentialsLookupEventResponse()
    {
        $credentials = [ 'email' => 'test@exmaple.com' ];
        Event::shouldReceive('fire')->once()->with('oss2/auth::pre_credentials_lookup', $credentials );
        $response = $this->call( 'POST', 'auth/find-usernames', $credentials );
        $this->assertEquals( 204, $response->getStatusCode() );
    }

    /**
     * Test the response for a valid user
     */
    public function testValidUserResponse()
    {
        // reset the invalid response to avoid checking that:
        Config::set( 'oss2/auth::find-usernames.invalidCredentialsResponse', 404 );
        $response = $this->call( 'POST', 'auth/find-usernames', [ 'email' => 'test@example.com' ] );
        Config::set( 'oss2/auth::find-usernames.invalidCredentialsResponse', 204 );
        $this->assertEquals( 204, $response->getStatusCode() );
    }

    /**
     * @expectedException Oss2\Auth\Handlers\TestException
     */
    public function testTokenSendHandler()
    {
        App::bind( 'Oss2\Auth\Handlers\FindUsernamesHandler', 'Oss2\Auth\Handlers\TestHandler' );
        $response = $this->call( 'POST', 'auth/find-usernames', [ 'email' => 'test@example.com' ] );
    }

    /**
     */
    public function testUserFound()
    {
        App::bind( 'Oss2\Auth\Handlers\FindUsernamesHandler', 'Oss2\Auth\Handlers\TestHandler' );
        try {
            $this->call( 'POST', 'auth/find-usernames', [ 'email' => 'test@example.com' ] );
        } catch( Oss2\Auth\Handlers\TestException $e ) {
            $this->assertTrue( is_array( $e->getData() ) );
            $this->assertTrue( is_array( $e->getData()['users'] ) );
            $this->assertEquals( 1, count( $e->getData()['users'] ) );
            $this->assertInstanceOf( 'Oss2\Auth\User\FixedUser', array_pop( $e->getData()['users'] ) );
        }
    }

    /**
     */
    public function testUsersFound()
    {
        App::bind( 'Oss2\Auth\Handlers\FindUsernamesHandler', 'Oss2\Auth\Handlers\TestHandler' );
        try {
            $this->call( 'POST', 'auth/find-usernames', [ 'email' => '667@example.com' ] );
        } catch( Oss2\Auth\Handlers\TestException $e ) {
            $this->assertTrue( is_array( $e->getData() ) );
            $this->assertTrue( is_array( $e->getData()['users'] ) );
            $this->assertEquals( 2, count( $e->getData()['users'] ) );
            $users = $e->getData()['users'];
            $this->assertInstanceOf( 'Oss2\Auth\User\FixedUser', array_pop( $users ) );
            $this->assertInstanceOf( 'Oss2\Auth\User\FixedUser', array_pop( $users ) );
            $this->assertEquals( 0, count( $users ) );
        }
    }


    /**
     * Ensure validation is working
     * @expectedException Oss2\Auth\Validation\Exception
     */
    public function testValidation()
    {
        \Config::set( 'oss2/auth::find-usernames.paramFilter', [ 'email' ] );
        \Config::set( 'oss2/auth::find-usernames.paramRules', [
            'email' => ['required', 'email']
        ]);

        $this->call( 'POST', 'auth/find-usernames', [ 'email' => 'not-an-email-address' ] );
    }

    /**
     * Ensure validation is working
     * @expectedException Oss2\Auth\Validation\Exception
     */
    public function testFilter()
    {
        \Config::set( 'oss2/auth::find-usernames.paramFilter', [ 'noemail' ] );
        \Config::set( 'oss2/auth::find-usernames.paramRules', [
            'email' => ['required', 'email']
        ]);

        $this->call( 'POST', 'auth/find-usernames', [ 'email' => 'test@example.com' ] );
    }

}
