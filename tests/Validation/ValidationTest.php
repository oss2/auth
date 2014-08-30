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
class ValidationTest extends \Oss2\Auth\Testbench\TestCase {

    private $instance = null;

    public function setUp()
    {
        parent::setUp();
        $this->instance = new ValidationTestClass;
    }


    public function testRulesAccessors()
    {
        $array = [ 1, 2, 3 ];
        $this->instance->setRules( $array );
        $this->assertEquals( $array, $this->instance->getRules() );
    }

    public function testInputAccessors()
    {
        $array = [ 1, 2, 3 ];
        $this->instance->setInput( $array );
        $this->assertEquals( $array, $this->instance->getInput() );
    }

    public function testValidateOk()
    {
        $input = [ 'param1' => 'abcdefgh' ];
        $rules = [
            'param1' => [ 'required', 'min:8' ]
        ];

        $this->instance->setInput( $input );
        $this->instance->setRules( $rules );

        $this->assertTrue( $this->instance->validate() );
    }

    /**
     * @expectedException Oss2\Auth\Validation\Exception
     */
    public function testValidateFail()
    {
        $input = [ 'param1' => 'abcdefgh' ];
        $rules = [
            'param1' => [ 'required', 'min:8' ],
            'param2' => [ 'required' ]
        ];

        $this->instance->setInput( $input );
        $this->instance->setRules( $rules );

        $this->instance->validate();
    }
}

class ValidationTestClass extends \Oss2\Auth\Validation\Validation{}
