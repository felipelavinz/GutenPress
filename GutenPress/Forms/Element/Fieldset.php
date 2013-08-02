<?php

namespace GutenPress\Forms\Element;

class Fieldset extends \GutenPress\Forms\FormElement implements \GutenPress\Forms\FieldsetElementInterface{
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
	public function __construct( $id = '', $label = '', array $properties = array(), $elements = array() ){
		if ( $id ) {
			$this->setId( $id );
		}
		if ( $label )
			$this->label = $label;
		if ( ! empty($elements) ) {
			foreach ( $elements as $element ) {
				$this->addElement( $element );
			}
		}
		parent::__construct( $label, null, $properties );
	}
	public function setId( $id ){
		$this->id = $id;
		$this->properties['id'] = $this->id;
		$this->attributes['id'] = $this->id;
	}
	public function getLabel(){
		return $this->label;
	}
	public function addElement( $element ) {
		if ( ! $element instanceof \GutenPress\Forms\Element ){
			throw new \Exception( __('$element must be an instance of \GutenPress\Forms\Element', 'gutenpress') );
		}
		$this->elements[] = $element;
		return $this;
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
	public static function filterMetaboxField( $element, $field, $form, $instance ){
		if ( get_class($element) === __CLASS__ ) {
			// $element it's a Fieldset
			if ( empty($field->properties['elements']) ) {
				throw new \Exception( __('Please add some elements within this Fieldset, otherwhise it will feel very empty', 'gutenpress') );
			}

			$element->setId( $form->getId( $field->name ) );

			// loop over the fieldset elements and instantiate them
			foreach ( $field->properties['elements'] as $fs_field ) {
				$field_name = $fs_field->name;
				$fs_element = $instance->createElement( $fs_field, $form );
				$fs_element->setName( $field_name );
				// on a simple fieldset, every field it's stored as a separate meta value
				$fs_element->setAttribute( 'name', $form->getName( $field->name .'_'. $field_name ) );
				$instance->setElementValue( $fs_element, $field->name .'_'. $field_name );
				$element->addElement( $fs_element );
			}
		}
		return $element;
	}
}
add_filter('gutenpress_metabox_field', array('\GutenPress\Forms\Element\Fieldset', 'filterMetaboxField'), 10, 4);