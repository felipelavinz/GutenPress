<?php

namespace GutenPress\Forms;

class MetaboxForm extends Form{
	public function __toString(){
		return (string) new $this->view( $this, $this->elements );
	}
}