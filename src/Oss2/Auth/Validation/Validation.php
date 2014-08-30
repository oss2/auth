<?php namespace Oss2\Auth\Validation;

abstract class Validation
{
        protected $rules    = [];
        protected $messages = [];
        protected $input    = [];

        public function __construct( array $input = [], array $rules = [] ) {
            $this->setInput( $input );
            $this->setRules( $rules );
        }

        public function validate() {
            $validator = \Validator::make( $this->getInput(), $this->getRules() );

            if( $validator->fails() )
                throw new Exception( $validator );

            return true;
        }

        public function setRules( $rules ) {
            $this->rules = $rules;
        }

        public function getRules() {
            return $this->rules;
        }

        public function setInput( $input ) {
            $this->input = $input;
        }

        public function getInput() {
            return $this->input;
        }
}
