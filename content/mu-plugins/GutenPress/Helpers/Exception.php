<?php

namespace GutenPress\Helpers;

class Exception extends \Exception{
	function __toString(){
		return sprintf( __('Exception: %1$s (on %2$s:%3$s)', 'gutenpress'), $this->getMessage(), $this->getFile(), $this->getLine() );
	}
}