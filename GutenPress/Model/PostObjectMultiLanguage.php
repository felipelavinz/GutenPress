<?php

namespace GutenPress\Model;

class PostObjectMultiLanguage extends PostObject{
	public function __get( $key ){
		if( parent::__isset( $key.'_'.qtrans_getLanguage() ) === true ){
			$key = $key.'_'.qtrans_getLanguage();
		}
		return parent::__get( $key );
	}
	public function __isset( $key ){
		$exists_localized = metadata_exists( 'post', $this->post->ID, $key .'_'. qtrans_getLanguage() );
		if ( $exists_localized )
			return true;
		return parent::__isset( $key );
	}
}