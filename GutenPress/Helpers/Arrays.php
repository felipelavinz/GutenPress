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
}