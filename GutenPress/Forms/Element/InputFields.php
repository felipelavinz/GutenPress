<?php

namespace GutenPress\Forms\Element;

class InputFields extends Input{
	//protected static $type = 'numbers';

	public function __toString(){
		$out = '';
		$value = $this;
		$out = var_export($value,TRUE);
		return $out;
	}
}