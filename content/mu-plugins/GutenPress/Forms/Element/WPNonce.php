<?php

namespace GutenPress\Forms\Element;

class WPNonce extends InputHidden{
	private $action;
	private $use_referer;
	public function __construct( $action = '-1', $name = '_wpnonce', $referer = true ) {
		$this->action = $action;
		$this->use_referer = (bool)$referer;
		parent::__construct( $name, '' );
	}
	public function __toString(){
		return wp_nonce_field( $this->action, $this->getAttribute('name'), $this->use_referer, false );
	}
}