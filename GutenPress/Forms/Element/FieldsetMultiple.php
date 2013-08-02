<?php

namespace GutenPress\Forms\Element;

class FieldsetMultiple extends Fieldset implements \GutenPress\Forms\MultipleFormElementInterface{
	private $i = 1;
	public function __toString(){
		$out  = '';
		$sanitized_class = sanitize_html_class( $this->view_properties['wrap_class'] );
		$this->getAttribute('class') ? $this->setAttribute( 'class', $this->getAttribute('class') .' fieldset-multiple' ) : 'fieldset-multiple';
		$values = (array)$this->getValue();
		$value  = current( $values );
		/* translators: Add new / remove links for multiple fieldsets */
		$action_links = '<p class="fieldset-multiple-actions actions"><button class="btn-link clone-fieldset">'. _x('Add new', 'multiple fieldset action', 'gutenpress') .'</button> | <button class="btn-link remove-fieldset">'. _x('Remove',  'multiple fieldset action', 'gutenpress') .'</button></p>';
		$out .= '<div class="fieldset-multiple-wrap">';
		do {
			$out .= '<fieldset'. $this->renderAttributes() .'>';
				foreach ( $this->elements as $element ) {
					$element->setAttribute('id', $this->getAttribute('id') .'-'. $this->i);
					$element->setAttribute('data-nameformat', $element->getAttribute('name'));
					if ( isset($value[$element->name]) ) {
						$element->setValue( $value[$element->name] );
					}
					$out .= '<div class="'. $sanitized_class .'">';
						if ( method_exists($element, 'getLabel') )
							$out .= '<label for="'. $element->getAttribute('id') .'">'. $element->getLabel() .'</label>';
						$out .= $element;
					$out .= '</div>';
					++$this->i;
				}
				$out .= $action_links;
			$out .= '</fieldset>';
			next( $values );
			$value = current( $values );
		} while ( $value );
		$out .= '</div>';
		\GutenPress\Assets\Assets::getInstance()->loadScript('Forms-Element-FieldsetMultiple');
		return $out;
	}
	public static function filterMetaboxField( $element, $field, $form, $instance ){
		if ( get_class($element) === __CLASS__ ) {
			// $element it's a Fieldset
			if ( empty($field->properties['elements']) ) {
				throw new \Exception( __('Please add some elements within this Fieldset, otherwhise it will feel very empty', 'gutenpress') );
			}

			// set value on MultipleFieldset
			// each copy of the fieldset it's stored as one serialized meta value
			// with this method, we're getting an array of arrays
			$instance->setElementValue( $element, $field->name );

			$element->setId( $form->getId( $field->name ) );

			// loop over the fieldset elements and instantiate them
			foreach ( $field->properties['elements'] as $fs_field ) {
				$field_name = $fs_field->name;
				$fs_element = $instance->createElement( $fs_field, $form );
				$fs_element->setName( $field_name );
				$fs_element->setAttribute( 'name', $form->getName( $field->name . '][__i__]['. $field_name ) );
				$element->addElement( $fs_element );
			}
		}
		return $element;
	}
}
add_filter('gutenpress_metabox_field', array('\GutenPress\Forms\Element\FieldsetMultiple', 'filterMetaboxField'), 10, 4);