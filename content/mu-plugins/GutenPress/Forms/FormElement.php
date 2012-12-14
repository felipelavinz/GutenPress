<?php

namespace GutenPress\Forms;

abstract class FormElement extends \GutenPress\Forms\Element{
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
	public function __construct( $label, $name, array $properties = array() ) {
		$this->label = $label;
		$this->name  = $name;
		if ( ! empty($properties['value']) ) {
			$this->setValue( $properties['value'] );
		}
		$properties['name']	= $this->name;
		parent::__construct( $properties );
	}
	protected function setValue( $value ){
		$this->value = $value;
	}
	public function getValue(){
		return $this->value;
	}
	public function getLabel(){
		return $this->label;
	}
}