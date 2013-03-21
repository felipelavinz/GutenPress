<?php

namespace GutenPress\Forms;

/**
 * Programming interface for elements that can contain options
 */
interface OptionsFormElementInterface extends FormElementInterface{
	public function setOptions( array $options );
}