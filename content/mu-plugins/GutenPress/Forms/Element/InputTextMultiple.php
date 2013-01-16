<?php

namespace GutenPress\Forms\Element;

class InputTextMultiple extends Input{
	protected $view_properties = array(
		'display_inline' => 0
	);
	public function __construct( $label, $name, array $properties = array() ){
		$view_properties = array_intersect_key($properties, $this->view_properties);
		$this->view_properties = wp_parse_args( $view_properties, $this->view_properties );
		// use name as an array
		$name .= '[]';
		parent::__construct( $label, $name, $properties );

		// The input type can be modified through the properties (to use url, email, etc)
		if ( empty($properties['type']) ) {
			$this->setAttribute('type', 'text');
		} else {
			$this->setAttribute('type', $properties['type']);
		}
	}
	public function __toString(){
		$out = '';
		$values = (array)$this->getValue();
		/* translators: Add new / remove links for multiple inputs */
		$action_links = '<a class="clone-parent" href="#clone">'. _x('Add new', 'multiple input action', 'gutenpress') .'</a> | <a class="remove-parent" href="#remove">'. _x('Remove',  'multiple input action', 'gutenpress') .'</a>';
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