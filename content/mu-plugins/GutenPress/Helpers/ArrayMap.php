<?php

namespace GutenPress\Helpers;

class ArrayMap{
	private $array = array();
	private $map;
	public function __construct( array $array, $delimiter = '\\' ){
		$this->array = $array;
		$this->flattenMap( $this->array );
		$this->delimiter = $delimiter;
	}
	public function __get( $key ){
		$key = '_'. $key;
		if ( isset($this->map[ $key ]) ) {
			return $this->map[ $key ];
		}
		return '';
	}
	/**
	 * Flatten the given array
	 **/
	private function flattenMap( $array, $seed_key = '' ){
		foreach ( $array as $key => $val ) {
			if ( is_array($val) ) {
				$this->flattenMap( $val, $seed_key .'_'. $key );
			} else {
				$this->map[ $seed_key .'_'. $key ] = $val;
			}
		}
	}
	/**
	 * Get the value from a given path
	 * @param array|string $path Path that will be searched. If it's a string, delimiter it's required
	 * @param string $delimiter The string that will be used as delimiter if $path it's a string
	 */
	public function getPath( $path, $delimiter = '' ){
		$path = $this->parsePath( $path, $delimiter );
		return $this->__get( $path );
	}
	/**
	 * Translate the give path to a searchable path
	 * @param array $path
	 */
	private function parsePath( $path , $delimiter = '' ){
		if ( is_string($path) ) {
			if ( empty($delimiter) ) {
				trigger_error("$delimiter it's required when $path it's a string", E_USER_ERROR);
			}
			$path = implode('_', explode($delimiter, $path));
		} else {
			$path = implode( '_', $path );
		}
		return $path;
	}
}