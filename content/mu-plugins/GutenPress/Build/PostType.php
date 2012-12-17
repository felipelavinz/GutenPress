<?php

namespace GutenPress\Build;

use GutenPress\Forms as Forms;
use GutenPress\Helpers as Helpers;
use GutenPress\Validate as Validate;
use GutenPress\Forms\Element as Element;
use GutenPress\Validate\Validations as Validation;

class PostType{
	const version = 1;

	private static $instance;

	// menu page vars
	private $page_title;
	private $menu_title;
	private $capability;
	private $menu_slug;
	private $function;

	private function __construct(){
		// set WordPress actions for this module
		$this->actionsManager();
	}
	public static function getInstance(){
		if ( !isset(self::$instance) ){
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}
	public function __clone(){
		trigger_error('Clone is not allowed.', E_USER_ERROR);
	}
	private function actionsManager(){
		add_action('admin_menu', array($this, 'adminMenu'));
		add_action('admin_init', array($this, 'buildPostType'));
		add_action('admin_notices', array($this, 'adminNotices'));
	}
	public function adminMenu(){
		// initialize variables just before adding the admin page, so they might be filtered
		$this->page_title = __('Custom Post Type generator', 'gutenpress');
		$this->menu_title = __('Post Type generator', 'gutenpress');
		$this->capability = apply_filters('gutenpress_build_post_type_capability', 'edit_plugins');
		$this->menu_slug  = 'gutenpress-build-post_type';
		$this->function   = apply_filters('gutenpress_build_post_type_function', array($this, 'adminPage'));
		$admin_page = add_management_page( $this->page_title, $this->menu_title, $this->capability, $this->menu_slug, $this->function );
		add_action( 'admin_print_scripts-'. $admin_page, array($this, 'enqueueAssets') );
	}
	public function adminNotices(){
		if ( empty($_GET['gp_msg']) )
			return;
		// define messages
		$msgs = apply_filters('gutenpress_build_post_type_notices', array(
			'post_type_created' => __('Custom post type <strong>%1$s</strong> was correctly created. Please activate the plugin to enable', 'gutenpress')
		));
		if ( empty($msgs[ $_GET['gp_msg'] ]) )
			return;
		// sanitize msg vars
		$msg_params = array();
		foreach ( $_GET['gp_msg_params'] as $param ) {
			$msg_params[] = esc_html( $param );
		}
		echo '<div class="updated"><p>', vsprintf( $msgs[ $_GET['gp_msg'] ], $msg_params ) ,'</p></div>';
	}
	public function enqueueAssets(){
		$Assets = \GutenPress\Assets\Assets::getInstance();
		wp_enqueue_style('gp-admin-form-styles');
		wp_enqueue_script('gp-build-post-type', $Assets->scriptUrl('Build-PostType'), array('jquery'), self::version, true );
	}
	public function adminPage(){
		$form = new Forms\Form('gp-build-post_type', '', array('class' => 'gutenpress-form'));
		$form->addElement( new Element\InputText(
			__('Post type slug', 'gutenpress'),
			$form->getName('post_type'),
			array(
				'id' => $form->getId('post_type'),
				'required' => 'required',
				'size' => 25,
				'maxlength' => 20,
				'description' => __('A <strong>singular</strong> slug, used as <code>post_type</code> on new entries. Max. 20 characters, no capital letters or spaces', 'gutenpress'),
				'description_inline' => true,
				'required' => 'required'
			)
		) );
		$form->addElement( new Element\InputText(
			__('Label', 'gutenpress'),
			$form->getName('label'),
			array(
				'id' => $form->getId('label'),
				'description' => __('A <strong>plural</strong> descriptive name for the post type', 'gutenpress')
			)
		) );
		$form->addElement( new Element\Fieldset(
			$form->getId('labels'),
			__('Labels', 'gutenpress'),
			array(),
			array(
				new Element\Select(
					__('Use gender', 'gutenpress'),
					'labels_gender',
					array(
						'masculine' => _x('Masculine', 'gutenpress labels gender', 'gutenpress'),
						'feminine' => _x('Feminine', 'gutenpress labels gender', 'gutenpress')
					),
					array(
						'id' => 'gp-build-post_type-labels-gender'
					)
				),
				new Element\InputText(
					__('Name', 'gutenpress'),
					$form->getName('labels][name'),
					array(
						'id' => $form->getId('labels-name'),
						'placeholder' => __('Name for the post type, plural', 'gutenpress'),
						'readonly' => 'readonly',
						'class' => 'regular-text',
						'data-number' => 'plural',
						/* translators: name label masculine format */
						'data-format-masculine' => _x('%s', 'name label masculine format', 'gutenpress'),
						/* translators: name label feminine format */
						'data-format-feminine' => _x('%s', 'name label feminine format', 'gutenpress')
					)
				),
				new Element\InputText(
					__('Singular Name', 'gutenpress'),
					$form->getName('labels][singular_name'),
					array(
						'id' => $form->getId('labels-singular_name'),
						'placeholder' => __('Name for one object of this post type', 'gutenpress'),
						'class' => 'regular-text'
					)
				),
				new Element\InputText(
					__('Add New', 'gutenpress'),
					$form->getName('labels][add_new'),
					array(
						'data-number' => 'singular',
						/* translators: add new label masculine format */
						'data-format-masculine' => _x('Add new %s', 'add new label masculine format', 'gutenpress'),
						/* translators: add new label feminine format */
						'data-format-feminine' => _x('Add new %s', 'add new label feminine format', 'gutenpress'),
						'class' => 'regular-text'
					)
				),
				new Element\InputText(
					__('All Items', 'gutenpress'),
					$form->getName('labels][all_items'),
					array(
						'data-number' => 'plural',
						/* translators: all items label masculine format */
						'data-format-masculine' => _x('%s', 'all items label masculine format', 'gutenpress'),
						/* translators: all items label feminine format */
						'data-format-feminine' => _x('%s', 'all items label feminine format', 'gutenpress'),
						'class' => 'regular-text'
					)
				),
				new Element\InputText(
					__('Add New Item', 'gutenpress'),
					$form->getName('labels][add_new_item'),
					array(
						'data-number' => 'singular',
						/* translators: add new item label masculine format */
						'data-format-masculine' => _x('Add new %s', 'add new item label masculine format', 'gutenpress'),
						/* translators: add new item label feminine format */
						'data-format-feminine' => _x('Add new %s', 'add new item label feminine format', 'gutenpress'),
						'class' => 'regular-text'
					)
				),
				new Element\InputText(
					__('Edit Item', 'gutenpress'),
					$form->getName('labels][edit_item'),
					array(
						'data-number' => 'singular',
						/* translators: edit item label masculine format */
						'data-format-masculine' => _x('Edit %s', 'edit item label masculine format', 'gutenpress'),
						/* translators: edit item label feminine format */
						'data-format-feminine' => _x('Edit %s', 'edit item label feminine format', 'gutenpress'),
						'class' => 'regular-text'
					)
				),
				new Element\InputText(
					__('New Item', 'gutenpress'),
					$form->getName('labels][new_item'),
					array(
						'data-number' => 'singular',
						/* translators: new item label masculine format */
						'data-format-masculine' => _x('New %s', 'new item label masculine format', 'gutenpress'),
						/* translators: new item label feminine format */
						'data-format-feminine' => _x('New %s', 'new item label feminine format', 'gutenpress'),
						'class' => 'regular-text'
					)
				),
				new Element\InputText(
					__('View Item', 'gutenpress'),
					$form->getName('labels][view_item'),
					array(
						'data-number' => 'singular',
						/* translators: view item label masculine format */
						'data-format-masculine' => _x('View %s', 'view item label masculine format', 'gutenpress'),
						/* translators: view item label feminine format */
						'data-format-feminine' => _x('View %s', 'view item label feminine format', 'gutenpress'),
						'class' => 'regular-text'
					)
				),
				new Element\InputText(
					__('Search Items', 'gutenpress'),
					$form->getName('labels][search_items'),
					array(
						'data-number' => 'plural',
						/* translators: search items label masculine format */
						'data-format-masculine' => _x('Search %s', 'search items label masculine format', 'gutenpress'),
						/* translators: search items label femimine format */
						'data-format-feminine' => _x('Search %s', 'search items label feminine format', 'gutenpress'),
						'class' => 'regular-text'
					)
				),
				new Element\InputText(
					__('Not Found', 'gutenpress'),
					$form->getName('labels][not_found'),
					array(
						'data-number' => 'plural',
						/* translators: not found label masculine format */
						'data-format-masculine' => _x('No %s found', 'not found label masculine format', 'gutenpress'),
						/* translators: not found label feminine format */
						'data-format-feminine' => _x('No %s found', 'not found label feminine format', 'gutenpress'),
						'class' => 'regular-text'
					)
				),
				new Element\InputText(
					__('Not Found in Trash', 'gutenpress'),
					$form->getName('labels][not_found_in_trash'),
					array(
						'data-number' => 'plural',
						/* translators: not found in trash label masculine format */
						'data-format-masculine' => _x('No %s found in trash', 'not found in trash label masculine format', 'gutenpress'),
						/* translators: not found in trash label feminine format */
						'data-format-feminine' => _x('No %s found in trash', 'not found in trash label feminine format', 'gutenpress'),
						'class' => 'regular-text'
					)
				),
				new Element\InputText(
					__('Parent Item', 'gutenpress'),
					$form->getName('labels][parent_item_colon'),
					array(
						'data-number' => 'singular',
						/* translators: paren item label masculine format */
						'data-format-masculine' => _x('Parent %s', 'parent item label masculine format', 'gutenpress'),
						/* translators: parent item label feminine format */
						'data-format-feminine' => _x('Parent %s', 'parent item label feminine format', 'gutenpress'),
						'class' => 'regular-text'
					)
				),
				new Element\InputText(
					__('Menu Name', 'gutenpress'),
					$form->getName('labels][menu_name'),
					array(
						'data-number' => 'plural',
						/* translators: menu name label masculine format */
						'data-format-masculine' => _x('%s', 'menu name label masculine format', 'gutenpress'),
						/* translators: menu name label feminie format */
						'data-format-feminine' => _x('%s', 'menu name label feminine format', 'gutenpress'),
						'class' => 'regular-text'
					)
				)
			)
		) );
		$form->addElement( new Element\Textarea(
			__('Description', 'gutenpress'),
			$form->getName('description'),
			array(
				'rows' => 3,
				'class' => 'widefat',
				'placeholder' => __('A short descriptive summary of what the post type is', 'gutenpress')
			)
		) );
		$form->addElement( new Element\InputRadio(
			__('Public', 'gutenpress'),
			$form->getName('public'),
			array(
				'1' => __('True: Post type is intended for public use. This includes on the front end and in wp-admin', 'gutenpress'),
				'0' => __('False: Post type is not intended to be used publicly and should generally be unavailable in wp-admin and on the front end unless explicitly planned for elsewhere', 'gutenpress')
			),
			array(
				'id' => $form->getId('public'),
				'description' => __('Whether a post type is intended to be used publicly either via the admin interface or by front-end users', 'gutenpress')
			)
		) );
		$form->addElement( new Element\InputRadio(
			__('Exclude from search', 'gutenpress'),
			$form->getName('exclude_from_search'),
			array(
				'1' => __('True: search will not include posts of this post type', 'gutenpress'),
				'0' => __('False: search <strong>will</strong> posts of this post type', 'gutenpress')
			),
			array(
				'description' => __('Note: If you want to show these posts on taxonomy terms lists, you must set it to True', 'gutenpress'),
				'data-public' => 'reverse'
			)
		) );
		$form->addElement( new Element\YesNo(
			__('Publicly Queryable', 'gutenpress'),
			$form->getName('publicly_queryable'),
			array(
				'description' => __('Whether queries can be performed on the front end as part of <code>parse_request()</code>', 'gutenpress'),
				'description_inline' => true,
				'data-public' => 'direct'
			)
		) );
		$form->addElement( new Element\YesNo(
			__('Show UI', 'gutenpress'),
			$form->getName('show_ui'),
			array(
				'description' => __('Whether to generate a default UI for managing this post type in the admin', 'gutenpress'),
				'description_inline' => true,
				'data-public' => 'direct'
			)
		) );
		$form->addELement( new Element\YesNo(
			__('Show in Nav Menus', 'gutenpress'),
			$form->getName('show_in_nav_menus'),
			array(
				'description' => __('Whether this post type is available for selection in navigation menus', 'gutenpress'),
				'description_inline' => true,
				'data-public' => 'direct'
			)
		) );
		$form->addElement( new Element\Select(
			__('Show in menu', 'gutenpress'),
			$form->getName('show_in_menu'),
			array_merge(
				array(
					'0' => __('Do not display in the admin menu', 'gutenpress'),
					'1' => __('Display as a top level menu', 'gutenpress')
				),
				$this->getAdminMenuPages()
			),
			array(
				'description' => __('Where to show the post type in the admin menu. "Show UI" must be True', 'gutenpress'),
				'data-public' => 'direct'
			)
		) );
		$form->addElement( new Element\YesNo(
			__('Show in admin bar', 'gutenpress'),
			$form->getName('show_in_admin_bar'),
			array(
				'description' => __('Whether to make this post type available in the WordPress admin bar', 'gutenpress'),
				'description_inline' => true,
				'data-public' => 'direct'
			)
		) );
		$form->addElement( new Element\InputNumber(
			__('Menu position', 'gutenpress'),
			$form->getName('menu_position'),
			array(
				'maxlength' => 2,
				'description' => __('The position in the menu order the post type should appear. "Show in menu" must be true', 'gutenpress'),
				'description_inline' => true
			)
		) );
		$form->addElement( new Element\InputUrl(
			__('Menu icon', 'gutenpress'),
			$form->getName('menu_icon'),
			array(
				'placeholder' => 'http://',
				'description' => __('The URL to the icon to be used for this menu', 'gutenpress')
			)
		) );
		$form->addElement( new Element\InputCheckbox(
			__('Supports', 'gutenpress'),
			$form->getName('supports'),
			array(
				'title' => __('Title', 'gutenpress'),
				'editor' => __('Editor (content)', 'gutenpress'),
				'author' => __('Author', 'gutenpress'),
				'thumbnail' => __('Featured image', 'gutenpress'),
				'excerpt' => __('Excerpt', 'gutenpress'),
				'trackbacks' => __('Trackbacks', 'gutenpress'),
				'custom-fields' => __('Custom fields', 'gutenpress'),
				'comments' => __('Comments', 'gutenpress'),
				'revisions' => __('Revisions', 'gutenpress'),
				'page-attributes' => __('Page attributes: menu order, parent (if hierarchical is true)', 'gutenpress'),
				'post-formats' => __('Post formats', 'gutenpress')
			)
		) );
		$form->addElement( new Element\Fieldset(
			$form->getId('capability-type'),
			__('Capability Type', 'gutenpress'),
			array(
				'description' => __('A singular and plural slug to use as base to construct the capabilities.', 'gutenpress')
			),
			array(
				new Element\InputText(
					__('Singular slug', 'gutenpress'),
					$form->getName('capabilities]['),
					array(
						'class' => 'regular-text'
					)
				),
				new Element\InputText(
					__('Plural slug', 'gutenpress'),
					$form->getName('capabilities]['),
					array(
						'class' => 'regular-text'
					)
				)
			)
		));
		$form->addElement( new Element\YesNo(
			__('Hierarchical', 'gutenpress'),
			$form->getName('hierarchical'),
			array(
				'description' => __('Whether the post type is hierarchical. Allows Parent to be specified. The \'supports\' parameter should contain \'page-attributes\' to show the parent select box on the editor page', 'gutenpress'),
				'description_inline' => true
			)
		) );
		$form->addElement( new Element\YesNo(
			__('Has archive', 'gutenpress'),
			$form->getName('has_archive'),
			array(
				'description' => __('Enables post type archives', 'gutenpress'),
				'description_inline' => true,
				'data-public' => 'direct'
			)
		) );
		$form->addElement( new Element\Fieldset(
			$form->getId('rewrite'),
			__('Rewrite', 'gutenpress'),
			array(),
			array(
				new Element\YesNo(
					__('Enable', 'gutenpress'),
					$form->getName('rewrite_enable')
				),
				new Element\InputText(
					__('Slug', 'gutenpress'),
					$form->getName('rewrite][slug'),
					array(
						'class' => 'regular-text rewrite-opt',
						'placeholder' => __('Customize the permastruct slug', 'gutenpress')
					)
				),
				new Element\YesNo(
					__('With Front', 'gutenpress'),
					$form->getName('rewrite][with_front'),
					array(
						'class' => 'rewrite-opt'
					)
				),
				new Element\YesNo(
					__('Build feeds permastruct', 'gutenpress'),
					$form->getName('rewrite][feeds'),
					array(
						'class' => 'rewrite-opt'
					)
				),
				new Element\YesNo(
					__('Build pagination permastruct', 'gutenpress'),
					$form->getName('rewrite][pages'),
					array(
						'class' => 'rewrite-opt'
					)
				)
			)
		) );
		$form->addElement( new Element\InputRadio(
			__('Query Var', 'gutenpress'),
			$form->getName('query_var'),
			array(
				'1' => __('True: can use <code>/?{post_type}={single_post_slug}</code> to load an specific post', 'gutenpress'),
				'0' => __('False: can\'t use <code>$post_type</code> to load a post', 'gutenpress')
			),
			array(
				'data-public' => 'direct'
			)
		) );
		$form->addElement( new Element\YesNo(
			__('Can export?', 'gutenpress'),
			$form->getName('can_export'),
			array(
				'description' => __('Can this post type be exported?', 'gutenpress'),
				'description_inline' => true
			)
		) );
		$form->addElement( new Element\InputSubmit(
			__('Submit', 'gutenpress'),
			'submit'
		) );
		$form->addElement( new Element\InputHidden(
			'action',
			'gutenpress-build-post_type'
		) );
		$form->addElement( new Element\WPNonce(
			'gutenpress-build-post_type',
			'_build_post_type_nonce'
		) );
		echo $form;
	}
	private function getAdminMenuPages(){
		global $menu;
		$options = array();
		foreach ( $menu as $item ) {
			if ( ! empty($item[0]) ) {
				$options[$item[2]] = $item[0];
			}
		}
		return $options;
	}
	public function buildPostType(){
		// not of our bussiness, so get out
		if ( ! isset($_POST['gp-build-post_type']) )
			return;
		try {
			if ( ! isset($_POST['_build_post_type_nonce']) || ! wp_verify_nonce( $_POST['_build_post_type_nonce'], 'gutenpress-build-post_type' ) ) {
				throw new Helpers\Exceptions\NonceFail( $_POST['_build_post_type_nonce'], 'gutenpress-build-post_type' );
			}

			$postdata = Helpers\Arrays::filterRecursive( $_POST['gp-build-post_type'] );

			$rules = array(
				'post_type' => new Validation\Required(),
				'public' => array( new Validation\Required(), new Validation\Boolean() )
			);

			$validate = new Validate\Validate( $postdata, $rules );
			if ( ! $validate->isValid() ) {
				echo '<pre>', print_r($validate->getErrorMessages(), true) ,'</pre>';
				exit;
			}

			$post_type = $postdata['post_type'];
			unset( $postdata['post_type'] );
			$args = $postdata;

			$cpt = new \GutenPress\Generate\Generators\PostType( $post_type, $args );
			if( $cpt->commit() ) {
				// redirect to plugins page, with some custom notification info
				$success_url = add_query_arg(array(
					'gp_msg' => 'post_type_created',
					'gp_msg_params' => array(
						$cpt->label
					)
				), admin_url('plugins.php'));
				wp_redirect( $success_url, 303 );
				exit;
			} else {
				throw new \Exception( sprintf( __('Error creating post type %1$s', 'gutenpress'), $post_type ) );
			}

		} catch ( Exception $e ) {
			wp_die( $e->getMessage() );
		}
	}
}