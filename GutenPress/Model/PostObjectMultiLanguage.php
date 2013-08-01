<?php

namespace GutenPress\Model;

class PostObjectMultiLanguage extends PostObject{
	public function __get( $key ){
		if( parent::__isset( $key.'_'.qtrans_getLanguage() ) === true ){
			$key = $key.'_'.qtrans_getLanguage();
		}
		return parent::__get( $key );
	}
}