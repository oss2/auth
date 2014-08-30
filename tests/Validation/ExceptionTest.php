<?php

/**
 * Oss2/Auth
 *
 * Test the validation abstract class
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
class ExceptionTest extends \Oss2\Auth\Testbench\TestCase {

    private $validator;

    public function setUp()
    {
        parent::setUp();

        // Need a failing validator for these tests:
        $this->validator = \Validator::make( [ 'p1' => 'a' ], [ 'p1' => [ 'required', 'min:3', 'email' ], 'p2' => 'required' ] );
    }


    /**
     * Ensure the validator actually fails
     */
    public function testValidatorFails()
    {
        $this->assertTrue( $this->validator->fails() );
    }

    /**
     * Ensure we have validator messages
     */
    public function testValidatorMessages()
    {
        // trigger a failed validation (and ensure it failed!)
        $this->assertTrue( $this->validator->fails() );

        // mainly to document and ensure the expected behavior:
        $this->assertTrue( $this->validator->messages() instanceof Illuminate\Support\MessageBag );
        $this->assertEquals( 3, $this->validator->messages()->count() );
        $this->assertTrue( $this->validator->messages()->has( 'p1' ) );
        $this->assertTrue( $this->validator->messages()->has( 'p2' ) );
        $this->assertFalse( $this->validator->messages()->has( 'p3' ) );
        $this->assertEquals( 2, count( $this->validator->messages()->get( 'p1' ) ) );
        $this->assertEquals( 1, count( $this->validator->messages()->get( 'p2' ) ) );
    }

    public function testJsonApiResponse()
    {
        $this->assertTrue( $this->validator->fails() );
        $this->assertEquals(
            '{"p1":["validation.min.string","validation.email"],"p2":["validation.required"]}',
            $this->validator->messages()->toJson()
        );

        $e = new Oss2\Auth\Validation\Exception( $this->validator );
        $this->assertEquals( $this->validator->messages()->toJson(), $e->getApiErrors() );
    }



}
