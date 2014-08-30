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
        \Config::set( 'oss2/auth::reset.invalidCredentialsResponse', 404 );
        $response = $this->call( 'POST', 'auth/send-reset-token', [ 'username' => 'badusername' ] );
        $this->assertEquals( 404, $response->getStatusCode() );
        \Config::set( 'oss2/auth::reset.invalidCredentialsResponse', 204 );
    }

    /**
     * Test the response for an unknown user
     */
    public function testCredentialsLookupEventResponse()
    {
        $credentials = [ 'username' => 'badusername' ];
        Event::shouldReceive('fire')->once()->with('oss2/auth::pre_credentials_lookup', $credentials );
        $response = $this->call( 'POST', 'auth/send-reset-token', $credentials );
        $this->assertEquals( 204, $response->getStatusCode() );
    }

    /**
     * Test the response for a valid user
     */
    public function testValidUserResponse()
    {
        // reset the invalid response to avoid checking that:
        \Config::set( 'oss2/auth::reset.invalidCredentialsResponse', 404 );
        $response = $this->call( 'POST', 'auth/send-reset-token', [ 'username' => 'testusername' ] );
        \Config::set( 'oss2/auth::reset.invalidCredentialsResponse', 204 );
        $this->assertEquals( 204, $response->getStatusCode() );
    }

    public function testTokenCreation()
    {
        $response = $this->call( 'POST', 'auth/send-reset-token', [ 'username' => 'testusername' ] );
        $this->assertEquals( 204, $response->getStatusCode() );
        $tokens = $this->getUsers( 0 )->getAuthTokens( 'oss2/auth.password-reset.tokens' );
        $this->assertTrue( is_array( $tokens ) );
        $this->assertGreaterThan( 0, count( $tokens ) );
        $this->assertTrue( is_string( array_pop( $tokens ) ) );
    }

    public function testExcessiveTokenCreation()
    {
        for( $i = 0; $i < 10; $i++ ) {
            $this->refreshClient();
            $response = $this->call( 'POST', 'auth/send-reset-token', [ 'username' => 'testusername' ] );
        }
//dd( $this->getUsers() );
//dd( $this->app );
//$this->refreshApplication();
//$response = $this->call( 'POST', 'auth/send-reset-token', [ 'username' => 'testusername' ] );

//dd( $this->getUsers() );
//        }
/*
        $this->assertEquals( 204, $response->getStatusCode() );
        $prefs = $this->getUsers( 0 )->getIndexedPreference( 'oss2/auth.password-reset.tokens' );
        $this->assertTrue( is_array( $prefs ) );
        $this->assertGreaterThan( 0, count( $prefs ) );
        $this->assertTrue( is_string( $prefs[0]['value'] ) );
        */
    }


}
