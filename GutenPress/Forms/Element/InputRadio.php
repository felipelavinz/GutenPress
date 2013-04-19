<?php

namespace GutenPress\Forms\Element;

class InputRadio extends \GutenPress\Forms\OptionElement{
	protected static $type = 'radio';
	protected static $element_attributes = array(
		'accept',
		'alt',
		'autocomplete',
		'autofocus',
		'checked',
		'dirname',
		'disabled',
		'form',
		'formaction',
		'formenctype',
		'formmethod',
		'formnovalidate',
		'formtarget',
		'height',
		'list',
		'max',
		'maxlength',
		'min',
		'multiple',
		'name',
		'pattern',
		'placeholder',
		'readonly',
		'required',
		'size',
		'src',
		'step',
		'type',
		'value',
		'width'
	);
	protected $view_properties = array(
		'inline' => 0,
		'wrap_class' => 'control-group'
	);
	private $i = 1;
	public function __construct( $label = '', $name = '', array $options = array(), array $properties = array() ) {
		parent::__construct( $label, $name, $options, $properties );
	}
	public function setProperties( array $properties ){
		$view_properties = array_intersect_key($properties, $this->view_properties);
		$this->view_properties = wp_parse_args( $view_properties, $this->view_properties );
		if ( empty($properties['value']) ) {
			$this->setValue( array() );
		} else {
			$this->setValue( (array)$properties['value'] );
		}
		unset($properties['value']);
		parent::setProperties( $properties );
	}
	public function __toString(){
		global $post;
		$out = '';
		$base_id = $this->getAttribute('id');
		$sanitized_class = sanitize_html_class( $this->view_properties['wrap_class'] );
		if ( $this->view_properties['inline'] ) {
			foreach ( $this->options as $key => $val ) {
				$this->setAttribute('id', $base_id .'-'. $this->i );
				$out .= $this->inlineView( $key, $val, $sanitized_class );
				++$this->i;
			}
		} else {
			foreach ( $this->options as $key => $val ) {
				$this->setAttribute('id', $base_id .'-'. $this->i );
				$out .= $this->blockView( $key, $val, $sanitized_class );
				++$this->i;
			}
		}
		return $out;
	}
	protected function inlineView( $key, $val, $wrap_class ){
		$class = sanitize_html_class( $this->view_properties['wrap_class'] );
		return '<label class="'. $wrap_class .'">'. $this->controlView( $key, $val ) .'</label>';
	}
	protected function blockView( $key, $val, $wrap_class ){
		$class = sanitize_html_class( $this->view_properties['wrap_class'] );
		return '<p class="'. $wrap_class .'"><label>'. $this->controlView( $key, $val ) .'</label></p>';
	}
	protected function controlView( $key, $val ){
		$checked = in_array($key, (array)$this->getValue()) ? ' checked="checked"' : '';
		$this->setAttribute('value', $key);
		return '<input type="'. static::$type .'"'. $checked . $this->renderAttributes() .'> '. $val;
	}
}