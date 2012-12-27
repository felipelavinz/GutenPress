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
	 * @param string $element A fully qualified name of a form element class
	 * @param array $args An array of arguments passed to the element constructor
	 * @param array $properties A set of data properties
	 */
	public function __construct( $name, $element, array $args, array $properties = array() ){
		$this->name = $name;
		$this->element = $element;
		$this->args = $args;
		$this->properties = $properties;
	}
	public function __get( $key ){
		return $this->{ $key };
	}
	public function isMultiple(){
		return ! empty( $this->properties['multiple'] );
	}
}