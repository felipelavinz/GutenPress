<?php

namespace GutenPress\Validate;

class Validate{
	protected $data;
	protected $rules;
	protected $is_valid = false;
	protected $errors = array();
	protected $validated = false;


	/**
	 * @param array $data The data that will be validated; keys are input names, values are input values
	 * @param array $rules A set of validation rules
	 */
	public function __construct( array $data, array $rules ){

		$this->data  = $data;
		$this->rules =  $rules;

	}

	/**
	 * @return bool
	 */
	public function isValid(){

		if ( $this->validated )
			return $this->is_valid;

		$this->validateLoop();

		if ( empty($this->errors) ) {
			$this->is_valid = true;
		}

		return $this->is_valid;
	}

	protected function validateLoop(){
		if ( $this->validated ) {
			return;
		}

		foreach ( $this->rules as $key => $val ) {
			if ( ! isset($this->data[$key]) ) {
				$this->errors[ $key ] = __("The $key field it's not set", 'gutenpress');
			} elseif ( is_array($val) ) {
				foreach ( $val as $validator ) {
					$this->validateData( $key, $this->data[$key], $validator );
				}
			} else {
				$this->validateData( $key, $this->data[$key], $val );
			}
		}

		$this->validated = true;
	}

	protected function validateData( $name, $value, ValidatorInterface $validator ){
		if ( ! $validator->isValid($value) ) {
			$this->errors[ $name ] = $validator->getMessages();
		}
	}

	/**
	 * @return bool
	 */
	public function isInvalid(){
		return ! $this->isValid();
	}

	/**
	 * @return array
	 */
	public function getErrorMessages(){
		return $this->errors;
	}

}