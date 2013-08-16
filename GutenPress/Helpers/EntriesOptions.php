<?php

namespace GutenPress\Helpers;

class EntriesOptions extends \ArrayIterator{
	private $query;
	private $cur_post;
	public function __construct( $query_params = array(), $flags = array() ){
		$args = wp_parse_args( $query_params, array(
			'post_status' => 'publish',
			'orderby' => 'title',
			'order' => 'ASC',
			'posts_per_page' => -1
		));
		$flags = wp_parse_args( $flags, array(
			'show_option_none' => false
		));
		$this->query = new \WP_Query( $args );
		if ( $flags['show_option_none'] !== false ) {
			array_unshift($this->query->posts, (object)array(
				'ID' => '',
				'post_title' => is_string($flags['show_option_none']) ? $flags['show_option_none'] : _x('(None)', 'null entry option', 'gutenpress')
			));
		}
		parent::__construct( $this->query->posts );
	}
	public function current(){
		$this->cur_post = parent::current();
		return $this->cur_post->post_title;
	}
	public function key(){
		$key = parent::key();
		return $this->query->posts[$key]->ID;
	}
	public function getArrayCopy(){
		$array = array();
		foreach ( $this->query->posts as $post ){
			$array[ $post->ID ] = $post->post_title;
		}
		return $array;
	}
}