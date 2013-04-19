<?php

namespace GutenPress\Helpers\Exceptions;

class NonceFail extends \Exception{
	private $nonce;
	private $action;
	public function __construct( $nonce, $action ){
		$this->nonce  = $nonce;
		$this->action = $action;
	}
	public function __toString(){
		if ( empty($this->nonce) ) {
			return sprintf( __('Nonce verification Exception: nonce value it\'s empty on action \'%1$s\' (on %2$s:%3$s)', 'gutenpress'), $this->action, $this->getFile(), $this->getLine() );
		}
		return sprintf( __('Nonce verification Exception: \'%1$s\' it\'s not a valid nonce for the \'%2$s\' action (on %3$s:%4$s)', 'gutenpress'), $this->nonce, $this->action, $this->getFile(), $this->getLine() );
	}
}