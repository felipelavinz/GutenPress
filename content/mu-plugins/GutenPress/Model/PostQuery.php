<?php

namespace GutenPress\Model;

abstract class PostQuery implements \Iterator, \Countable{

	/**
	 * Holds the instance of WP_Query which we'll iterate over
	 * @access private
	 */
	private $query;

	/**
	 * Holds an instance of the current post decorator
	 * @access private
	 */
	private $the_post;

	/**
	 * Keep a copy of the global post, for those times when wp_reset_postdata doesn't work
	 * @access private
	 */
	private $_wp_post;

	/**
	 * The post type slug for this type of query
	 * @access private
	 */
	private $post_type;

	/**
	 * An array of arguments passed to WP_Query
	 * @access private
	 */
	private $query_args;

	/**
	 * An array of metadata definitions
	 * @access protected
	 */
	protected $metadata;

	/**
	 * The FQN of a decorator for WP_Post
	 * @access protected
	 */
	protected $decorator;

	/**
	 * An array of meta fields that should be interpreted as multiple
	 * @access private
	 */
	private $are_multiple;

	/**
	 * @param array $query_args An array of arguments passed on to WP_Query
	 */
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

	/**
	 * Set the post type for this type of query
	 * @return string A post type slug
	 */
	abstract protected function setPostType();

	/**
	 * Set the name for the WP_Post decorator
	 * @return string The FQN of a WP_Post decorator. It could be empty
	 */
	abstract protected function setDecorator();

	/**
	 * Parse query params and execute WP_Query
	 * @return void
	 */
	private function getObjects(){
		$query_args = wp_parse_args( $this->query_args, array(
			'post_type' => $this->post_type
		) );
		$this->query = new \WP_Query( $query_args );
	}

	/**
	 * Re-use an existing WP_Query (for instance, on archive templates where it doesn't make sense to repeat the query)
	 * @param object WP_Query object
	 * @throws \Exception Fails if $this->query was already set
	 */
	final public function setQuery( \WP_Query $query ){
		if ( $this->query instanceof \WP_Query ) {
			throw new \Exception( sprintf( __('The query for this object was already set. You might want to create a new %s', 'gutenpress'), get_called_class() ) );
		}
		$this->query = $query;
		$this->post_type = $this->query->get('post_type');
		$this->preLoop();
	}

	/**
	 * Return the current WP_Query
	 * @return object The current WP_Query
	 */
	public function getQuery(){
		return $this->query;
	}


	/**
	 * Implement countable interface, so we can do count($object)
	 * @return int The number of posts on the current query
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