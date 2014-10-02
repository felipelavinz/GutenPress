<?php

namespace GutenPress\Forms\Element;

class InputTextSearchMultiple extends Input implements \GutenPress\Forms\MultipleFormElementInterface{
	protected static $type = 'hidden';
	public function __construct( $label = '', $name = '', array $properties = array() ){
		parent::__construct( $label, $name, $properties );
	}
	public function setName( $name ){
		// use name as an array
		parent::setName( $name .'[]' );
	}
	public function setProperties( array $properties ){
		$properties['type']  = self::$type;
		parent::setProperties( $properties );
	}
	public function renderAttributes(){
		$out = '';
		foreach ( $this->attributes as $key => $val ) {
			if($key != 'options'){
				$out  .= ' '. $key .'="'. esc_attr( $val ) .'"';
			}
		}
		return $out;
	}
	public function renderOptions(){
		$out = '';
		foreach ( $this->properties['options'] as $key => $val ) {
				$out  .= ' '. $key .'="'. esc_attr( $val ) .'"';
		}
		return $out;
	}
	public function __toString(){
		$fieldidentifier = explode('[', $this->name);
		$fieldidentifier = substr($fieldidentifier[1], 0 , strlen($fieldidentifier[1])-1);
		$out = '';
		$values = (array)$this->getValue();
		$out .= '<p class="input-text-search-multiple '.$fieldidentifier.'"><input field="'.$fieldidentifier.'" '. $this->renderOptions() .' class="searchmultiplefield regular-text" type="text"></p>';
		$out .= '<div class="tagchecklist box-text-search-multiple '.$fieldidentifier.'">';
		if(empty($values)){
			$out .= '<input '. $this->renderAttributes() .'>';
			$out .= '<span></span>';
		}
		foreach($values as $value){
			$this->setValue($value);
			$out .= '<input '. $this->renderAttributes() .'>';
			$out .= '<span><a id="element-'.$value.'" postid="'.$value.'" class="ntdelbutton label-text-search-multiple">X</a>&nbsp;'.get_the_title($value).'</span>';
		}
		$out .= '</div>';
		$assets = \GutenPress\Assets\Assets::getInstance();
		$assets->enqueueRegisteredScript( 'jquery-ui-autocomplete' );
		$assets->loadScript( 'Forms-Element-InputTextSearchMultiple' );
		return $out;
	}
}