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
class ResetTest extends Oss2\Auth\Testbench\TestCase
{
    private $params;

    public function setUp()
    {
        parent::setUp();

        // dummy params for testing
        $this->params = [
            'username'              => 'testusername',
            'token'                 => 'qwerty123',
            'password'              => '12345678',
            'password_confirmation' => '12345678'
        ];
    }

    /**
     * Test the response for an unknown user
     */
    public function testUnknownUserResponse403()
    {
        $this->params['username'] = 'badusername';
        $response = $this->call( 'POST', 'auth/reset', $this->params );
        $this->assertEquals( 403, $response->getStatusCode() );
    }

    /**
     * Test the response for an known user with unknown token
     */
    public function testKnownUserUnknownTokenResponse404()
    {
        $response = $this->call( 'POST', 'auth/reset', $this->params );
        $this->assertEquals( 403, $response->getStatusCode() );
    }

    /**
     * Test the response for an unknown user
     */
    public function testCredentialsLookupEventResponse()
    {
        $this->params['username'] = 'badusername';
        Event::shouldReceive('fire')->once()->with('oss2/auth::pre_credentials_lookup', $this->params );
        $response = $this->call( 'POST', 'auth/reset', $this->params );
        $this->assertEquals( 403, $response->getStatusCode() );
    }

    /**
     * Test password reset
     */
    public function testPasswordReset()
    {
        // test p/w not equal to new one
        $this->getUsers(0)->authAddToken( 'oss2/auth.password-reset.tokens', 'qwerty123' );
        $this->assertFalse( $this->params['password'] == $this->getUsers(0)->getAuthPassword() );
        $response = $this->call( 'POST', 'auth/reset', $this->params );
        $this->assertEquals( 204, $response->getStatusCode() );
        $this->assertEquals( $this->params['password'], $this->getUsers(0)->getAuthPassword() );
    }

    public function testTokenCreation()
    {
        $response = $this->call( 'POST', 'auth/send-reset-token', [ 'username' => 'testusername' ] );
        $this->assertEquals( 204, $response->getStatusCode() );
        $tokens = $this->getUsers( 0 )->authGetTokens( 'oss2/auth.password-reset.tokens' );
        $this->assertTrue( is_array( $tokens ) );
        $this->assertGreaterThan( 0, count( $tokens ) );
        $this->assertTrue( is_string( array_pop( $tokens ) ) );
    }

    /**
     * @expectedException Oss2\Auth\Handlers\TestException
     *
    public function testTokenSendHandler()
    {
        App::bind( 'Oss2\Auth\Handlers\SendResetTokenHandler', 'Oss2\Auth\Handlers\TestHandler' );
        $response = $this->call( 'POST', 'auth/send-reset-token', [ 'username' => 'testusername' ] );
    }

    public function testExcessiveTokenCreation()
    {
        for( $i = 0; $i < Config::get( 'oss2/auth::send-reset-token.maxTokens', 5 ) + 1; $i++ ) {
            $this->refreshClient();
            $response = $this->call( 'POST', 'auth/send-reset-token', [ 'username' => 'testusername' ] );
        }

        $this->assertEquals( \Config::get( 'oss2/auth::send-reset-token.maxTokens', 5 ), count( $this->getUsers(0)->authGetTokens( 'oss2/auth.password-reset.tokens' ) ) );
    }

    public function testInexcessiveTokenCreation()
    {
        for( $i = 0; $i < Config::get( 'oss2/auth::send-reset-token.maxTokens', 5 ) - 1; $i++ ) {
            $this->refreshClient();
            $response = $this->call( 'POST', 'auth/send-reset-token', [ 'username' => 'testusername' ] );
        }

        $this->assertLessThan( \Config::get( 'oss2/auth::send-reset-token.maxTokens', 5 ), count( $this->getUsers(0)->authGetTokens( 'oss2/auth.password-reset.tokens' ) ) );
    }

    public function testExpiredTokens()
    {
        for( $i = 0; $i <2; $i++ ) {
            $this->refreshClient();
            $response = $this->call( 'POST', 'auth/send-reset-token', [ 'username' => 'testusername' ] );
        }

        $this->assertEquals( 2, count( $this->getUsers(0)->authGetTokens( 'oss2/auth.password-reset.tokens' ) ) );

        // force expiration of a token:
        $tokens = $this->getUsers(0)->getTokens();
        $token = array_pop( $tokens['oss2/auth.password-reset.tokens'] );
        $token['expiry'] = time() - 1;
        array_push( $tokens['oss2/auth.password-reset.tokens'], $token );
        $this->getUsers(0)->setTokens( $tokens );

        $this->assertEquals( 1, count( $this->getUsers(0)->authGetTokens( 'oss2/auth.password-reset.tokens' ) ) );
    }*/


    /**
     * Ensure validation is working
     * @expectedException Oss2\Auth\Validation\Exception
     */
    public function testValidation()
    {
        \Config::set( 'oss2/auth::reset.paramFilter', [ 'username' ] );
        \Config::set( 'oss2/auth::reset.paramRules', [
            'username' => ['required', 'min:5']
        ]);

        $this->call( 'POST', 'auth/reset', [ 'username' => 'a' ] );
    }

    /**
     * Ensure validation is working
     * @expectedException Oss2\Auth\Validation\Exception
     */
    public function testFilter()
    {
        \Config::set( 'oss2/auth::reset.paramFilter', [ 'nousername' ] );
        \Config::set( 'oss2/auth::reset.paramRules', [
            'username' => ['required', 'min:5']
        ]);

        $this->call( 'POST', 'auth/reset', [ 'username' => 'testusername' ] );
    }

}
