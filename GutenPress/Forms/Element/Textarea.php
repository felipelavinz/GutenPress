<?php

namespace GutenPress\Forms\Element;

class Textarea extends \GutenPress\Forms\FormElement{
	protected static $element_attributes = array(
		'autofocus',
		'cols',
		'dirname',
		'disabled',
		'form',
		'maxlength',
		'name',
		'placeholder',
		'readonly',
		'required',
		'rows',
		'wrap'
	);
	public function __construct( $label = '', $name = '', array $properties = array() ){
		parent::__construct( $label, $name, $properties );
	}
	public function setProperties( array $properties ){
		if ( !isset($properties['cols']) )
			$properties['cols'] = apply_filters('gutenpress_forms_element_textarea_cols', 10);
		if ( !isset($properties['rows']) )
			$properties['rows'] = apply_filters('gutenpress_forms_element_textarea_rows', 5);
		parent::setProperties( $properties );
	}
	public function __toString(){
		return '<textarea'. $this->renderAttributes() .'>'. esc_textarea( $this->getValue() ) .'</textarea>';
	}
}