<?php

namespace GutenPress\Model;

abstract class PostMeta{
	/**
	 * Holds the meta data defined for this class
	 * @var array
	 */
	protected $data = array();

	/**
	 * Metabox ID, also used to prepend meta keys from this class
	 * @var string
	 */
	protected $id;

	/**
	 * Instantiate the PostMeta object
	 * @throws \Exception When metabox ID it's not defined
	 * @throws \Exception If data model registration fails (duplicate or too long key)
	 * @todo Check for duplicate PostMeta id
	 */
	final public function __construct(){
		$this->id = sanitize_key( $this->setId() );
		if ( empty( $this->id ) ) {
			throw new \Exception( __('You must define a short string as metabox ID (will also be prepended to this class of meta data)', 'gutenpress') );
		}
	}

	/**
	 * Set a unique ID for this metabox. Will be used as HTML "id" and prepended to the meta data saved by this class
	 * @return string
	 */
	abstract protected function setId();

	/**
	 * Set the data model for this post-meta class. Should return an array of \GutenPress\Model\PostMetaData objects
	 * @return array
	 */
	abstract protected function setDataModel();

	public function initDataModel(){
		foreach ( $this->setDataModel() as $data ) {
			$this->registerData( $data );
		}
	}

	/**
	 * Register the given meta data for this class
	 * @param object PostMetaData definition
	 * @throws \Exception If key name it's too long
	 * @throws \Exception For duplicate key name
	 * @return void
	 */
	private function registerData( PostMetaData $data ){
		static $key_seed_length;
		$key_seed_length = strlen( $this->id );
		$index = $data->name;
		if ( $key_seed_length + strlen( $index ) > 255 ) {
			throw new \Exception( sprintf( __('Key %s it\'s too long. Please choose something shorter', 'gutenpress') ) );
		}
		if ( isset($this->data[ $index ]) ) {
			throw new \Exception( sprintf( __('Duplicate metadata name for key %s', 'gutenpress'), $index ) );
		}
		$this->data[ $index ] = $data;
	}

	/**
	 * Check if a given key exists on the defined data
	 * @param string $key The name of the meta data key to check
	 * @return bool Whether the given key it's a valid registered \GutenPress\Model\PostMetadata in this class
	 */
	public function __isset( $key ){
		if ( $key === 'data' ) {
			return empty($this->data);
		}
		return isset( $this->data[ $key ] );
	}

	/**
	 * Get a property from this object. Used to provide read-access to the object's properties
	 * @param string $key
	 * @return mixed The kind of data requested
	 */
	public function __get( $key ){
		if ( $key === 'id' ) {
			return $this->id;
		}
		if ( $key === 'data' ) {
			if ( empty($this->data) ) $this->initDataModel();
			return $this->data;
		}
		return $this->data[ $key ];
	}

	/**
	 * Check if a given property has multiple values
	 * @param string $key The property key to check
	 * @return bool Whether the given key it's saved as multiple fields
	 */
	public function isMultiple( $key ){
		return isset( $this->data[ $key ]->properties['multiple'] );
	}
}