<?php

namespace GutenPress\Forms\Element;

class InputCheckbox extends InputRadio implements \GutenPress\Forms\MultipleFormElementInterface{
	protected static $type = 'checkbox';
	public function __construct( $label = '', $name = '', $options = null , array $properties = array() ) {
		// cast options as array
		parent::__construct( $label, $name, (array)$options, $properties );
	}
	public function setName( $name ){
		parent::setName( $name .'[]' );
	}
}