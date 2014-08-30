<?php namespace Oss2\Auth\Validation;

class Exception extends \Oss2\Auth\Exception
{
    protected $validator;

    public function __construct( \Illuminate\Validation\Validator $validator, $message = 'Validation error' ) {
        parent::__construct( $message );
        $this->validator = $validator;
    }

    public function getErrors() {
        return $this->validator->messages();
    }

    public function getApiErrors() {
        return $this->validator->messages()->toJson();
    }
}
