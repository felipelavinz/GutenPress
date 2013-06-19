<?php
/**
 * GutenPress Shortcode Model
 */
namespace GutenPress\Model;

abstract class Shortcode{

	protected $tag;
	protected $friendly_name;
	protected $description;

	protected static $instance;

	public function __construct(){
		$this->setTag();
		$this->setFriendlyName();
		$this->setDescription();
		if ( ! isset($this->tag, $this->friendly_name, $this->description) ) {
			throw new \Exception( sprintf( __('You must set the required class variables for %s', 'gutenpress'), get_called_class() ) );
		}
	}

	/**
	 * Set the shortcode tag name ($this->tag)
	 */
	abstract public function setTag();

	/**
	 * Set the shortcode friendly name ($this->friendly_name)
	 */
	abstract public function setFriendlyName();

	/**
	 * Set a brief description for the shortcode ($this->description)
	 */
	abstract public function setDescription();

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

	/**
	 * Destroy and remove shortcode tag
	 * @return void
	 */
	public function __destroy(){
		$this->unregister();
	}

	/**
	 * Register the shortcode tag
	 * @return void
	 */
	final public function register(){
		add_shortcode( $this->tag, array($this, 'display') );
	}

	/**
	 * Remove the shortcode tag
	 * @return void
	 */
	final public function unregister(){
		remove_shortcode( $this->tag );
	}

}