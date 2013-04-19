<?php

namespace GutenPress\Forms\Element;

class InputHidden extends Input{
	protected static $type = 'hidden';
	public function __construct( $name = '', $value = '', array $properties = array() ) {
		if ( $value )
			$properties['value'] = $value;
		parent::__construct( '', $name, $properties );
	}
	public function setProperties( array $properties ){
		$properties['type']  = self::$type;
		parent::setProperties( $properties );
	}
}