<?php

namespace GutenPress\Forms\Element;

class InputHidden extends Input{
	protected static $type = 'hidden';
	public function __construct( $name, $value, array $properties = array() ) {
		$properties['type']  = self::$type;
		$properties['value'] = $value;
		parent::__construct( '', $name, $properties );
	}
}