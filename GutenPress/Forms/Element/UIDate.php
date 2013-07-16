<?php

namespace GutenPress\Forms\Element;

class UIDate extends InputText{
	public function __toString(){
		$out  = '';

		$class = $this->getAttribute('class');
		if ( empty($class) )
			$this->setAttribute('class', 'gp-ui-datepicker');
		else
			$this->setAttribute('class', $class .' gp-ui-datepicker');

		$out .= parent::__toString();
		$assets = \GutenPress\Assets\Assets::getInstance();
		$assets->enqueueRegisteredScript( 'jquery-ui-datepicker' );
		$assets->loadScript( 'Forms-Element-UIDate' );
		return $out;
	}
}