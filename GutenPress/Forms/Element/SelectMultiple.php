<?php

namespace GutenPress\Forms\Element;

class SelectMultiple extends \GutenPress\Forms\OptionElement implements \GutenPress\Forms\MultipleFormElementInterface{
	protected static $element_attributes = array(
		'autofocus',
		'disabled',
		'form',
		'multiple',
		'name',
		'required',
		'size'
	);
	public function setName( $name ){
		// use name as an array
		parent::setName( $name .'[]' );
	}
	public function __toString(){
		$out = '';
		$values = (array)$this->getValue();
		/* translators: Add new / remove links for multiple selects */
		$action_links = '<button class="btn-link clone-parent clone-select">'. _x('Add new', 'multiple select action', 'gutenpress') .'</button> | <button class="btn-link remove-parent remove-select">'. _x('Remove',  'multiple select action', 'gutenpress') .'</button>';
		do {
			$value = current( $values );
			$this->setValue( $value );
			$out .= '<p class="select-multiple">';
				$out .= '<select'. $this->renderAttributes() .'>';
					foreach ( $this->options as $key => $val ) {
						if ( is_string($val) ) {
							$out .= $this->renderOption( $key, $val, $value );
						} else {
							$out .= '<optgroup label="'. esc_attr($key) .'">';
								foreach ( $this->options as $k => $v ) $out .= $this->renderOption($k, $v, $value);
							$out .= '</optgroup>';
						}
					}
				$out .= '</select>';
				$out .= $action_links;
			$out .= '</p>';
			next( $values );
		} while ( $value );
		\GutenPress\Assets\Assets::getInstance()->loadScript('Forms-Element-SelectMultiple');
		return $out;
	}
	private function renderOption( $key, $val, $selected ){
		$is_selected = $selected === (string)$key ? ' selected="selected"' : '';
		return '<option value="'. esc_attr($key) .'"'. $is_selected .'>'. $val .'</option>';
	}
}