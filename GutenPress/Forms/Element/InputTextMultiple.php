<?php

namespace GutenPress\Forms\Element;

class InputTextMultiple extends Input implements \GutenPress\Forms\MultipleFormElementInterface{
	protected $view_properties = array(
		'display_inline' => 0
	);
	public function __construct( $label = '', $name = '', array $properties = array() ){
		parent::__construct( $label, $name, $properties );
	}
	public function setName( $name ){
		// use name as an array
		parent::setName( $name .'[]' );
	}
	public function setProperties( array $properties ){
		$view_properties = array_intersect_key($properties, $this->view_properties);
		$this->view_properties = wp_parse_args( $view_properties, $this->view_properties );
		// The input type can be modified through the properties (to use url, email, etc)
		if ( empty($properties['type']) ) {
			$this->setAttribute('type', 'text');
		} else {
			$this->setAttribute('type', $properties['type']);
		}
		parent::setProperties( $properties );
	}
	public function __toString(){
		$out = '';
		$values = (array)$this->getValue();
		/* translators: Add new / remove links for multiple inputs */
		$action_links = '<button class="btn-link clone-parent">'. _x('Add new', 'multiple input action', 'gutenpress') .'</button> | <button class="btn-link remove-parent">'. _x('Remove',  'multiple input action', 'gutenpress') .'</button>';
		do {
			$value = current( $values );
			$this->setValue( $value );
			$out .= '<p class="input-text-multiple"><input '. $this->renderAttributes() .'> '. $action_links .'</p>';
			next( $values );
		} while ( $value );
		\GutenPress\Assets\Assets::getInstance()->loadScript('Forms-Element-InputTextMultiple');
		return $out;
	}
}