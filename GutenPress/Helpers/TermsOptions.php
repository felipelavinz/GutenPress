<?php

namespace GutenPress\Helpers;

class TermsOptions extends \ArrayIterator{
	private $terms = array();
	public function __construct( $taxonomy, $args = array(), $flags = array() ){
		$args = wp_parse_args( $args, array(
			'orderby' => 'name',
			'order' => 'ASC'
		) );
		$flags = wp_parse_args( $flags, array(
			'show_option_none' => true
		) );
		$this->terms = get_terms( $taxonomy, $args );
		if ( $flags['show_option_none'] ) {
			array_unshift($this->terms, (object)array(
				'name' => _x('(None)', 'null term option', 'gutenpress'),
				'term_id' => ''
			));
		}
		parent::__construct( $this->terms );
	}
	public function current(){
		$cur = parent::current();
		return $cur->name;
	}
	public function key(){
		$key = parent::key();
		return $this->terms[$key]->term_id;
	}
}