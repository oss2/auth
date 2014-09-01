<?php namespace Oss2\Auth\Validation;

/**
 * Oss2/Auth
 *
 * Validation functionality.
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
abstract class Validation
{
    /** @var array Validation rules (as per Laravel) */
    protected $rules    = [];

    /** @var array */
    protected $messages = [];

    /** @var array Input to test against validation */
    protected $input    = [];

    public function __construct( array $input = [], array $rules = [] ) {
        $this->setInput( $input );
        $this->setRules( $rules );
    }

    /**
     * Validate the input data against the rules
     *
     * @throws Oss2\Auth\Vaidation\Exception On failure
     * @return bool True on success
     */
    public function validate() {
        $validator = \Validator::make( $this->getInput(), $this->getRules() );

        if( $validator->fails() )
            throw new Exception( $validator );

        return true;
    }

    /**
     * Set the rules
     * @param array The rules
     */
    public function setRules( $rules ) {
        $this->rules = $rules;
    }

    /**
     * Get the rules
     * @return array The rules
     */
    public function getRules() {
        return $this->rules;
    }

    /**
     * Set the input
     * @param array The input
     */
    public function setInput( $input ) {
        $this->input = $input;
    }

    /**
     * Get the input
     * @return array The rules
     */
    public function getInput() {
        return $this->input;
    }
}
