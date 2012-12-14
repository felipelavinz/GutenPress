<?php

namespace GutenPress\Forms\Element;

class Input extends \GutenPress\Forms\FormElement{
	protected static $type;
	protected static $element_attributes = array(
		'accept',
		'alt',
		'autocomplete',
		'autofocus',
		'checked',
		'dirname',
		'disabled',
		'form',
		'formaction',
		'formenctype',
		'formmethod',
		'formnovalidate',
		'formtarget',
		'height',
		'list',
		'max',
		'maxlength',
		'min',
		'multiple',
		'name',
		'pattern',
		'placeholder',
		'readonly',
		'required',
		'size',
		'src',
		'step',
		'type',
		'value',
		'width'
	);
	public function __construct( $label, $name, array $properties = array() ){
		$properties['type'] = static::$type;
		parent::__construct( $label, $name, $properties );
	}
	public function __toString(){
		return '<input '. $this->renderAttributes() .'>';
	}
}