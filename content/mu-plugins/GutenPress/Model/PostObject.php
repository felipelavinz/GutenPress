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

	/**
	 * @var array
	 */
	protected $postmeta;

	public function __construct( \WP_Post $post, array $metadata = array(), array $multiple = array() ){
		$this->post = $post;
		$this->multiple = $multiple;
		$this->postmeta = $metadata;
	}

	public function __get( $key ){
		if ( $key === 'thumbnail' ) {
			return $this->getThumbnail();
		}
		if ( $key === 'permalink' ) {
			$this->permalink = get_permalink( $this->post->ID );
			return $this->permalink;
		}
		if ( !empty($this->multiple[ $key ]) ) {
			return get_post_meta( $this->post->ID, $key, false );
		}
		return $this->post->{$key};
	}

	/**
	 * Check if some property exists
	 * @param string The name of the checked property
	 * @return bool
	 */
	public function __isset( $key ){
		return metadata_exists( 'post', $this->post->ID, $key );
	}

	public function getThumbnail( $size = 'post-thumbnail', $attr = array() ){
		return get_the_post_thumbnail( $this->post->ID, $size, $attr );
	}
}