<?php

namespace GutenPress\Model;

class PostMetaData{
	protected $name;
	protected $element;
	protected $properties;
	public function __construct( $name, \GutenPress\Forms\Element $element, $properties ){
		$this->name = $name;
		$this->element = $element;
		$this->properties = $properties;
	}
}