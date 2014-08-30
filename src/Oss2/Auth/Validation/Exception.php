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

    public function getJsonApiErrors()
    {
        return json_encode( [ 'errors' => $this->getApiErrors() ] );
    }
}
