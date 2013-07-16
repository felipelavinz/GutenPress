<?php

namespace GutenPress\Forms\Element;

class DivContent extends \GutenPress\Forms\Element{
	protected static $element_attributes = array(
		'id',
		'name',
		'style'
	);
	public function __construct( $content = '', array $properties = array() ) {
		parent::__construct( $properties, $content );
	}
	public function __toString(){
		return '<div '. $this->renderAttributes() .'>'. $this->getContent() .'</div>';
	}
}