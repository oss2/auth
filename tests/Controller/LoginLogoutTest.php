<?php

/**
 * Oss2/Auth
 *
 * Test the authentication controller's auth method(s)
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
class LoginLogoutTest extends Oss2\Auth\Testbench\TestCase
{
    /**
     * Basic auth tests (are not logged in before auth and after logout)
     */
    public function testBasics()
    {
        \Auth::logout();
        $this->assertTrue( \Auth::guest() );
        $this->assertFalse( \Auth::check() );

        // good username, bad password
        $response = $this->call( 'POST', 'auth', [ 'username' => 'testusername', 'password' => 'badpassword' ] );
        $this->assertEquals( 403, $response->getStatusCode() );
        $this->assertTrue( \Auth::guest() );
        $this->assertFalse( \Auth::check() );

        $response = $this->call( 'POST', 'auth', [ 'username' => 'testusername', 'password' => 'testpassword' ] );
        $this->assertResponseOk(); // 200
        $this->assertFalse( \Auth::guest() );
        $this->assertTrue( \Auth::check() );

        \Auth::logout();
        $this->assertTrue( \Auth::guest() );
        $this->assertFalse( \Auth::check() );
    }

    /**
     * Test different types of failed auth
     */
    public function testFailedLogins()
    {
        // bad username, good password
        $response = $this->call( 'POST', 'auth', [ 'username' => 'badusername', 'password' => 'testpassword' ] );
        $this->assertEquals( 403, $response->getStatusCode() );
        $this->assertTrue( \Auth::guest() );
        $this->assertFalse( \Auth::check() );

        // good username, bad password
        $response = $this->call( 'POST', 'auth', [ 'username' => 'testusername', 'password' => 'badpassword' ] );
        $this->assertEquals( 403, $response->getStatusCode() );
        $this->assertTrue( \Auth::guest() );
        $this->assertFalse( \Auth::check() );

        // bad username, bad password
        $response = $this->call( 'POST', 'auth', [ 'username' => 'badusername', 'password' => 'badpassword' ] );
        $this->assertEquals( 403, $response->getStatusCode() );
        $this->assertTrue( \Auth::guest() );
        $this->assertFalse( \Auth::check() );
    }

    /**
     * Test a successful login and the various responses
     */
    public function testSuccessfulLogins()
    {
        $this->assertTrue( \Auth::guest() );
        $this->assertFalse( \Auth::check() );

        $response = $this->call( 'POST', 'auth', [ 'username' => 'testusername', 'password' => 'testpassword' ] );
        $this->assertResponseOk(); // 200

        $this->assertInstanceOf( '\\Illuminate\\Http\\JsonResponse', $response );

        $this->assertObjectHasAttribute( 'user', $response->getData() );
        $this->assertObjectHasAttribute( 'authIdentifier', $response->getData()->user );

        $this->assertFalse( \Auth::guest() );
        $this->assertTrue( \Auth::check() );
    }

    /**
     * Test a successful login and the various responses
     */
    public function testLoginAlias()
    {
        $this->assertTrue( \Auth::guest() );
        $this->assertFalse( \Auth::check() );

        $response = $this->call( 'POST', 'auth/login', [ 'username' => 'testusername', 'password' => 'testpassword' ] );
        $this->assertResponseOk(); // 200

        $this->assertFalse( \Auth::guest() );
        $this->assertTrue( \Auth::check() );
    }


    /**
     * Test a logout call
     */
    public function testLogout()
    {
        // login first:
        $response = $this->call( 'POST', 'auth', [ 'username' => 'testusername', 'password' => 'testpassword' ] );
        $this->assertResponseOk(); // 200

        $this->assertFalse( \Auth::guest() );
        $this->assertTrue( \Auth::check() );

        $response = $this->call( 'GET', 'auth/logout' );
        $this->assertEquals( 204, $response->getStatusCode() );

        $this->assertTrue( \Auth::guest() );
        $this->assertFalse( \Auth::check() );
    }

    /**
     * We rely on Laravel's Auth events in places so ensure they work:
     */
    public function testSuccessfulLoginEvents()
    {
        // good username, good password
        $credentials = [ 'username' => 'testusername', 'password' => 'testpassword' ];
        $payload = [ $credentials, false, true ];

        Event::shouldReceive('fire')->once()->with('auth.attempt', $payload );
        Event::shouldReceive('fire')->once()->with('auth.login', [ $this->users[0], false ] );
        Event::shouldReceive('fire')->once()->with('oss2/auth::pre_credentials_lookup', $credentials );
        Event::shouldReceive('fire')->once()->with('oss2/auth::credentials_retrieved', [ [ 'credentials' => $credentials, 'user' => $this->users[0] ] ] );
        Event::shouldReceive('fire')->once()->with('oss2/auth::credentials_valid', [ [ 'credentials' => $credentials, 'user' => $this->users[0] ] ] );

        $response = $this->call( 'POST', 'auth', $credentials );
        $this->assertResponseOk(); // 200
    }

    /**
     * We rely on Laravel's Auth events in places so ensure they work:
     */
    public function testUnsuccessfulLoginEvents()
    {
        // good username, bad password
        $credentials = [ 'username' => 'testusername', 'password' => 'badpassword' ];
        $payload = [ $credentials, false, true ];

        Event::shouldReceive('fire')->once()->with('auth.attempt', $payload );
        Event::shouldReceive('fire')->once()->with('oss2/auth::pre_credentials_lookup', $credentials );
        Event::shouldReceive('fire')->once()->with('oss2/auth::credentials_retrieved', [ [ 'credentials' => $credentials, 'user' => $this->users[0] ] ] );
        Event::shouldReceive('fire')->once()->with('oss2/auth::credentials_invalid', [ [ 'credentials' => $credentials, 'user' => $this->users[0] ] ] );
        Event::shouldReceive('fire')->once()->with('oss2/auth::auth_failed', $credentials );

        $response = $this->call( 'POST', 'auth', $credentials );
        $this->assertEquals( 403, $response->getStatusCode() );


        // bad username, bad password
        $credentials = [ 'username' => 'badusername', 'password' => 'badpassword' ];
        $payload = [ $credentials, false, true ];

        Event::shouldReceive('fire')->once()->with('auth.attempt', $payload );
        Event::shouldReceive('fire')->once()->with('oss2/auth::pre_credentials_lookup', $credentials );
        Event::shouldReceive('fire')->once()->with('oss2/auth::credentials_retrieved', [ [ 'credentials' => $credentials, 'user' => null ] ] );
        Event::shouldReceive('fire')->once()->with('oss2/auth::credentials_invalid', [ [ 'credentials' => $credentials, 'user' => null ] ] );
        Event::shouldReceive('fire')->once()->with('oss2/auth::auth_failed', $credentials );

        $this->call( 'POST', 'auth', $credentials );
        $this->assertEquals( 403, $response->getStatusCode() );
    }

    /**
     * Ensure validation is working
     * @expectedException Oss2\Auth\Validation\Exception
     */
    public function testValidation()
    {
        \Config::set( 'oss2/auth::login.paramFilter', [ 'username', 'password' ] );
        \Config::set( 'oss2/auth::login.paramRules', [
            'username' => ['required', 'min:5'],
            'password' => ['required', 'min:8']
        ]);

        $this->call( 'POST', 'auth', [ 'username' => 'a', 'password' => '' ] );
    }

    /**
     * Ensure validation is working
     * @expectedException Oss2\Auth\Validation\Exception
     */
    public function testFilter()
    {
        \Config::set( 'oss2/auth::login.paramFilter', [ 'nousername', 'password' ] );
        \Config::set( 'oss2/auth::login.paramRules', [
            'username' => ['required', 'min:5'],
            'password' => ['required', 'min:8']
        ]);

        $this->call( 'POST', 'auth', [ 'username' => 'testusername', 'password' => 'testpassword' ] );
    }

}
