<?php

namespace GutenPress\Generate\Generators;

class Taxonomy extends \GutenPress\Generate\Generator{
	protected $args;
	private $args_map;
	protected $taxonomy;
	public function __construct( $taxonomy, $args ){
		$this->args = $args;
		$this->taxonomy = sanitize_key( $taxonomy );
		if ( strlen($this->taxonomy) > 32 ) {
			throw new \Exception( sprintf( _x('The taxonomy name %s must be 32 characters or less', 'gutenpress exception', 'gutenpress'), $this->taxonomy ) );
		}
		$this->args_map = new \GutenPress\Helpers\ArrayMap( $this->args );
		parent::__construct();
	}
	protected function setTargetPath(){
		$this->target_path = WP_PLUGIN_DIR .'/'. $this->prefix . strtolower($this->classname);
	}
	protected function setTemplateVars(){
		$this->template_vars = array(
			$this->taxonomy,
			$this->object_type,
			$this->label,
			$this->labels_name,
			$this->labels_singular_name,
			$this->labels_menu_name,
			$this->labels_all_items,
			$this->labels_edit_item,
			$this->labels_view_item,
			$this->labels_update_item,
			$this->labels_add_new_item,
			$this->labels_new_item_name,
			$this->labels_parent_item,
			$this->labels_parent_item .':',
			$this->labels_search_items,
			$this->labels_popular_items,
			$this->labels_separate_items_with_commas,
			$this->labels_add_or_remove_items,
			$this->labels_choose_from_most_used,
			$this->labels_not_found,
			$this->truthy( $this->public ),
			$this->truthy( $this->show_ui ),
			$this->truthy( $this->show_in_nav_menus ),
			$this->truthy( $this->show_tagcloud ),
			$this->truthy( $this->show_admin_menu ),
			$this->truthy( $this->hierarchical ),
			$this->truthy( $this->query_var ),
			$this->rewrite,
			$this->truthy( $this->sort ),
			$this->classname
		);
	}
	private function truthy( $val ){
		$true = array(
			'true',
			'yes',
			'on',
			'1'
		);
		if ( in_array( (string)$val, $true) ) {
			return 'true';
		}
		return 'false';
	}
	private function getClassName(){
		$words = str_replace('_', ' ', $this->taxonomy);
		$words = ucwords( $words );
		return str_replace(' ', '', $words);
	}
	public function __get( $key ){
		if ( $key === 'classname' )
			return $this->getClassName();
		if ( $key === 'show_in_menu' ) {
			return $this->getShowInMenu();
		}
		if ( $key === 'rewrite' ) {
			return $this->getRewrite();
		}
		if ( $key === 'object_type' ) {
			return $this->getObjectType();
		}
		return $this->args_map->$key;
	}
	private function quoteString( $str ){
		return "'". $str ."'";
	}
	public function getRewrite(){
		if ( empty($this->args['rewrite']) || (bool)$this->args['rewrite'] === false )
			return 'false';
		$vars = array();
		foreach ( $this->args['rewrite'] as $key => $val ) {
			if ( $key === 'slug' ) {
				$vars[] = "'$key' => __('$val', 'cpt_{$this->taxonomy}')";
			} else {
				$vars[] = "'$key' => ". $this->truthy( $val );
			}
		}
		return 'array( '. implode(', ', $vars) .' )';
	}
	private function getObjectType(){
		$object_type = empty($this->args['object_type']) ? array() : array_map( array($this, 'quoteString'), $this->args['object_type'] );
		return 'array( '. implode(', ', $object_type) .' )';
	}
	protected function prepareCommit(){
		$file = trailingslashit( $this->target_path ) . $this->prefix . strtolower($this->classname) .'.php';
		$this->parseTemplate( $this->readTemplate(), $this->template_vars, $file );
	}
}