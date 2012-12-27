<?php

namespace GutenPress\Forms\Element;

class InputTextMultiple extends Input{
	protected $view_properties = array(
		'display_inline' => 0
	);
	public function __construct( $label, $name, array $properties = array() ){
		$view_properties = array_intersect_key($properties, $this->view_properties);
		$this->view_properties = wp_parse_args( $view_properties, $this->view_properties );
		// use name as an array
		$name .= '[]';
		parent::__construct( $label, $name, $properties );

		// The input type can be modified through the properties (to use url, email, etc)
		if ( empty($properties['type']) ) {
			$this->setAttribute('type', 'text');
		} else {
			$this->setAttribute('type', $properties['type']);
		}
	}
	public function __toString(){
		$out = '';
		$values = $this->getValue();
		/* translators: Add new / remove links for multiple inputs */
		$action_links = '<a class="clone-parent" href="#clone">'. _x('Add new', 'multiple input action', 'gutenpress') .'</a> | <a class="remove-parent" href="#remove">'. _x('Remove',  'multiple input action', 'gutenpress') .'</a>';
		do {
			$value = current( $values );
			$this->setValue( $value );
			$out .= '<p><input '. $this->renderAttributes() .'> '. $action_links .'</p>';
			next( $values );
		} while ( $value );
		$out .= $this->addScript();
		return $out;
	}
	protected function addScript(){
		static $done;
		if ( $done === true )
			return;
		$done = true;
		$out  = '';
		$out .= '<script type="text/javascript">';
			$out .= '(function($){';
				$out .= 'jQuery(document).ready(function(){';
					$out .= '$(".clone-parent").on("click", function(event){';
						$out .= 'var el = $(this),';
							$out .= 'clone = el.parent().clone( true );';
							$out .= 'clone.find("input").val("");';
						$out .= 'el.parent().after( clone ).next().find("input").focus();';
						$out .= 'event.preventDefault();';
					$out .= '});';
					$out .= '$(".remove-parent").on("click", function(event){';
						$out .= 'var el = $(this),';
							$out .= 'parent = el.parent();';
						$out .= 'if ( parent.siblings("p").length ) {';
							$out .= 'parent.remove()';
						$out .= '}';
						$out .= 'event.preventDefault();';
					$out .= '});';
				$out .= '});';
			$out .= '})(jQuery);';
		$out .= '</script>';
		return $out;
	}
}