<?php

namespace GutenPress\Forms\Element;

class Button extends \GutenPress\Forms\Element{
	protected static $element_attributes = array(
		'autofocus',
		'disabled',
		'form',
		'formaction',
		'formenctype',
		'formmethod',
		'formnovalidate',
		'formtarget',
		'name',
		'type',
		'value'
	);
	public function __construct( $type, $content, array $properties = array() ) {
		$properties['type'] = $type;
		parent::__construct( $properties, $content );
	}
	public function __toString(){
		return '<button '. $this->renderAttributes() .'>'. $this->getContent() .'</button>';
	}
}