<?php
/**
 * GutenPress Shortcode Model
 *
 * This class attempts to ease the management of WordPress shortcodes, by providing an standard way
 * to integrate shortcode options into the visual editor and generating shortcodes that are
 * obvious to use
 *
 * Should do:
 *
 * - registration of the shortcode
 * - add a unique "shortcode" button to the visual editor
 * - manage shortcode options and expose them to the user
 * - insert the generated shortcode into the editor
 * - (?) allow easy edition of a generated shortcode
 * - (?) allow a better representation of the shortcode within the editor
 */
namespace GutenPress\Model;

abstract class Shortcode{

	protected $tag;
	protected $friendly_name;
	protected $description;

	protected static $instance;

	public function __construct(){
		$this->tag = $this->getTag();
		$this->friendly_name = $this->getFriendlyName();
		$this->description = $this->getDescription();
	}

	abstract public function getTag();
	abstract public function getFriendlyName();
	abstract public function getDescription();

	/**
	 * Define the output of the shortcode. Must RETURN a string
	 * @param  array $atts    Array of arguments. Internally should be managed with
	 * @param  [type] $content [description]
	 * @return [type]          [description]
	 */
	abstract public function display( $atts, $content );

	/**
	 * Echo a configuration form for the current shortcode
	 * @return void Should echo whatever necessary
	 */
	abstract public function configForm(); // use MetaboxForm instead of just "form"

	/**
	 * Always return the corresponding class property (read-only)
	 * @param  string $key The property name
	 * @return mixed       The corresponding value
	 */
	public function __get( $key ){
		return $this->$key;
	}
	public function __destroy(){
		$this->unregister();
	}

	final public function register(){
		add_shortcode( $this->tag, array($this, 'display') );
	}

	final public function unregister(){
		remove_shortcode( $this->tag );
	}


}