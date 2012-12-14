<?php

namespace GutenPress\Validate\Validations;

class Boolean implements \GutenPress\Validate\ValidatorInterface{

	private static $allowed_values = array(
		'true',
		'false',
		'on',
		'off',
		'yes',
		'no',
		'1',
		'0'
	);

	private $allow_empty = false;

	public function __construct( $allow_empty = false ){
		$this->allow_empty = (bool)$allow_empty;
	}

	public function isValid( $value ) {
		$value = (string)trim($value);
		if ( $this->allow_empty ) {
			return ( in_array($value, self::$allowed_values, true) || empty($value) ) ? true : false;
		} else {
			return in_array($value, self::$allowed_values, true);
		}
	}

	public function getMessages(){
		return __('The value for this field should be of a true/false type');
	}

}