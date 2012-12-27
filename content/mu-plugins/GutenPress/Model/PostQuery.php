<?php

namespace GutenPress\Model;

abstract class PostQuery implements \Iterator, \Countable{
	private $query;
	private $the_post;
	private $_wp_post;
	private $post_type;
	private $decorator;
	private $query_args;
	protected $metadata;
	private $are_multiple;
	final public function __construct( array $query_args = array() ){
		$this->query_args = $query_args;
		$this->post_type = $this->setPostType();
		$this->decorator = $this->setDecorator();
		if ( ! $this->decorator ) {
			$this->decorator = '\GutenPress\Model\PostObject';
		}
		if ( ! is_subclass_of($this->decorator, '\GutenPress\Model\PostObject') ) {
			throw new \Exception( sprintf( __('%s must be a subclass of \GutenPress\Model\PostObject', 'gutenpress'), $this->decorator ) );
		}
	}

	abstract protected function setPostType();

	abstract protected function setDecorator();

	private function getObjects(){
		$query_args = wp_parse_args( $this->query_args, array(
			'post_type' => $this->post_type
		) );
		$this->query = new \WP_Query( $query_args );
	}
	// so we can use this on alredy-made queries
	final public function setQuery( \WP_Query $query ){
		$this->query = $query;
		$this->post_type = $this->query->get('post_type');
		$this->preLoop();
	}
	public function getQuery(){
		return $this->query;
	}


	/**
	 * Countable
	 */
	public function count(){
		if ( !isset( $this->query ) ) {
			$this->getObjects();
			$this->preLoop();
		}
		return $this->query->post_count;
	}


	/**
	 * Iterator methods
	 */

	public function current(){
		if ( isset($this->the_post) ) {
			return $this->the_post;
		}
		if ( ! isset($this->query) ) {
			$this->rewind();
		}
		global $post;
		$this->query->the_post();
		$this->the_post = new $this->decorator( $post, (array)$this->metadata, (array)$this->are_multiple );
		return $this->the_post;
	}
	public function key(){
		return $this->query->current_post;
	}
	public function next(){
		$this->the_post = null;
	}
	public function rewind(){
		// check if
		if ( !isset( $this->query ) ) {
			global $wp_query;
			if ( $wp_query->is_archive() ) {
				$this->setQuery( $wp_query );
			} else {
				$this->getObjects();
			}
			$this->preLoop();
		} else {
			$this->query->rewind_posts();
		}
	}
	public function valid(){
		if ( $this->query->have_posts() ) {
			return true;
		} else {
			// loop it's ending, so let's cleanup
			wp_reset_query();
			global $post;
			$post = $this->_wp_post;
			return false;
		}
	}

	private function preLoop(){
		$metadata = apply_filters( $this->post_type .'_postquery_meta', array(), $this);
		foreach ( (array)$metadata as $meta ) {
			$this->registerMetadata( $meta );
		}
		global $post;
		$this->_wp_post = $post;
	}
	private function registerMetadata( \GutenPress\Model\Postmeta $meta ){
		$this->metadata[ $meta->id ] = $meta;
		foreach ( $meta->data as $data ) {
			$this->are_multiple[ $meta->id .'_'. $data->name ] = $data->isMultiple() ?: 0;
		}
	}
}