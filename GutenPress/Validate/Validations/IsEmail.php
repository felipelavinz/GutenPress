<?php

namespace GutenPress\Validate\Validations;

class IsEmail implements \GutenPress\Validate\ValidatorInterface{
	public function isValid( $value ){
		return is_email( $value );
	}
	public function getMessages(){
		return __('Please enter a valid e-mail address', 'gutenpress');
	}
}