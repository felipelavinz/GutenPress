<?php

namespace GutenPress\Forms\View;

use GutenPress\Forms as Forms;
use GutenPress\Forms\Element as Element;

class BSVertical extends Forms\View{
	private $i = 1;
	public function __construct( \GutenPress\Forms\Form $form, array $elements = array() ){
		parent::__construct( $form, $elements );
	}
	public function __toString(){
		$out  = '';
		$errors = $this->form->getProperty('errors');
		$warnings = $this->form->getProperty('warnings');
		$infos = $this->form->getProperty('infos');
		$successes = $this->form->getProperty('successes');
		foreach ( $this->elements as $element ){
			$this->setElementViewAttributes( $element );
			if ( $element instanceof Element\InputHidden || $element->getAttribute('class') === 'hidden' ) {
				$out .= '<div class="hidden control-group">';
						$out .= (string)$element;
				$out .= '</div>';
			} elseif ( $element instanceof Element\InputButton || $element instanceof Element\Button || ! method_exists($element, 'getLabel') ) {
				$out .= '<div class="control-group">';
					$out .= (string)$element;
				$out .= '</div>';
			} else {
				$this->setInputSize( $element );
				$has_error = isset($errors[$element->name]) ? ' error' : '';
				$has_warning = isset($warnings[$element->name]) ? ' warning' : '';
				$has_info = isset($infos[$element->name]) ? ' info' : '';
				$has_success = isset($successes[$element->name]) ? ' success' : '';
				$out .= '<div class="control-group'. $has_error . $has_warning . $has_info . $has_success .'" id="'. $element->getAttribute('id') .'-group">';
					$out .= '<label for="'. $element->getAttribute('id') .'" class="control-label">';
						$out .= $element->getLabel();
					$out .= '</label>';
					$out .= '<div class="controls">';
						$out .= (string)$element;
						if ( $has_error )   $out .= '<span class="help-inline">'. $errors[$element->name] .'</span>';
						if ( $has_warning ) $out .= '<span class="help-inline">'. $warnings[$element->name] .'</span>';
						if ( $has_info )    $out .= '<span class="help-inline">'. $infos[$element->name] .'</span>';
						if ( $has_success ) $out .= '<span class="help-inline">'. $successes[$element->name] .'</span>';
					$out .= '</div>';
				$out .= '</div>';
			}
		}
		return $out;
	}
	protected function setElementViewAttributes( Element &$element ){
		$has_id = $element->getAttribute('id');
		if ( ! $has_id ) {
			$element->setAttribute('id', $this->form->getAttribute('id') .'-'. $this->i );
		}
		if ( $element instanceof Element\InputSubmit || $element instanceof Element\Button && $element->getAttribute('type') === 'submit' ) {
			$class = $element->getAttribute('class');
			$element->setAttribute('class', $class . ' button-primary');
		} elseif ( $element instanceof Element\InputButton || $element instanceof Element\Button ) {
			$element->setAttribute('class', 'button');
		}
		$has_value = $this->form->getValue( $element->name );
		if ( $has_value ) {
			$element->setValue( $has_value );
		}
		$this->i++;
	}
	/**
	 * Automatically set a suitable class for text input fields
	 */
	protected function setInputSize( Element &$element ){
		if ( ! $element instanceof Element\InputText )
			return;

		if ( $element->getAttribute('class') )
			return;

		$maxlength = $element->getAttribute('maxlength');
		if ( !empty($maxlength) ) {
			if ( $maxlength < 6 ) {
				$element->setAttribute('class', 'small-text');
				return;
			}
			if ( $maxlength > 40 ) {
				$element->setAttribute('class', 'widefat');
				return;
			}
		}

		if ( ! $element->getAttribute('size') ) {
			$element->setAttribute('class', 'regular-text');
		}
		return;
	}
	protected function getElementDescription( Element $element ){
		$description = $element->getProperty('description');
		$show_inline = $element->getProperty('description_inline');
		if ( $description ) {
			return $show_inline ? ' <span class="description">'. $description .'</span>' : '<p class="description">'. $description .'</p>';
		}
	}
}