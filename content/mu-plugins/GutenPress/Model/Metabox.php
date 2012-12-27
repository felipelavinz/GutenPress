<?php

namespace GutenPress\Model;

class Metabox{

	protected $id;
	protected $title;
	protected $context;
	protected $priorty;
	protected $post_type;
	protected $callback_args;

	private $default_args = array(
		'context' => 'normal',
		'priority' => 'default'
	);

	private $model;
	private $postmeta;

	/**
	 * Create a new metabox
	 * @param string $postmeta Fully qualified name for a PostMeta class
	 * @param string $title Localized title of the metabox
	 * @param string $post_type The post type where the metabox will be shown
	 * @param array $args Metabox arguments
	 */
	public function __construct( $postmeta, $title, $post_type, array $args = array() ){
		$this->model = $postmeta;
		$this->title = $title;
		$this->post_type = $post_type;
		$this->args = wp_parse_args( $args, $this->default_args );
		$this->setActions();
	}
	protected function setActions(){
		// add meta box
		add_action( 'add_meta_boxes_'. $this->post_type, array($this, 'addMetabox') );
		// save
		add_action( 'save_post', array($this, 'saveMetabox'), 10, 2 );
	}
	final public function addMetabox(){
		// instantiate PostMeta
		$this->initPostMeta();
		add_meta_box( $this->id, $this->title, array($this, 'contentCallback'), $this->post_type, $this->args['context'], $this->args['priority'] );
	}
	private function initPostMeta(){
		$this->postmeta = new $this->model;
		if ( ! $this->postmeta instanceof PostMeta ) {
			throw new Exception( sprintf( __('%s must be a subclass of \GutenPress\Model\PostMeta', 'gutenpress'), $this->model ) );
		}
		$this->id = $this->postmeta->id;
	}
	final public function contentCallback(){
		global $post;
		// create "form"... wich are not actually forms, since they are part
		// of the greater "post" form
		$form = new \GutenPress\Forms\MetaboxForm( $this->id .'-form' );
		foreach ( $this->postmeta->data as $field ) {
			$element = $this->createElement( $field, $form );
			if ( is_callable( array($element, 'setValue') ) ) {
				$value = $field->isMultiple() ? get_post_meta( $post->ID, $this->id .'_'. $field->name, false ) : get_post_meta( $post->ID, $this->id .'_'. $field->name, true );
				$element->setValue( $value );
			}
			$form->addElement( $element );
		}
		// add a nonce for security...
		$form->addElement( new \GutenPress\Forms\Element\WPNonce(
			$this->id .'_metabox',
			$this->id .'_nonce'
		) );
		echo $form;
	}

	/**
	 * @todo Replace reflection magic with some interface compliance
	 */
	private function createElement( $field, $form ){
		global $post;

		// using Reflection it's kind of costly... alternatives?
		$element = new \ReflectionClass( $field->element );
		$params = $this->getElementParameters( $element );


		$name_i = array_search('name', $params);
		$properties_i = array_search('properties', $params);
		$args = array();

		$i=0; foreach ( $field->args as $arg ){
			if ( $i === $name_i ) {
				// prefix name with the form id, so all our data its sent as an array
				$args[] = $form->getName( $arg );
			} else {
				$args[] = $arg;
			}
		++$i; }

		return $element->newInstanceArgs( $args );
	}
	private function getElementParameters( \ReflectionClass $element ){
		$constructor = $element->getMethod('__construct');
		$params = $constructor->getParameters();
		$out = array();
		foreach ( $params as $param ) {
			$out[] = $param->name;
		}
		return $out;
	}
	final public function saveMetabox( $post_id, \WP_Post $post ){
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
			return;

		try{
			$this->checkPermissions( $post_id );
		} catch ( \Exception $e ) {
			wp_die( $e->getMessage() );
		}

		// no data sent
		if ( !isset($_POST[ $this->id .'-form' ]) )
			return;

		$data = $_POST[ $this->id .'-form' ];
		$data = \GutenPress\Helpers\Arrays::filterRecursive( $data );
		// no need for slashes; WordPress will take care of sanitizing
		$data = stripslashes_deep( $data );

		// empty data
		if ( empty($data) )
			return;

		foreach ( $this->postmeta->data as $meta ) {
			if ( isset( $data[ $meta->name ] ) ) {
				if ( is_array($data[ $meta->name] ) ) {
					// delete previous data
					delete_post_meta( $post_id, $this->id .'_'. $meta->name );
					foreach ( $data[ $meta->name ] as $value ) {
						add_post_meta( $post_id, $this->id .'_'. $meta->name, $value );
					}
				} else {
					update_post_meta( $post_id, $this->id .'_'. $meta->name, $data[ $meta->name ] );
				}
			} else {
				// if data it's defined, but no data is sent, try to delete the given key
				// this will take care of checkboxes and such
				delete_post_meta( $post_id, $this->id .'_'. $meta->name );
			}
		}
	}
	private function checkPermissions( $postid ){
		$this->initPostMeta();
		// nonce it's not present when not saving
		if ( ! isset($_POST[ $this->id .'_nonce']) ) {
			return;
		}

		// verify nonce
		if ( ! wp_verify_nonce( $_POST[ $this->id . '_nonce'], $this->id .'_metabox' ) ) {
			throw new \Exception( sprintf( __('It seems you\'re not allowed to save data on %s.', 'gutenpress' ), $this->title) );
		}
		// get permissions
		$post_type = get_post_type_object( $this->post_type );
		$edit_capability = $post_type->cap->edit_post;
		if ( ! current_user_can( $edit_capability, $postid ) ) {
			throw new \Exception( __('You are not authorized to edit this content', 'gutenpress') );
		}
		return true;
	}
}