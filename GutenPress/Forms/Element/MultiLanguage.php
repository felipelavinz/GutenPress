<?php

namespace GutenPress\Forms\Element;

class Multilanguage extends Fieldset{
	public function addElement( $element ){
		$langs = qtrans_getSortedLanguages();
		foreach ( $langs as $lang ){
			$element = new $element;
			$element->setLabel( qtrans_getLanguageName($lang) );
			$element->setAttribute('id', $this->getAttribute('id') .'-'. $lang);
			$element->setAttribute('name', $this->id .'_'. $lang );
			parent::addElement( $element );
		}
	}
	// public function __toString(){
	// 	static $langs;
	// 	$out     = '';
	// 	$langs   = qtrans_getSortedLanguages();
	// 	$element = current($this->elements);
	// 	foreach ( $langs as $lang ){
	// 		$element->setAttribute('id', $this->getAttribute('id') .'-'. $lang);
	// 		$element->setAttribute('name', str_replace($element->name, $element->name.'_'. $lang, $this->getAttribute('name') ) );
	// 		// Output
	// 		$out .= '<div class="'. $sanitized_class .'">';
	// 			if ( method_exists($element, 'getLabel') )
	// 				$out .= '<label for="'. $element->getAttribute('id') .'">'. qtrans_getLanguageName($lang) .'</label>';
	// 			$out .= $element;
	// 		$out .= '</div>';
	// 		++$this->i;
	// 	}
	// 	return $out;
	// }
	// public function renderAttributes(){
	// 	$out = '';
	// 	print_r($this->getAttributes());
	// 	foreach ( $this->getAttributes() as $key => $val ) {
	// 		if( $key === 'value' ){
	// 			echo '<pre>'. print_r($val, true) .'</pre>';
	// 		}
	// 		$out  .= ' '. $key .'="'. esc_attr( $val ) .'"';
	// 	}
	// 	return $out;
	// }
}