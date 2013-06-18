<?php

namespace GutenPress\Helpers;

class Arrays{
	public static function filterRecursive( array $array ){
		foreach ( $array as &$value ) {
			if ( is_array($value) ) {
				$value = self::filterRecursive( $value );
			}
		}
		return array_filter( $array );
	}
	/**
	 * Re-index an array by a given key name
	 * @param  array  $array A multidimensional array or array of objects
	 * @param  string $key   The name of the key used as index
	 * @return array         A new array using selected key as indexes
	 */
	public static function indexByKey( array $array, $key ){
		$new = array();
		foreach ( $array as $item ){
			$i = (array)$item;
			$new[ $i[$key] ] = $item;
		}
		return $new;
	}
}