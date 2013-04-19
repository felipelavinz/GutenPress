<?php

namespace GutenPress\Forms;

abstract class OptionElement extends FormElement implements OptionsFormElementInterface{
	protected $options;
	public function __construct( $label = '', $name = '', array $options = array(), array $properties = array() ) {
		if ( $options )
			$this->setOptions( $options );
		parent::__construct( $label, $name, $properties );
	}
	public function setOptions( array $options ){
		$options = array_filter( $options );
		// if the options array doesn't have specific keys, use labels as keys
		if ( array_values($options) === $options ) {
			$this->options = array_combine( $options, $options );
		} else {
			$this->options = $options;
		}
	}
	public function getOptions(){
		return $this->options;
	}
}