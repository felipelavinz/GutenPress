<?php

namespace GutenPress\Model;

class PostMetaData{
	protected $name;
	protected $element;
	protected $properties;
	protected $args;
	/**
	 * Build a metadata bit
	 * @param string $name The field name
	 * @param string $label The human-friendly label
	 * @param string $element A fully qualified name of a form element class
	 * @param array $properties A set of data properties
	 */
	public function __construct( $name, $label, $element, array $properties = array() ){
		$this->name = $name;
		$this->label = $label;
		$this->element = $element;
		$this->properties = $properties;
	}
	public function __get( $key ){
		return $this->{ $key };
	}
	public function isMultiple(){
		return $this instanceof \GutenPress\Forms\MultipleFormElementInterface;
	}
	public function getProperty( $key ){
		return ( isset( $this->properties[ $key ] ) ? $this->properties[ $key ] : false ) ;
	}
	public function setProperty( $key, $value ){
		$this->properties[$key] = $value;
		return $this->properties;
	}
}