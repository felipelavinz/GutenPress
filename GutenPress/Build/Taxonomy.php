<?php

namespace GutenPress\Build;

use GutenPress\Forms as Forms;
use GutenPress\Helpers as Helpers;
use GutenPress\Validate as Validate;
use GutenPress\Forms\Element as Element;
use GutenPress\Validate\Validations as Validation;

class Taxonomy{
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
		add_action('admin_init', array($this, 'buildTaxonomy'));
		add_action('admin_notices', array($this, 'adminNotices'));
	}
	public function adminMenu(){
		// initialize variables just before adding the admin page, so they might be filtered
		$this->page_title = __('Custom Taxonomy generator', 'gutenpress');
		$this->menu_title = __('Taxonomy generator', 'gutenpress');
		$this->capability = apply_filters('gutenpress_build_taxonomy_capability', 'edit_plugins');
		$this->menu_slug  = 'gutenpress-build-taxonomy';
		$this->function   = apply_filters('gutenpress_build_taxonomy_function', array($this, 'adminPage'));
		$admin_page = add_management_page( $this->page_title, $this->menu_title, $this->capability, $this->menu_slug, $this->function );
		add_action( 'admin_print_scripts-'. $admin_page, array($this, 'enqueueAssets') );
	}
	public function adminNotices(){
		if ( empty($_GET['gp_msg']) )
			return;
		// define messages
		$msgs = apply_filters('gutenpress_build_taxonomy_notices', array(
			'taxonomy_created' => __('Custom taxonomy <strong>%1$s</strong> was correctly created. Please activate the plugin to enable', 'gutenpress')
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
		wp_enqueue_script('gp-build-post-type', $Assets->scriptUrl('Build-Taxonomy'), array('jquery'), self::version, true );
	}
	public function adminPage(){
		$form = new Forms\Form('gp-build-taxonomy', '', array('class' => 'gutenpress-form'));
		$form->addElement( new Element\InputText(
			__('Taxonomy slug', 'gutenpress'),
			$form->getName('taxonomy'),
			array(
				'id' => $form->getId('taxonomy'),
				'required' => 'required',
				'size' => 30,
				'maxlength' => 32,
				'description' => __('The name of the taxonomy; should be in <em>slug</em> form (no uppercase nor spaces)', 'gutenpress'),
				'description_inline' => true
			)
		));
		$form->addElement( new Element\InputCheckbox(
			__('Object type', 'gutenpress'),
			$form->getName('object_type'),
			$this->getObjectTypeOptions()
		));
		$form->addElement( new Element\InputText(
			__('Label', 'gutenpress'),
			$form->getName('label'),
			array(
				'id' => $form->getId('label'),
				'description' => __('General name for the taxonomy, <strong>plural</strong>', 'gutenpress')
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
						'id' => 'gp-build-taxonomy-labels-gender'
					)
				),
				new Element\InputText(
					__('Name', 'gutenpress'),
					$form->getName('labels][name'),
					array(
						'id' => $form->getId('labels-name'),
						'class' => 'regular-text',
						'data-number' => 'plural',
						'readonly' => 'readonly',
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
						'placeholder' => __('Name for one object of this taxonomy', 'gutenpress'),
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
					__('Update Item', 'gutenpress'),
					$form->getName('labels][update_item'),
					array(
						'data-number' => 'singular',
						/* translators: update item label masculine format */
						'data-format-masculine' => _x('Update %s', 'view item label masculine format', 'gutenpress'),
						/* translators: update item label feminine format */
						'data-format-feminine' => _x('Update %s', 'view item label feminine format', 'gutenpress'),
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
					__('New Item Name', 'gutenpress'),
					$form->getName('labels][new_item_name'),
					array(
						'data-number' => 'singular',
						/* translators: new item name label masculine format */
						'data-format-masculine' => _x('New %s name', 'new item name label masculine format', 'gutenpress'),
						/* translators: new item name label feminine format */
						'data-format-feminine' => _x('New %s name', 'new item name label feminine format', 'gutenpress'),
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
					__('Popular Items', 'gutenpress'),
					$form->getName('labels][popular_items'),
					array(
						'data-number' => 'plural',
						/* translators: popular items label masculine format */
						'data-format-masculine' => _x('Popular %s', 'Popular items label masculine format', 'gutenpress'),
						/* translators: popular items label femimine format */
						'data-format-feminine' => _x('Popular %s', 'Popular items label feminine format', 'gutenpress'),
						'class' => 'regular-text'
					)
				),
				new Element\InputText(
					__('Separate items with commas', 'gutenpress'),
					$form->getName('labels][separate_items_with_commas'),
					array(
						'data-number' => 'plural',
						/* translators: separate items with commas label masculine format */
						'data-format-masculine' => _x('Separate %s with commas', 'separate items with commas label masculine format', 'gutenpress'),
						/* translators: separate items with commas label femimine format */
						'data-format-feminine' => _x('Separate %s with commas', 'separate items with commas label feminine format', 'gutenpress'),
						'class' => 'regular-text'
					)
				),
				new Element\InputText(
					__('Add or remove items', 'gutenpress'),
					$form->getName('labels][add_or_remove_items'),
					array(
						'data-number' => 'plural',
						/* translators: add or remove label masculine format */
						'data-format-masculine' => _x('Add or remove %s', 'add or remove label masculine format', 'gutenpress'),
						/* translators: add or remove label femimine format */
						'data-format-feminine' => _x('Add or remove %s', 'add or remove label feminine format', 'gutenpress'),
						'class' => 'regular-text'
					)
				),
				new Element\InputText(
					__('Choose from most used', 'gutenpress'),
					$form->getName('labels][choose_from_most_used'),
					array(
						'data-number' => 'plural',
						/* translators: choose from most used label masculine format */
						'data-format-masculine' => _x('Choose from most used %s', 'choose from most used label masculine format', 'gutenpress'),
						/* translators: choose from most used label femimine format */
						'data-format-feminine' => _x('Choose from most used %s', 'choose from most used label feminine format', 'gutenpress'),
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
				)
			)
		) );
		$form->addElement( new Element\InputRadio(
			__('Public', 'gutenpress'),
			$form->getName('public'),
			array(
				'1' => __('True: Should this taxonomy be exposed in the admin UI', 'gutenpress'),
				'0' => __('False: This taxonomy should <strong>not</strong> be exposed in the admin UI', 'gutenpress')
			),
			array(
				'id' => $form->getId('public')
			)
		) );
		$form->addElement( new Element\YesNo(
			__('Show UI', 'gutenpress'),
			$form->getName('show_ui'),
			array(
				'description' => __('Whether to generate a default UI for managing this taxonomy in the admin', 'gutenpress'),
				'description_inline' => true,
				'data-public' => 'direct'
			)
		) );
		$form->addELement( new Element\YesNo(
			__('Show in Nav Menus', 'gutenpress'),
			$form->getName('show_in_nav_menus'),
			array(
				'description' => __('Whether this taxonomy is available for selection in navigation menus', 'gutenpress'),
				'description_inline' => true,
				'data-public' => 'direct'
			)
		) );
		$form->addELement( new Element\YesNo(
			__('Show tag cloud', 'gutenpress'),
			$form->getName('show_tagcloud'),
			array(
				'description' => __('Whether to allo the tag cloud widget to use this taxonomy', 'gutenpress'),
				'description_inline' => true,
				'data-public' => 'direct'
			)
		) );
		$form->addElement( new Element\YesNo(
			/* translators: Show admin column for a custom taxonomy */
			__('Show admin column', 'gutenpress'),
			$form->getName('show_admin_column'),
			array(
				'description' => __('Whether to allow automatic creation of taxonomy columns on associated post-type', 'gutenpress'),
				'description_inline' => true
			)
		));
		$form->addElement( new Element\YesNo(
			__('Hierarchical', 'gutenpress'),
			$form->getName('hierarchical'),
			array(
				'description' => __('Whether the taxonomy is hierarchical (has descendants, like categories) or not (like tags)', 'gutenpress'),
				'description_inline' => true
			)
		) );
		$form->addElement( new Element\InputRadio(
			__('Query Var', 'gutenpress'),
			$form->getName('query_var'),
			array(
				'1' => __('True: can use <code>/?{taxonomy}={term_slug}</code> to load an taxonomy term', 'gutenpress'),
				'0' => __('False: can\'t use <code>$taxonomy</code> to load a taxonomy', 'gutenpress')
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
					__('Hierarchical', 'gutenpress'),
					$form->getName('rewrite][hierarchical'),
					array(
						'class' => 'rewrite-opt'
					)
				)
			)
		) );
		$form->addElement( new Element\YesNo(
			/* translators: enable or disable the "sort" property on custom taxonomies */
			__('Sort', 'gutenpress'),
			$form->getName('sort'),
			array(
				'description' => __('Whether this taxonomy should remember the order in which terms are added to objects', 'gutenpress'),
				'description_inline' => true
			)
		));
		$form->addElement( new Element\InputSubmit(
			__('Submit', 'gutenpress'),
			'submit'
		) );
		$form->addElement( new Element\InputHidden(
			'action',
			'gutenpress-build-taxonomy'
		) );
		$form->addElement( new Element\WPNonce(
			'gutenpress-build-taxonomy',
			'_build_taxonomy_nonce'
		) );
		echo $form;
	}
	private function getObjectTypeOptions(){
		$options = array();
		$post_types = get_post_types( array(), 'objects' );
		foreach ( (array)$post_types as $name => $props ) {
			$options[ $name ] = $props->label;
		}
		return $options;
	}
	public function buildTaxonomy(){
		// not of our bussiness, so get out
		if ( ! isset($_POST['gp-build-taxonomy']) )
			return;
		try {
			if ( ! isset($_POST['_build_taxonomy_nonce']) || ! wp_verify_nonce( $_POST['_build_taxonomy_nonce'], 'gutenpress-build-taxonomy' ) ) {
				throw new Helpers\Exceptions\NonceFail( $_POST['_build_taxonomy_nonce'], 'gutenpress-build-taxonomy' );
			}

			$taxdata = Helpers\Arrays::filterRecursive( $_POST['gp-build-taxonomy'] );

			$rules = array(
				'taxonomy' => new Validation\Required(),
				'object_type' => new Validation\Required()
			);

			$validate = new Validate\Validate( $taxdata, $rules );
			if ( ! $validate->isValid() ) {
				echo '<pre>', print_r($validate->getErrorMessages(), true) ,'</pre>';
				exit;
			}

			$taxonomy = $taxdata['taxonomy'];
			unset( $taxdata['taxonomy'] );
			$args = $taxdata;

			$custom_taxonomy = new \GutenPress\Generate\Generators\Taxonomy( $taxonomy, $args );
			if( $custom_taxonomy->commit() ) {
				// redirect to plugins page, with some custom notification info
				$success_url = add_query_arg(array(
					'gp_msg' => 'taxonomy_created',
					'gp_msg_params' => array(
						urlencode( $custom_taxonomy->label )
					)
				), admin_url('plugins.php'));
				wp_redirect( $success_url, 303 );
				exit;
			} else {
				throw new \Exception( sprintf( __('Error creating custom taxonomy %1$s', 'gutenpress'), $taxonomy ) );
			}

		} catch ( \Exception $e ) {
			wp_die( $e->getMessage() );
		}
	}
}