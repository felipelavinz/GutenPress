<?php

namespace GutenPress\Model;

class Metabox{

	/**
	 * Hold the metabox ID
	 * @var string
	 * @access protected
	 */
	protected $id;

	/**
	 * Hold the (localized) metabox title
	 * @var string
	 * @access protected
	 */
	protected $title;

	/**
	 * Hold the metabox context (normal, advanced or side)
	 * @var string
	 * @access protected
	 */
	protected $context;

	/**
	 * Hold the metabox priority (high, low or default)
	 * @var string
	 * @access protected
	 */
	protected $priority;

	/**
	 * Hold the post type(s) the metabox was registered for
	 * @var string
	 * @access protected
	 */
	protected $post_type;

	/**
	 * Hold the callback args
	 * @access protected
	 */
	protected $callback_args;

	/**
	 * Set some default args for the metabox registration
	 * @var array
	 * @access private
	 */
	private $default_args = array(
		'context' => 'normal',
		'priority' => 'default'
	);

	/**
	 * Hold the FQN of a \GutenPress\Models\PostMeta class
	 * @var string
	 * @access private
	 */
	private $model;

	/**
	 * Hold an instance of the \GutenPress\Models\PostMeta object with the data definition
	 * @var object
	 * @access private
	 */
	private $postmeta;

	/**
	 * Create a new metabox
	 * @param string $postmeta Fully qualified name for a PostMeta class
	 * @param string $title Localized title of the metabox
	 * @param string $post_type The post type where the metabox will be shown
	 * @param array $args Metabox arguments
	 * @return void
	 */
	public function __construct( $postmeta, $title, $post_type, array $args = array() ){
		$this->model = $postmeta;
		$this->title = $title;
		$this->post_type = $post_type;
		$this->args = wp_parse_args( $args, $this->default_args );
		$this->setActions();
	}

	/**
	 * Set-up common metabox actions
	 * @return void
	 */
	protected function setActions(){
		// add meta box
		add_action( 'add_meta_boxes_'. $this->post_type, array($this, 'addMetabox') );
		// save
		add_action( 'save_post', array($this, 'saveMetabox'), 10, 2 );
		// allow file uploads
		add_action( 'post_edit_form_tag', array($this, 'allowFormUploads') );
	}

	/**
	 * Do the actual metabox registration for the given post-type
	 * @return void
	 */
	final public function addMetabox(){
		// instantiate PostMeta
		$this->initPostMeta();
		add_meta_box( $this->id, $this->title, array($this, 'contentCallback'), $this->post_type, $this->args['context'], $this->args['priority'] );
	}

	/**
	 * Init the \GutenPress\Model\PostMeta object
	 * @throws \Exception If the designated PostMeta it's not an instance of \GutenPress\Model\PostMeta
	 */
	private function initPostMeta(){
		$this->postmeta = new $this->model;
		if ( ! $this->postmeta instanceof PostMeta ) {
			throw new \Exception( sprintf( __('%s must be a subclass of \GutenPress\Model\PostMeta', 'gutenpress'), $this->model ) );
		}
		$this->id = $this->postmeta->id;
	}

	/**
	 * Enable file uploads on the #post submission form
	 * @todo Perhaps detect if it's necessary (go through metabox elements to check if there's a file upload?)
	 * @return void
	 */
	final public function allowFormUploads(){
		echo ' enctype="multipart/form-data"';
	}

	/**
	 * Create and echo the metabox content
	 * @return void
	 */
	final public function contentCallback(){
		global $post;
		// create "form"... wich are not actually forms, since they are part
		// of the greater "post" form
		if ( $this->args['context'] === 'side' ) {
			$form = new \GutenPress\Forms\MetaboxForm( $this->id .'-form', '\GutenPress\Forms\View\WPSide' );
		} else {
			$form = new \GutenPress\Forms\MetaboxForm( $this->id .'-form' );
		}
		foreach ( $this->postmeta->data as $field ) {
			$element = $this->createElement( $field, $form );
			if ( $element instanceof \GutenPress\Forms\FormElementInterface ) {
				$value = $element instanceof \GutenPress\Forms\MultipleFormElementInterface ? get_post_meta( $post->ID, $this->id .'_'. $field->name, false ) : get_post_meta( $post->ID, $this->id .'_'. $field->name, true );
				if ( ! empty($value) ) $element->setValue( $value );
			}
			if ( $element instanceof \GutenPress\Forms\FieldsetElementInterface ) {
				// $element it's a Fieldset
				if ( empty($field->properties['elements']) ) {
					throw new \Exception( __('Please add some elements within this Fieldset, otherwhise it will feel very empty', 'gutenpress') );
				}
				$element->setId( $form->getId( $field->name ) );
				foreach ( $field->properties['elements'] as $fs_field ) {
					$field_name = $fs_field->name;
					$fs_element = $this->createElement( $fs_field, $form );
					$fs_element->setName( $field_name );
					$fs_element->setAttribute( 'name', $form->getName( $field->name . '][__i__]['. $field_name ) );
					$element->addElement( $fs_element );
				}
				// $element->setAttribute('id', $form->getId( $element->name ) );
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

	private function addField( &$form, $field ){
		global $post;
		$element = $this->createElement( $field, $form );
		if ( is_callable( array($element, 'setValue') ) ) {
			$value = $element instanceof \GutenPress\Forms\MultipleFormElementInterface ? get_post_meta( $post->ID, $this->id .'_'. $field->name, false ) : get_post_meta( $post->ID, $this->id .'_'. $field->name, true );
			if ( ! empty($value) )
				$element->setValue( $value );
		}
		$form->addElement( $element );
	}

	/**
	 * Create the form element
	 * @return object \GutenPress\Forms\Element
	 */
	private function createElement( $field, $form ){
		global $post;
		$element = new $field->element;
		$properties = $field->properties;

		// it's most likely that the element it's a form field
		if ( $element instanceof \GutenPress\Forms\FormElementInterface ) {
			$element->setName( $form->getName( $field->name ) );
			$element->setLabel( $field->label );
			// set options for elements that should have them (radio buttons, checkboxes, selects)
			if ( $element instanceof \GutenPress\Forms\OptionsFormElementInterface && isset($properties['options']) ) {
				$element->setOptions( $properties['options'] );
				unset( $properties['options'] );
			}
		} else {
			if ( ! empty($properties['content']) ) {
				$element->setContent( $properties['content'] );
			}
		}

		$element->setProperties( $properties );

		return $element;
	}

	/**
	 * Save metabox data. Will add/update/delete data accordingly
	 * @param int $post_id The post ID
	 * @param object WP_Post object
	 * @throws \Exception If user doesn't have permission to save data
	 * @uses apply_filters() Calls 'filter_'. $this->id .'_metabox_data' to filter data before saving
	 * @uses do_action() Calls $this->id .'_metabox_data_updated' after data is saved
	 * @return void
	 */
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
		// no need for slashes; WordPress will take care of sanitizing when using "add/update_post_meta"
		$data = stripslashes_deep( $data );

		// handle file uploads
		if ( ! empty($_FILES[ $this->id .'-form' ]) ) {
			$uploads = $this->handleUploads( $post_id );
			$data = array_merge( $data, $uploads );
		}

		// and now, you may filter the metabox data to do some
		// sanitizing and formatting
		$data = apply_filters( 'filter_'. $this->id .'_metabox_data', $data, $this, $post );

		foreach ( $this->postmeta->data as $meta ) {
			if ( isset( $data[ $meta->name ] ) ) {
				if ( in_array( 'GutenPress\Forms\MultipleFormElementInterface', class_implements($meta->element) ) ) {
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

		// hook into this action if you need to do something after metadata was saved
		do_action( $this->id .'_metabox_data_updated', $data, $post_id, $post, $this );
	}

	/**
	 * Handle file uploads and save them as attachments
	 * @param int $post_id The post ID
	 * @throws \Exception On upload error
	 * @return array A collection of successful uploads, with $_FILES name as keys and attachment IDs as values
	 */
	private function handleUploads( $post_id ){
		$files = array();

		foreach ( (array)$_FILES[ $this->id .'-form'] as $prop => $file ) {
			foreach ( $file as $key => $val ) {
				$files[ $key ][ $prop ] = $val;
			}
		}

		// check if no file was uploaded
		foreach ( $files as $name => $upload ) {
			if ( $upload['error'] === 4 ) unset($files[$name]);
		}

		if ( empty($files) )
			return array();

		$uploads = array();

		foreach ( $files as $name => $file ) {
			$date = date('Y/m');

			$upload = wp_handle_upload( $file, array('test_form' => false), $date );

			// errors
			if ( isset($upload['error']) ) {
				throw new \Exception( $upload['error'] );
			}
			if ( ! isset($upload['file']) ) {
				throw new \Exception( sprintf( __('Error uploading file %s', 'gutenpress'), $file['name'] ) );
			}

			$current_user_id  = wp_get_current_user()->ID;
			$wp_filetype = wp_check_filetype( basename($upload['file']) );

			$attachment = array(
				'post_mime_type' => $wp_filetype['type'],
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

			$attach_data = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
			wp_update_attachment_metadata( $attach_id, $attach_data );

		}

		return $uploads;
	}

	/**
	 * Check if the current user has permission to save data on the given post
	 * @param int $postid The current Post ID
	 * @throws \Exception If user doesn't have permissions on this entry
	 * @return bool True if users passes checks
	 */
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