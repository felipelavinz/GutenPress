<?php

namespace GutenPress\Forms\Element;

class WPEditor extends \GutenPress\Forms\FormElement{
	// wordpress defaults
	private $editor_settings = array(
		'wpautop'       => true,
		'media_buttons' => true,
		'textarea_name' => '',
		'textarea_rows' => '',
		'tabindex'      => '',
		'editor_css'    => '',
		'editor_class'  => '',
		'teeny'         => false,
		'dfw'           => false,
		'tinymce'       => true,
		'quicktags'     => true
	);
	public function __construct( $label = '', $name = '', array $properties = array() ){
		parent::__construct( $label, $name, $properties );
	}
	public function setProperties( array $properties ){
		if ( ! isset($properties['textarea_name']) )
			$properties['textarea_name'] = $this->name;
		$this->editor_settings = wp_parse_args( $properties, $this->editor_settings );
		parent::setProperties( $properties );
	}
	private function getSanitizedId(){
		$id = $this->getAttribute( 'id' ) . $this->name;
		$id = preg_replace( '/[^a-z]/', '', $id );
		return strtolower( $id );
	}
	public function __toString(){
		ob_start();
		wp_editor( $this->getValue(), $this->getSanitizedId(), $this->editor_settings );
		return ob_get_clean();
	}
}