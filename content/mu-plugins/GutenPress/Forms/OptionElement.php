<?php

namespace GutenPress\Forms;

abstract class OptionElement extends FormElement{
	protected $options;
	public function __construct( $label, $name, array $options, array $properties = array() ) {
		$options = array_filter( $options );
		// if the options array doesn't have specific keys, use labels as keys
		if ( array_values($options) === $options ) {
			$this->options = array_combine( $options, $options );
		} else {
			$this->options = $options;
		}
		parent::__construct( $label, $name, $properties );
	}
	public function getOptions(){
		return $this->options;
	}
}