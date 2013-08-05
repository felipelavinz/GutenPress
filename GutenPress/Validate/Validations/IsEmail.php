<?php

namespace GutenPress\Validate\Validations;

class IsEmail implements \GutenPress\Validate\ValidatorInterface{
	public function isValid( $value ){
		return is_email( $value );
	}
	public function getMessages(){
		return __('Por favor ingresa una dirección de e-mail válida', 'gutenpress');
	}
}