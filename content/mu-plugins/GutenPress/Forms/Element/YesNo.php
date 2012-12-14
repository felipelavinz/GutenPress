<?php

namespace GutenPress\Forms\Element;

class YesNo extends Select{
	public function __construct( $label, $name, array $properties = array() ){
		$options = array(
			'1' => __('Yes', 'gutenpress'),
			'0' => __('No', 'gutenpress')
		);
		parent::__construct( $label, $name, $options, $properties );
	}
}