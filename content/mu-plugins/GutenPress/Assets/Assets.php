<?php

namespace GutenPress\Assets;

class Assets{
	private $base_url;
	private $prefix = '';
	private static $instance;
	private function __construct(){
		$this->base_url = plugins_url('', __FILE__);
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

	/**
	 * Use a string to prefix registered handles
	 * @param string Base prefix for registered handles
	 */
	public function setPrefix( $prefix ){
		$this->prefix = $prefix;
	}

	public function registerScript( $handle, $src, array $deps = array(), $ver = false , $in_footer = false ){
		wp_register_script(
			$this->sanitizeHandle( $handle ),
			$src,
			$deps,
			$in_footer
		);
	}
	public function registerStyle( $handle, $src, array $deps = array(), $ver = false , $media = 'all' ){
		wp_register_style(
			$this->sanitizeHandle( $handle ),
			$src,
			$deps,
			$ver,
			$media
		);
	}

	/**
	 * Sanitize a handle slug. Should contain only lowercase
	 * @param string $handle The asset handle
	 * @return string Sanitized asset handle
	 */
	private function sanitizeHandle( $handle ){
		return strtolower( apply_filters('gp_asset_handle', $this->prefix . $handle ) );
	}

	public function styleUrl( $handle ){
		return $this->base_url .'\/Css\/'. $handle .'.css';
	}
	public function scriptUrl( $handle ){
		return $this->base_url .'\/Javascript\/'. $handle .'.js';
	}
}
