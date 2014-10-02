<?php

namespace GutenPress\Forms\Element;

class InputTextSearch extends Input{
	protected static $type = 'hidden';
	public function __construct( $label = '', $name = '', array $properties = array() ){
		parent::__construct( $label, $name, $properties );
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
		$out .= '<p class="input-text-search '.$fieldidentifier.'"><input field="'.$fieldidentifier.'" '. $this->renderOptions() .' class="searchfield widefat" type="text"></p>';
		$out .= '<div class="tagchecklist box-text-search '.$fieldidentifier.'">';
		$value = $this->getValue();
		if(empty($value)){
			$out .= '<input '. $this->renderAttributes() .'>';
			$out .= '<span></span>';
		}else{
			$this->setValue($value);
			$out .= '<input '. $this->renderAttributes() .'>';
			$out .= '<span><a id="element-'.$value.'" postid="'.$value.'" class="ntdelbutton label-text-search">X</a>&nbsp;'.get_the_title($value).'</span>';
		}
		$out .= '</div>';
		$assets = \GutenPress\Assets\Assets::getInstance();
		$assets->enqueueRegisteredScript( 'jquery-ui-autocomplete' );
		$assets->loadScript( 'Forms-Element-InputTextSearch' );
		return $out;
	}
}

