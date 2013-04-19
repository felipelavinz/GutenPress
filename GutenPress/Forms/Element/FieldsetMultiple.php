<?php

namespace GutenPress\Forms\Element;

class FieldsetMultiple extends Fieldset{
	public function __toString(){
		$out  = parent::__toString();
		$out .= '<p class="actions"><a href="">Add new</a></p>';
		return $out;
	}
}