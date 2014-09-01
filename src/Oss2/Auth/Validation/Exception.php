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
class Exception extends \Oss2\Auth\Exception
{
    /** @var Illuminate\Validation\Validator */
    protected $validator;

    /**
     * Constructor
     *
     * @params Illuminate\Validation\Validator $validator
     * @params string The exception description
     */
    public function __construct( \Illuminate\Validation\Validator $validator, $message = 'Validation error' ) {
        parent::__construct( $message );
        $this->validator = $validator;
    }

    public function getErrors() {
        return $this->validator->messages();
    }

    /**
     * Get the errors as an array of json.api error objects
     *
     * @return array
     */
    public function getApiErrors() {
        $errors = [];
        $i = 0;
        foreach( $this->validator->messages()->getMessages() as $param => $messages ) {
            $errors[$i] = [];
            $errors[$i]['id']       = $i;
            $errors[$i]['type']     = 'validation';
            $errors[$i]['param']    = $param;
            $errors[$i]['messages'] = $messages;
            $i++;
        }

        return $errors;
    }

    /**
     * Get the errors as a JSON collection of 'errors' (as per json.api)
     *
     * @return string The JSON string
     */
    public function getJsonApiErrors()
    {
        return json_encode( [ 'errors' => $this->getApiErrors() ] );
    }
}
