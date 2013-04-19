<?php

namespace GutenPress\Forms;

abstract class View{

	protected $form;
	protected $elements;

	public function __construct( \GutenPress\Forms\Form $form, array $elements = array() ){
		$this->form     = $form;
		$this->elements = $elements;
	}

	/**
	 * Return the string representation of the form
	 * @return string HTML markup for the form
	 */
	abstract public function __toString();
}