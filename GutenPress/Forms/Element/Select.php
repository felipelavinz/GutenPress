<?php

namespace GutenPress\Forms\Element;

class Select extends \GutenPress\Forms\OptionElement{
	protected static $element_attributes = array(
		'autofocus',
		'disabled',
		'form',
		'multiple',
		'name',
		'required',
		'size'
	);
	public function __toString(){
		$out = '';
		$selected = (string)$this->getValue();
		$out .= '<select'. $this->renderAttributes() .'>';
			foreach ( $this->options as $key => $val ) {
				if ( is_string($val) ) {
					$out .= $this->renderOption( $key, $val, $selected );
				} else {
					// if it's not a string, it should be an array that's an optgroup
					// and each one of it's elements it's an option
					$out .= '<optgroup label="'. esc_attr($key) .'">';
						foreach ( $val as $k => $v ) $out .= $this->renderOption($k, $v, $selected);
					$out .= '</optgroup>';
				}
			}
		$out .= '</select>';
		return $out;
	}
	private function renderOption( $key, $val, $selected ){
		$is_selected = $selected === (string)$key ? ' selected="selected"' : '';
		return '<option value="'. esc_attr($key) .'"'. $is_selected .'>'. $val .'</option>';
	}
}
