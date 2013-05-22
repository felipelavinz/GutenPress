<?php

namespace GutenPress\Forms;
use GutenPress\Forms;

abstract class FormElement extends Element implements FormElementInterface{
	protected $name;
	protected $label;
	protected $value;
	protected $description = '';

	/**
	 * Build a form element
	 * @param string $label The label for the corresponding element
	 * @param string $name The "name" for the form control
	 * @param array  $properties An associative array of element properties, most likely attributes but it can include other kind of data to be used on the view
	 */
	public function __construct( $label = '', $name = '', array $properties = array() ) {
		if ( $label )
			$this->setLabel( $label );
		if ( $name )
			$this->setName( $name );
		$this->setProperties( $properties );
	}
	public function setLabel( $label ){
		$this->label = $label;
	}
	public function setName( $name ){
		$this->name = $name;
	}
	public function setProperties( array $properties ){
		if ( ! empty($properties['value']) ) {
			$this->setValue( $properties['value'] );
		}
		$properties['name']	= $this->name;
		parent::__construct( $properties );
	}
	public function setValue( $value ){
		$this->value = $value;
		if ( in_array('value', static::$element_attributes) )
			$this->setAttribute('value', $value);
	}
	public function getValue(){
		return $this->value;
	}
	public function getLabel(){
		return $this->label;
	}
}