<?php

namespace GutenPress\Forms\Element;

class Fieldset extends \GutenPress\Forms\Element{
	protected static $element_attributes = array(
		'disabled',
		'form',
		'name'
	);
	protected $id;
	private $i = 1;
	protected $label;
	protected $elements;
	protected $view_properties = array(
		'wrap_class' => 'control-group'
	);
	public function __construct( $id, $label, array $properties = array(), $elements = array() ){
		$this->id = $id;
		$this->label = $label;
		$properties['id'] = $this->id;
		if ( ! empty($elements) ) {
			foreach ( $elements as $element ) {
				$this->addElement( $element );
			}
		}
		parent::__construct( $properties );
	}
	public function getLabel(){
		return $this->label;
	}
	public function addElement( \GutenPress\Forms\Element $element ) {
		$this->elements[] = $element;
	}
	public function __toString(){
		$out  = '';
		$sanitized_class = sanitize_html_class( $this->view_properties['wrap_class'] );
		$out .= '<fieldset'. $this->renderAttributes() .'>';
			foreach ( $this->elements as $element ) {
				$element->setAttribute('id', $this->getAttribute('id') .'-'. $this->i);
				$out .= '<div class="'. $sanitized_class .'">';
					if ( method_exists($element, 'getLabel') )
						$out .= '<label for="'. $element->getAttribute('id') .'">'. $element->getLabel() .'</label>';
					$out .= $element;
				$out .= '</div>';
				++$this->i;
			}
		$out .= '</fieldset>';
		return $out;
	}
}