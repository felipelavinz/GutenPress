<?php

namespace GutenPress\Model;

class PostObject{
	private $post;
	private $multiple;
	private $postmeta;
	final public function __construct( \WP_Post $post, array $metadata = array(), array $multiple = array() ){
		$this->post = $post;
		$this->multiple = $multiple;
		$this->postmeta = $metadata;
	}
	public function __get( $key ){
		if ( $key === 'thumbnail' ) {
			return $this->getThumbnail();
		}
		if ( $key === 'permalink' ) {
			return get_permalink( $this->post->ID );
		}
		if ( !empty($this->multiple[ $key ]) ) {
			return get_post_meta( $this->post->ID, $key, false );
		}
		return $this->post->{$key};
	}
	public function getThumbnail( $size = 'post-thumbnail', $attr = array() ){
		return get_the_post_thumbnail( $this->post->ID, $size, $attr );
	}
}