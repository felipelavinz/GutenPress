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
			foreach ( (array)$this->options as $key => $val ) {
				$is_selected = $selected === (string)$key ? ' selected="selected"' : '';
				$out .= '<option value="'. esc_attr($key) .'"'. $is_selected .'>'. $val .'</option>';
			}
		$out .= '</select>';
		return $out;
	}
}
