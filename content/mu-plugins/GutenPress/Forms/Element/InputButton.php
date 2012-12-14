<?php

namespace GutenPress\Forms\Element;

class InputButton extends Input{
	protected static $type = 'button';
	public function __construct( $value, $name, array $properties = array() ){
		$properties['type']  = static::$type;
		$properties['value'] = $value;
		$properties['name']  = $name;
		parent::__construct( '', $name, $properties );
	}
}