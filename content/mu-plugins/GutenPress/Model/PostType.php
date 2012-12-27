<?php

namespace GutenPress\Model;

abstract class PostType{
	private $args;
	private $post_type;
	final public function __construct(){
		$this->post_type = $this->setPostType();
		$this->args = $this->setPostTypeObject();
		$this->setActions();
	}
	final public function getPostType(){
		return $this->post_type;
	}
	final public function getPostTypeObject(){
		return $this->args;
	}
	abstract protected function setPostType();
	abstract protected function setPostTypeObject();
	private function setActions(){
		add_action( 'init', array($this, 'registerPostType') );
	}
	final public static function registerPostType(){
		$class = get_called_class();
		$post_type = new $class;
		$register = register_post_type( $post_type->getPostType(), $post_type->getPostTypeObject() );
		if ( is_wp_error( $register ) ) {
			throw new \Exception( $register->get_error_message() );
		}
	}
	public static function activatePlugin(){
		$admin = get_role('administrator');
		$class = get_called_class();
		$post_type = new $class;
		$post_type_object = (object)$post_type->getPostTypeObject();

		// add capabilites for admin
		$post_type_object->map_meta_cap = true;
		$post_type_object->capabilities = array();
		$capabilities = (array)get_post_type_capabilities( $post_type_object );
		foreach ( $capabilities as $key => $val ){
			$admin->add_cap( $val );
		}

		// regenerate permalinks structure
		flush_rewrite_rules();
	}
}