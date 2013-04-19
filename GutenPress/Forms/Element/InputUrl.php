<?php

namespace GutenPress\Forms\Element;

class InputUrl extends InputText{
	protected static $type = 'url';
	public function renderAttributes(){
		$out = '';
		foreach ( $this->getAttributes() as $key => $val ) {
			$out  .= $key === 'value' ? ' value="'. esc_url($val) .'"' : ' '. $key .'="'. esc_attr( $val ) .'"';
		}
		return $out;
	}
}