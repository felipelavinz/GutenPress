<?php

namespace GutenPress\Model;

abstract class PostType{

	protected static $post_type;
	protected static $post_type_object;

	public function __construct(){

	}
	public static function getPostTypeObject(){
		return static::$post_type_object;
	}
	public static function getPostType(){
		return static::$post_type;
	}
	public static function registerPostType(){
		$post_type = static::setPostType();
		$post_type_object = register_post_type( $post_type, static::setPostTypeObject() );
		if ( is_wp_error( $post_type_object ) ) {
			throw new \Exception( $post_type_object->get_error_message() );
		}
		static::setActions();
	}
	private static function setActions(){
		$class = get_called_class();
		add_action('add_meta_boxes_'. $class::getPostType(), array($class, 'addMetaBoxes'));
	}
	/**
	 * Initialize metaboxes for this post type
	 */
	public static function addMetaBoxes(){
		foreach ( static::$registered_metaboxes as $metabox ) {
			$metabox->init();
		}
	}
	public static function registerMetabox( \GutenPress\Model\PostMeta $metabox ){
		self::$registered_metaboxes = $metabox;
	}
	abstract protected static function setPostType();
	abstract protected static function setPostTypeObject();

	/**
	 * Add capabilities for admin on plugin activation
	 */
	public static function activatePlugin(){
		$admin = get_role('administrator');
		$post_type_object = (object)static::setPostTypeObject();
		$post_type_object->map_meta_cap = true;
		$post_type_object->capabilities = array();
		$capabilities = get_post_type_capabilities( $post_type_object );
		foreach ( $capabilities as $key => $val ){
			$admin->add_cap( $val );
		}
	}
}