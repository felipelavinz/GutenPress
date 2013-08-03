<?php

namespace GutenPress\Forms;

interface FieldsetElementInterface{
	public function setId( $id );
	public function addElement( $element );
}