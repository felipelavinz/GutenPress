<?php

namespace GutenPress\Validate;

interface ValidatorInterface{

	public function isValid( $value );

	public function getMessages();

}