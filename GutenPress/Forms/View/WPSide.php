<?php

namespace GutenPress\Forms\View;

use GutenPress\Forms as Forms;
use GutenPress\Forms\Element as Element;

class WPSide extends Forms\View{
	private $i = 1;
	public function __toString(){
		$out = '';
		$out .= '<div class="form">';
			foreach ( $this->elements as $element ){
				$this->setElementViewAttributes( $element );
				if ( $element instanceof Element\InputHidden ) {
					$out .= '<div class="hidden">';
							$out .= (string)$element;
					$out .= '</div>';
				} elseif ( $element instanceof Element\InputButton || $element instanceof Element\Button || ! method_exists($element, 'getLabel') ) {
					$out .= '<div class="clear">';
							$out .= (string)$element;
					$out .= '</div>';
				} else {
					$this->setInputSize( $element );
					$out .= '<div class="control-group">';
						$out .= '<p>';
							$out .= '<label for="'. $element->getAttribute('id') .'">'. $element->getLabel() .'</label>';
							$out .= (string)$element;
							$out .= $this->getElementDescription( $element );
						$out .= '</p>';
					$out .= '</div>';
				}
			}
		$out .= '</div>';
		return $out;
	}
	protected function setElementViewAttributes( Element &$element ){
		$has_id = $element->getAttribute('id');
		if ( ! $has_id ) {
			$element->setAttribute('id', $this->form->getAttribute('id') .'-'. $this->i );
		}
		if ( $element instanceof Element\InputSubmit || $element instanceof Element\Button && $element->getAttribute('type') === 'submit' ) {
			$element->setAttribute('class', 'button-primary');
		} elseif ( $element instanceof Element\InputButton || $element instanceof Element\Button ) {
			$element->setAttribute('class', 'button');
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
			$element->setAttribute('class', 'widefat');
		}
		return;
	}
	protected function getElementDescription( Element $element ){
		$description = $element->getProperty('description');
		$show_inline = $element->getProperty('description_inline');
		if ( $description ) {
			return $show_inline ? ' <span class="description">'. $description .'</span>' : '<br><span class="description">'. $description .'</span>';
		}
	}
}