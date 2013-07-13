<?php

namespace GutenPress\Forms\Element;

class LinkButton extends \GutenPress\Forms\Element{
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
		'href',
		'value'
	);
	public function __construct( $type = '' , $content = '', array $properties = array() ) {
		$properties['type'] = $type;
		parent::__construct( $properties, $content );
	}
	public function __toString(){
		return '<a '. $this->renderAttributes() .'>'. $this->getContent() .'</a>';
	}
}