<?php
/**
 * This class attempts to ease the management of WordPress shortcodes, by providing an standard way
 * to integrate shortcode options into the visual editor and generating shortcodes that are
 * obvious to use
 */
namespace GutenPress\Model;
use GutenPress\Forms as Forms;
use GutenPress\Forms\Element as Element;

class ShortcodeFactory{
	private $shortcodes;
	private $sc_classes;
	private static $instance;

	/**
	 * Set factory actions within WordPress
	 */
	private function __construct(){
		$this->setActions();
	}
	private function __clone(){	}
	private function __wakeup(){ }
	public static function getInstance(){
		if ( !isset(self::$instance) ){
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}

	/**
	 * Instantiate a shortcode class
	 * @param  string $classname The FQN of the shortcode class handler
	 * @return \GutenPress\Model\Shortcode A shortcode object
	 * @throws \Exception If the class does not inherit from \GutenPress\Model\Shortcode
	 */
	public static function create( $classname ){
		$factory   = self::getInstance();
		$shortcode = new $classname;
		if ( ! $shortcode instanceof \GutenPress\Model\Shortcode ) {
			throw new \Exception( sprintf( __('%s must be a subclass of \GutenPress\Model\Shortcode', 'gutenpress'), $classname ) );
		}

		// register wordpress shortcode
		$shortcode->register();

		// add to the internal shortcode list
		$factory->register( $classname, $shortcode );

		return $shortcode;
	}

	/**
	 * Remove registered shortcode object and delete tag
	 * @param  string $classname The name of the class handler
	 * @return bool False if wasn't registered, true if successfuly removed
	 */
	public static function destroy( $classname ){
		if ( ! isset($this->shortcodes[ $classname ]) )
			return false;
		$tag = $this->shortcodes[ $classname ]->tag;
		unset( $this->shortcodes[ $classname ]);
		unset( $this->sc_classes[ $tag ] );
		return true;
	}

	/**
	 * Register a shortcode within the factory
	 * @param  string                      $classname FQN of the classname
	 * @param  \GutenPress\Model\Shortcode $shortcode Instantiated shortcode
	 * @return void
	 */
	public function register( $classname, \GutenPress\Model\Shortcode $shortcode ){
		$this->shortcodes[ $classname ] = $shortcode;
		$this->sc_classes[ $shortcode->tag ] = $classname;
	}

	/**
	 * Remove from the list of registered shortcodes. Will also remove the shortcode
	 * @param  string $id The shortcode class name
	 * @return bool       False if wasn't on the list
	 */
	public function unregister( $id ){
		if ( !isset($this->shortcodes[$id]) )
			return false;
		unset($this->shortcodes[$id]);
		// @todo delete from sc_classes
		return true;
	}

	// Plug into WordPress

	private function setActions(){
		add_action('admin_enqueue_scripts', array($this, 'enqueueStuff'));
		add_filter('mce_buttons', array($this, 'addEditorButton'));
		add_filter('mce_external_plugins', array($this, 'registerButton'));
		add_action('wp_ajax_gutenpress_shortcode_get_composer', array($this, 'getComposeForm'));
		add_action('wp_ajax_gutenpress_shortcode_get_fields', array($this, 'getFormFields'));
	}

	public function enqueueStuff(){
		if ( ! in_array( get_current_screen()->base, array('post', 'page') ) )
			return;
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_style('jquery-ui-dialog');
		wp_enqueue_style('wp-jquery-ui-dialog');
		add_action('admin_head', array($this, 'l10nScript'));
	}

	/**
	 * Add localization variables for the shortcode manager scripts
	 * @return void
	 */
	public function l10nScript(){
		$lang_vars = array(
			'settings' => array(
				'ajax_url' => admin_url('admin-ajax.php')
			),
			'tinymce' => array(
				'button_title' => __('Shortcodes manager', 'gutenpress'),
				'longname' => __('Shortcodes manager', 'gutenpress')
			),
			'dialog' => array(
				'title' => __('Shortcodes manager', 'gutenpress'),
				'button_insert' => __('Insert generated shortcode', 'gutenpress'),
				'text_select' => __('Select the shortcode you want to insert', 'gutenpress')
			)
		);
		echo '<script type="text/javascript">';
			echo 'var GutenPress_Shortcodes_l10n = '. json_encode( $lang_vars ) .';';
		echo '</script>';
	}

	/**
	 * Add the shortcode manager button to the visual editor
	 * @param array $buttons Buttons array with our custom button added
	 */
	public function addEditorButton( $buttons ){
		array_push( $buttons, "|", "gutenpress_shortcode" );
		return $buttons;
	}

	/**
	 * Add the js file to the list of plugins loaded by tinyMCE
	 * @param  array $plugin_arr Array of tinyMCE
	 * @return array             Plugins URLs with our custom file appended
	 */
	public function registerButton( $plugin_arr ){
		$plugin_arr['gutenpress_shortcode'] = GUTENPRESS_URL .'/Assets/Javascript/Model-Shortcode.js';
		return $plugin_arr;
	}

	/**
	 * Create the shortcode compose form that will allow to select the desired shortcode
	 * @return void
	 */
	public function getComposeForm(){
		$form = new Forms\Form('gutenpress-shortcode-composer', '\GutenPress\Forms\View\GPShortcode');
		$form->addElement( new Element\Select(
			__('Select the desired Shortcode', 'gutenpress'),
			'shortcode',
			$this->getShortcodeOptions(),
			array(
				'id' => 'gutenpress-shortcode-select'
			)
		) );
		echo $form;
		exit;
	}

	/**
	 * Echo the shortcode configuration form
	 * @return void
	 */
	public function getFormFields(){
		$shortcode_tag = trim( $_GET['shortcode'] );
		if ( ! isset($this->sc_classes[ $shortcode_tag]) )
			return false;
		$shortcode = $this->shortcodes[ $this->sc_classes[ $shortcode_tag ] ];
		echo $shortcode->configForm();
		exit;
	}

	/**
	 * Build a list of shortcode options
	 * @return array List of available shortcodes
	 */
	private function getShortcodeOptions(){
		$options = array( '' => __('(Select a Shortcode)', 'gutenpress') );
		foreach ( $this->shortcodes as $classname => $shortcode ) {
			$options[ $shortcode->tag ] = $shortcode->friendly_name;
		}
		return $options;
	}
}