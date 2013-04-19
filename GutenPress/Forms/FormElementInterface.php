<?php

namespace GutenPress\Forms;

interface FormElementInterface{
	public function setLabel( $label );
	public function setName( $name );
	public function setProperties( array $properties );
	public function setValue( $value );
}