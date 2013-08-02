<?php

namespace GutenPress\Generate\Generators;

class PostType extends \GutenPress\Generate\Generator{
	protected $args;
	protected $post_type;
	// protected $defaults = array();
	public function __construct( $post_type, $args ){
		$this->args = $args;
		$this->post_type = sanitize_key( $post_type );
		if ( strlen($this->post_type) > 20 ) {
			throw new Exception(  );
		}
		$this->args_map = new \GutenPress\Helpers\ArrayMap( $this->args );
		parent::__construct();
	}
	// protected function setDefaults(){
	// }
	protected function setTargetPath(){
		$this->target_path = WP_PLUGIN_DIR .'/'. $this->classname;
	}
	protected function setTemplateVars(){
		$this->template_vars = array(
			$this->post_type,
			$this->label,
			$this->labels_name,
			$this->labels_singular_name,
			$this->labels_add_new,
			$this->labels_all_items,
			$this->labels_add_new_item,
			$this->labels_edit_item,
			$this->labels_new_item,
			$this->labels_view_item,
			$this->labels_search_items,
			$this->labels_not_found,
			$this->labels_not_found_in_trash,
			$this->labels_parent_item_colon,
			$this->labels_menu_name,
			$this->description,
			$this->truthy( $this->public ),
			$this->truthy( $this->exclude_from_search ),
			$this->truthy( $this->publicly_queryable ),
			$this->truthy( $this->show_ui ),
			$this->truthy( $this->show_in_nav_menus ),
			$this->show_in_menu,
			$this->truthy( $this->show_in_admin_bar ),
			$this->menu_position,
			$this->menu_icon,
			$this->capability_type,
			$this->truthy( $this->hierarchical ),
			$this->supports,
			$this->truthy( $this->has_archive ),
			$this->rewrite,
			$this->truthy( $this->query_var ),
			$this->truthy( $this->can_expor ),
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
		$words = str_replace('_', ' ', $this->post_type);
		$words = ucwords( $words );
		return str_replace(' ', '', $words);
	}
	public function __get( $key ){
		if ( $key === 'classname' )
			return $this->getClassName();
		if ( $key === 'show_in_menu' ) {
			return $this->getShowInMenu();
		}
		if ( $key === 'menu_position' ) {
			return $this->getMenuPosition();
		}
		if ( $key === 'menu_icon' ) {
			return $this->getMenuIcon();
		}
		if ( $key === 'capability_type' ) {
			return $this->getCapabilityType();
		}
		if ( $key === 'supports' ) {
			return $this->getSupports();
		}
		if ( $key === 'rewrite' ) {
			return $this->getRewrite();
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
				$vars[] = "'$key' => __('$val', 'cpt_{$this->post_type}')";
			} else {
				$vars[] = "'$key' => ". $this->truthy( $val );
			}
		}
		return 'array( '. implode(', ', $vars) .' )';
	}
	public function getSupports(){
		$supports = empty($this->args['supports']) ? array() : array_map( array($this, 'quoteString'), $this->args['supports'] );
		return 'array( '. implode(', ', $supports) .' )';
	}
	public function getCapabilityType(){
		if ( empty($this->args['capabilities']) )
			return '';
		$capabilities = array_map( array($this, 'quoteString'), $this->args['capabilities'] );
		return 'array( '.  implode(', ', $capabilities) .' )';
	}
	public function getShowInMenu(){
		if ( empty( $this->args['show_in_menu']) ) {
			return 'null';
		}
		if ( is_numeric($this->args['show_in_menu']) ) {
			return $this->truthy( $this->args['show_in_menu'] );
		}
		return "'". $this->args['show_in_menu'] ."'";
	}
	public function getMenuIcon(){
		if ( empty($this->args['menu_icon']) )
			return 'null';
		$url = filter_var( $this->args['menu_icon'], FILTER_VALIDATE_URL );
		$url = empty( $url ) ? 'null' : esc_url( $url );
		return "'". $url ."'";
	}
	public function getMenuPosition(){
		return empty( $this->args['menu_position'] ) ? 'null' : (int)$this->args['menu_position'];
	}
	protected function prepareCommit(){
		$file = trailingslashit( $this->target_path ) . $this->classname .'.php';
		$this->parseTemplate( $this->readTemplate(), $this->template_vars, $file );
	}
}