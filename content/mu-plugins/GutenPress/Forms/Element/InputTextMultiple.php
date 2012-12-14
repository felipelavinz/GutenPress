<?php

namespace GutenPress\Forms\Element;

class InputTextMultiple extends Input{
	protected $view_properties = array(
		'display_inline' => 0
	);
	public function __construct( $label, $name, array $properties = array() ){
		// The input type can be modified through the propertis (to use url, email, etc)
		$properties['type'] = empty($properties['type']) ? 'text' : $properties['type'];
		$view_properties = array_intersect_key($properties, $this->view_properties);
		$this->view_properties = wp_parse_args( $view_properties, $this->view_properties );
		parent::__construct( $label, $name, $properties );
	}
	public function __toString(){

	}
}