<?php

namespace GutenPress\Forms\Element;

class Sortable extends \GutenPress\Forms\OptionElement{
	/**
	 * Default item format for sortable elements.
	 * Can be overwritten with $properties['item_format']; must consider variables:
	 * 1.- item label (string)
	 * 2.- item value (number)
	 * 3.- field name (string)
	 * @var string
	 */
	protected $item_format = '<li class="menu-item-handle"><span class="item-title">%1$s</span><span class="item-controls">↑↓</span><input type="hidden" value="%2$d" name="%3$s"></li>';
	public function renderItem( $val, $option ){
		return sprintf( $this->item_format, $option, $val, $this->name );
	}
	protected function sortItems(){
		uksort( $this->options, function($a, $b){
			$keys = array_keys( $this->options );
			$a_index = array_search($a, $this->value);
			$b_index = array_search($b, $this->value);
			if ( $a_index === $b_index )
				return 0;
			if ( $a_index > $b_index )
				return 1;
			return -1;
		});
	}
	public function __toString(){
		$out = '';
		if ( empty($this->options) ){
			/* translators: Sortable element has no options */
			$out .= '<div class="error inline"><p>';
				$out .= __( 'There are no elements to order', 'gutenpress' );
			$out .= '</p></div>';
		}

		$class = $this->getAttribute('class');
		if ( empty($class) )
			$this->setAttribute('class', 'ui-sortable gp-sortable');
		else
			$this->setAttribute('class', $class .' ui-sortable gp-sortable');
		$out .= '<ul'. $this->renderAttributes() .'>';

		// treat as an array
		$this->name .= '[]';

		// if value is set, sort elements before displaying
		if ( ! empty($this->value) ) {
			$this->sortItems();
		}

		$this->item_format = empty($this->properties['item_format']) ? $this->item_format : $this->properties['item_format'];
		foreach ( $this->options as $val => $option ) {
			$out .= $this->renderItem( $val, $option );
		}

		$out .= '</ul>';
		$assets = \GutenPress\Assets\Assets::getInstance();
		$assets->enqueueRegisteredScript( 'jquery-ui-sortable' );
		$assets->loadScript( 'Forms-Element-Sortable' );
		return $out;
	}
}