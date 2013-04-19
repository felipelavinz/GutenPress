<?php

namespace GutenPress\Assets;

class Assets{
	private $base_url;
	private $prefix = '';
	private static $instance;
	private static $enqueued_scripts = array();
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

	public function enqueueScript( $handle, $src, array $deps = array(), $ver = false , $in_footer = false ){
		wp_enqueue_script(
			$this->sanitizeHandle( $handle ),
			$src,
			$deps,
			$in_footer
		);
	}

	public function enqueueStyle( $handle, $src, array $deps = array(), $ver = false , $media = 'all' ){
		wp_enqueue_style(
			$this->sanitizeHandle( $handle ),
			$src,
			$deps,
			$ver,
			$media
		);
	}

	public function loadScript( $handle ){
		if ( ! in_array($handle, self::$enqueued_scripts) ) {
			// if it ends on .js, assume it's a full path
			self::$enqueued_scripts[] = stripos($handle, '.js') === false ? $this->scriptUrl( $handle ) : $handle;
		}
	}

	public function loadEnqueuedScripts(){
		if ( ! empty(self::$enqueued_scripts) ) {
			echo '<script type="text/javascript">';
				foreach ( self::$enqueued_scripts as $script ) :
				echo 'head.js("'. $script .'");'."\n";
				endforeach;
			echo '</script>';
		}
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
