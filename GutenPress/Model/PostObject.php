<?php

namespace GutenPress\Model;

class PostObject{
	/**
	 * @var object WP_Post
	 */
	protected $post;

	/**
	 * Holds information about multiple meta data
	 * @var array
	 */
	protected $multiple;

	private $know_multiples = false;

	private $know_properties = array();

	/**
	 * @var array
	 */
	protected $postmeta;

	public function __construct( \WP_Post $post, array $metadata = array(), array $multiple = array() ){
		$this->post = $post;
		if ( ! empty($metadata) ) {
			$this->postmeta = $metadata;
		}
		if ( ! empty($multiple) ) {
			$this->multiple = $multiple;
			$this->know_multiples = true;
		}
	}

	public function __get( $key ){
		// if we already got that property, inmediately return it
		if ( isset($this->know_properties[ $key ]) ) {
			return $this->know_properties[ $key ];
		}
		if ( $key === 'thumbnail' ) {
			$this->know_properties['thumbnail'] = $this->getThumbnail();
			return $this->know_properties['thumbnail'];
		}
		if ( $key === 'permalink' ) {
			$this->known_properties['permalink'] = get_permalink( $this->post->ID );
			return $this->known_properties['permalink'];
		}
		if ( $this->know_multiples === true ) {
			// we already know what fields can have multiple values
			if ( $this->multiple[ $key ] ) {
				$this->know_properties[ $key ] = get_post_meta( $this->post->ID, $key, false );
				return $this->know_properties[ $key ];
			}
			return $this->post->{$key};
		} else {
			// we don't know, so it _might_ have multiple values
			$value = get_post_meta( $this->post->ID, $key, false );
			if ( empty($value) ) {
				// probably not a postmeta, let WP_Post handle it
				return $this->post->{$key};
			} elseif ( count($value) === 0 ) {
				$this->know_properties[ $key ] = $value[0];
			} else {
				$this->know_properties[ $key ] = $value;
			}
			return $this->know_properties[ $key ];
		}
	}

	/**
	 * Check if some property exists
	 * @param string The name of the checked property
	 * @return bool
	 */
	public function __isset( $key ){
		return metadata_exists( 'post', $this->post->ID, $key );
	}

	/**
	 * Get a given property as an array
	 * @param string $key The meta_key to get
	 * @return array The meta value
	 */
	public function getMultiple( $key ){
		return get_post_meta( $this->post->ID, $key, false );
	}

	public function getThumbnail( $size = 'post-thumbnail', $attr = array() ){
		return get_the_post_thumbnail( $this->post->ID, $size, $attr );
	}
}