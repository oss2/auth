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
    /**
     * Test the response for an unknown user
     */
    public function testUnknownUserResponse204()
    {
        $response = $this->call( 'POST', 'auth/send-reset-token', [ 'username' => 'badusername' ] );
        $this->assertEquals( 204, $response->getStatusCode() );
    }

    /**
     * Test the response for an unknown user
     */
    public function testUnknownUserResponse404()
    {
        Config::set( 'oss2/auth::send-reset-token.invalidCredentialsResponse', 404 );
        $response = $this->call( 'POST', 'auth/send-reset-token', [ 'username' => 'badusername' ] );
        $this->assertEquals( 404, $response->getStatusCode() );
        Config::set( 'oss2/auth::send-reset-token.invalidCredentialsResponse', 204 );
    }

    /**
     * Test the response for an unknown user
     */
    public function testCredentialsLookupEventResponse()
    {
        $credentials = [ 'username' => 'badusername' ];
        Event::shouldReceive('fire')->once()->with('oss2/auth::pre_credentials_lookup', $credentials );
        Log::shouldReceive( 'info' )->withAnyArgs();
        $response = $this->call( 'POST', 'auth/send-reset-token', $credentials );
        $this->assertEquals( 204, $response->getStatusCode() );
    }

    /**
     * Test the response for a valid user
     */
    public function testValidUserResponse()
    {
        // reset the invalid response to avoid checking that:
        Config::set( 'oss2/auth::send-reset-token.invalidCredentialsResponse', 404 );
        Log::shouldReceive( 'info' )->withAnyArgs();
        $response = $this->call( 'POST', 'auth/send-reset-token', [ 'username' => 'testusername' ] );
        Config::set( 'oss2/auth::send-reset-token.invalidCredentialsResponse', 204 );
        $this->assertEquals( 204, $response->getStatusCode() );
    }

    public function testTokenCreation()
    {
        Log::shouldReceive( 'info' )->withAnyArgs();
        $response = $this->call( 'POST', 'auth/send-reset-token', [ 'username' => 'testusername' ] );
        $this->assertEquals( 204, $response->getStatusCode() );
        $tokens = $this->getUsers( 0 )->authGetTokens( 'oss2/auth.password-reset.tokens' );
        $this->assertTrue( is_array( $tokens ) );
        $this->assertGreaterThan( 0, count( $tokens ) );
        $this->assertTrue( is_string( array_pop( $tokens ) ) );
    }

    public function testTokenSendHandler()
    {
        Log::shouldReceive( 'info' )->withAnyArgs();
        $response = $this->call( 'POST', 'auth/send-reset-token', [ 'username' => 'testusername' ] );
        $this->assertEquals( 204, $response->getStatusCode() );
        $tokens = $this->getUsers( 0 )->authGetTokens( 'oss2/auth.password-reset.tokens' );
    }

    public function testExcessiveTokenCreation()
    {
        for( $i = 0; $i < Config::get( 'oss2/auth::send-reset-token.maxTokens', 5 ) + 1; $i++ ) {
            $this->refreshClient();
            Log::shouldReceive( 'info' )->withAnyArgs();
            $response = $this->call( 'POST', 'auth/send-reset-token', [ 'username' => 'testusername' ] );
        }

        $this->assertEquals( \Config::get( 'oss2/auth::send-reset-token.maxTokens', 5 ), count( $this->getUsers(0)->authGetTokens( 'oss2/auth.password-reset.tokens' ) ) );
    }

    public function testInexcessiveTokenCreation()
    {
        for( $i = 0; $i < Config::get( 'oss2/auth::send-reset-token.maxTokens', 5 ) - 1; $i++ ) {
            $this->refreshClient();
            Log::shouldReceive( 'info' )->withAnyArgs();
            $response = $this->call( 'POST', 'auth/send-reset-token', [ 'username' => 'testusername' ] );
        }

        $this->assertLessThan( \Config::get( 'oss2/auth::send-reset-token.maxTokens', 5 ), count( $this->getUsers(0)->authGetTokens( 'oss2/auth.password-reset.tokens' ) ) );
    }

    public function testExpiredTokens()
    {
        for( $i = 0; $i <2; $i++ ) {
            $this->refreshClient();
            Log::shouldReceive( 'info' )->withAnyArgs();
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
    }


    /**
     * Ensure validation is working
     * @expectedException Oss2\Auth\Validation\Exception
     */
    public function testValidation()
    {
        \Config::set( 'oss2/auth::send-reset-token.paramFilter', [ 'username' ] );
        \Config::set( 'oss2/auth::send-reset-token.paramRules', [
            'username' => ['required', 'min:5']
        ]);

        $this->call( 'POST', 'auth', [ 'username' => 'a' ] );
    }

    /**
     * Ensure validation is working
     * @expectedException Oss2\Auth\Validation\Exception
     */
    public function testFilter()
    {
        \Config::set( 'oss2/auth::send-reset-token.paramFilter', [ 'nousername' ] );
        \Config::set( 'oss2/auth::send-reset-token.paramRules', [
            'username' => ['required', 'min:5']
        ]);

        $this->call( 'POST', 'auth', [ 'username' => 'testusername' ] );
    }

}
