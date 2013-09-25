<?php

namespace GutenPress\Forms;

class WidgetForm extends Form{
	protected $widget;
	public function __construct( \WP_Widget $widget, array $instance = array(), array $properties = array() ){
		$id = $widget->id;
		$view = '\GutenPress\Forms\View\WPSide';
		$this->values = $instance;
		$this->widget = $widget;
		parent::__construct( $id, $view, $properties );
	}
	public function __toString(){
		foreach ( $this->elements as &$element ){
			if ( isset( $this->values[$element->name] ) ) {
				$element->setValue( $this->values[ $element->name ] );
			}
			$element->setAttribute( 'name', $this->widget->get_field_name( $element->name ) );
			$element->setAttribute( 'id', $this->widget->get_field_id( $element->id) );
		}
		return (string) new $this->view( $this, $this->elements );
	}
}