<?php

namespace GutenPress\Forms\Element;

class InputCheckbox extends InputRadio{
	protected static $type = 'checkbox';
	public function __construct( $label, $name, $options = null , array $properties = array() ) {
		// cast options as array
		parent::__construct( $label, $name .'[]', (array)$options, $properties );
	}
}