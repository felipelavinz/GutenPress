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
	public function __construct( $type = '', $content = '', array $properties = array() ) {
		parent::__construct( $properties, $content );
	}
	public function setProperties( array $properties ){
		$properties['type'] = $type;
		parent::setProperties( $properties );
	}
	public function __toString(){
		return '<button '. $this->renderAttributes() .'>'. $this->getContent() .'</button>';
	}
}