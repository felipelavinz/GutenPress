<?php

namespace GutenPress\Model;

abstract class PostMeta{
	/**
	 * Holds the meta data defined for this class
	 */
	protected $data = array();

	/**
	 * Metabox ID, also used to prepend meta keys from this class
	 */
	protected $id;

	final public function __construct(){
		$this->id = sanitize_key( $this->setId() );
		if ( empty( $this->id ) ) {
			throw new Exception( __('You must define a short string as metabox ID (will also be prepended to this class of meta data)', 'gutenpress') );
		}
		foreach ( $this->setDataModel() as $data ) {
			$this->registerData( $data );
		}
	}

	abstract protected function setId();
	abstract protected function setDataModel();

	private function registerData( PostMetaData $data ){
		static $key_seed_length;
		$key_seed_length = strlen( $this->id );
		$index = $data->name;
		if ( $key_seed_length + strlen( $index ) > 255 ) {
			throw new Exception( sprintf( __('Key %s it\'s too long. Please choose something shorter', 'gutenpress') ) );
		}
		if ( isset($this->data[ $index ]) ) {
			throw new \Exception( sprintf( __('Duplicate metadata name for key %s', 'gutenpress'), $index ) );
		}
		$this->data[ $index ] = $data;
	}
	public function __isset( $key ){
		return isset( $this->data[ $key ] );
	}
	public function __get( $key ){
		if ( $key === 'id' ) {
			return $this->id;
		}
		if ( $key === 'data' ) {
			return $this->data;
		}
		return $this->data[ $key ];
	}
	public function isMultiple( $key ){
		return isset( $this->data[ $key ]->properties['multiple'] );
	}
}