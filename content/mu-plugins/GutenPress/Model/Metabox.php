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
		// allow file uploads
		add_action('post_edit_form_tag', array($this, 'allowFormUploads'));
	}
	final public function addMetabox(){
		// instantiate PostMeta
		$this->initPostMeta();
		add_meta_box( $this->id, $this->title, array($this, 'contentCallback'), $this->post_type, $this->args['context'], $this->args['priority'] );
	}
	private function initPostMeta(){
		$this->postmeta = new $this->model;
		if ( ! $this->postmeta instanceof PostMeta ) {
			throw new \Exception( sprintf( __('%s must be a subclass of \GutenPress\Model\PostMeta', 'gutenpress'), $this->model ) );
		}
		$this->id = $this->postmeta->id;
	}

	/**
	 * @todo Detect if it's necesarray (go through metabox elements to check if there's a file upload)
	 */
	final public function allowFormUploads(){
		echo ' enctype="multipart/form-data"';
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

		// handle file uploads
		if ( ! empty($_FILES[ $this->id .'-form' ]) ) {
			$uploads = $this->handleUploads( $post_id );
			$data = array_merge( $data, $uploads );
		}

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
	private function handleUploads( $post_id ){
		$files = array();

		foreach ( (array)$_FILES[ $this->id .'-form'] as $prop => $file ) {
			foreach ( $file as $key => $val ) {
				$files[ $key ][ $prop ] = $val;
			}
		}
		if ( empty($files) )
			return;

		$uploads = array();

		foreach ( $files as $name => $file ) {
			$date = date('Y/m');

			/**
			 * returns:
			 * - file
			 * - url
			 * - type
			 */
			$upload = wp_handle_upload( $file, array('test_form' => false), $date );

			// errors
			if ( isset($upload['error']) ) {
				throw new Exception( $upload['error'] );
			}
			if ( ! isset($upload['file']) ) {
				throw new Exception( sprintf( __('Error uploading file %s', 'gutenpress'), $file['name'] ) );
			}

			$current_user_id  = wp_get_current_user()->ID;
			$wp_filetype = wp_check_filetype( basename($upload['file']) );

			$attachment = array(
				'post_mime_type' => $wp_filetype,
				'guid' => $upload['url'],
				'post_title' => preg_replace('/\.[^.]+$/', '', basename($upload['file'])),
				'post_content' => '',
				'post_status' => 'inherit',
				'post_author' => $current_user_id,
				'post_parent' => $post_id
			);

			$attach_id = wp_insert_attachment( $attachment, $upload['file'], $post_id );
			$uploads[$name] = $attach_id;

			require_once ABSPATH .'wp-admin/includes/image.php';

			$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
			wp_update_attachment_metadata( $attach_id, $attach_data );

		}

		return $uploads;
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