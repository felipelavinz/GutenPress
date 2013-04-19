<?php

namespace GutenPress\Validate\Validations;

class Required implements \GutenPress\Validate\ValidatorInterface{

	public function isValid( $value ) {
		return ! empty( $value );
	}

	public function getMessages(){
		return __('Please complete this field', 'gutenpress');
	}

}