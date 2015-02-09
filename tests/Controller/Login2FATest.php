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
class Login2FATest extends Oss2\Auth\Testbench\TestCase
{
    public function setUp()
    {
        parent::setUp();

        \Config::set( 'oss2/auth::login.2fa_enabled', true );
    }

    /**
     * With 2FA enabled, do we get a redirect and 2FA token?
     */
    public function test2faLoginRedirect()
    {
        \Auth::logout();
        $this->assertTrue( \Auth::guest() );
        $this->assertFalse( \Auth::check() );

        $response = $this->call( 'POST', 'auth', [ 'username' => 'testusername', 'password' => 'testpassword' ] );
        $this->assertEquals( 300, $response->getStatusCode() );

        $params = json_decode( $response->getContent() );

        $this->assertObjectHasAttribute( 'username',   $params );
        $this->assertObjectHasAttribute( 'twofatoken', $params );
        $this->assertObjectHasAttribute( 'url',        $params );

        $this->assertEquals( 'testusername', $params->username );
        $this->assertTrue( strlen( $params->twofatoken ) > 10 );
        $this->assertTrue( strlen( $params->url ) > 10 );

        $this->assertTrue( \Auth::guest() );
        $this->assertFalse( \Auth::check() );
    }

    /**
     * With 2FA enabled, do we get a redirect and 2FA token?
     */
    public function testBad2faLogin()
    {
        \Auth::logout();
        $this->assertTrue( \Auth::guest() );
        $this->assertFalse( \Auth::check() );

        $response = $this->call( 'POST', 'auth/login2fa', [ 'username' => 'testusername', 'twofatoken' => 'test2fatoken', 'token' => 'qwerty78' ] );

        $this->assertEquals( 403, $response->getStatusCode() );

        $this->assertTrue( \Auth::guest() );
        $this->assertFalse( \Auth::check() );
    }

    /**
     * With 2FA enabled, do we get a redirect and 2FA token?
     */
    public function testBad2faLoginEvents()
    {
        $credentials = [ 'username' => 'testusername', 'twofatoken' => 'test2fatoken', 'token' => 'qwerty78' ];

        Event::shouldReceive('fire')->once()->with('oss2/auth::pre_credentials_lookup',   $credentials );
        Event::shouldReceive('fire')->once()->with('oss2/auth::2fa_auth_failed_2fatoken', $credentials );

        $response = $this->call( 'POST', 'auth/login2fa', $credentials );
    }

    /**
     * Test good 2fatoken but bad token
     */
    public function testGood2fatokenBadToken()
    {
        $this->getUsers(0)->authAddToken( 'oss2/auth.2fa.tokens', 'qwerty123456789' );
        $this->users[0]->twoFA = '\Oss2\Auth\TwoFactorAuthentication\Dummy';

        $credentials = [ 'username' => 'testusername', 'twofatoken' => 'qwerty123456789', 'token' => 'qwerty78' ];

        Event::shouldReceive('fire')->once()->with('oss2/auth::pre_credentials_lookup',   $credentials );
        Event::shouldReceive('fire')->once()->with('oss2/auth::2fa_auth_failed_token',    $credentials );

        $response = $this->call( 'POST', 'auth/login2fa', $credentials );

        $this->assertEquals( 403, $response->getStatusCode() );
    }

    /**
     * Test good 2fatoken, good token and valid login
     */
    public function testGood2fatokenGoodTokenLogin()
    {
        $this->getUsers(0)->authAddToken( 'oss2/auth.2fa.tokens', 'qwerty123456789' );
        $this->users[0]->twoFA = '\Oss2\Auth\TwoFactorAuthentication\Dummy';

        $credentials = [ 'username' => 'testusername', 'twofatoken' => 'qwerty123456789', 'token' => 'DUMMY' ];

        Event::shouldReceive('fire')->once()->with('oss2/auth::pre_credentials_lookup',   $credentials );
        Event::shouldReceive('fire')->once()->with('auth.login', [ $this->users[0], false ] );

        $response = $this->call( 'POST', 'auth/login2fa', $credentials );

        $this->assertResponseOk(); // 200
        $this->assertFalse( \Auth::guest() );
        $this->assertTrue( \Auth::check() );
    }



    /**
     * Ensure validation is working
     * @expectedException Oss2\Auth\Validation\Exception
     */
    public function testValidation()
    {
        \Config::set( 'oss2/auth::login-2fa.paramFilter', [ 'username', 'twofatoken', 'token' ] );
        \Config::set( 'oss2/auth::login-2fa.paramRules', [
            'username'   => ['required', 'min:5'],
            'twofatoken' => ['required', 'min:10'],
            'token'      => ['required']
        ]);

        $this->call( 'POST', 'auth', [ 'username' => 'a', 'twofatoken' => '', 'token' => '' ] );
    }

    /**
     * Ensure validation is working
     * @expectedException Oss2\Auth\Validation\Exception
     */
    public function testFilter()
    {
        \Config::set( 'oss2/auth::login.paramFilter', [ 'nousername', 'twofatoken', 'token' ] );
        \Config::set( 'oss2/auth::login.paramRules', [
            'username'   => ['required', 'min:5'],
            'twofatoken' => ['required', 'min:10'],
            'token'      => ['required']
        ]);

        $this->call( 'POST', 'auth', [ 'username' => 'testusername', 'twofatoken' => 'twofatokentesttest', 'token' => 'sdvvds' ] );
    }
}
