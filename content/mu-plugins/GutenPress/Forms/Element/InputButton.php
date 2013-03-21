<?php

namespace GutenPress\Forms\Element;

class InputButton extends Input{
	protected static $type = 'button';
	public function __construct( $value = '', $name = '', array $properties = array() ){
		if ( $value )
			$properties['value'] = $value;
		if ( $name )
			$properties['name']  = $name;
		parent::__construct( '', $name, $properties );
	}
	public function setProperties( array $properties ){
		$properties['type']  = static::$type;
		parent::setProperties( $properties );
	}
}