<?php

namespace GutenPress\Helpers;
use GutenPress\Model as Model;

class Multilanguage{
	/**
	 * Add multilanguage fields support for metaboxes data
	 * @param array $fields Set of \GutenPress\Model\PostMetaData
	 */
	public static function postMetaData( $fields ){
		static $langs;
		$meta_fields = array();
		$langs = qtrans_getSortedLanguages();
		foreach( $fields as $field ){
			if ( $field->getProperty('multilang') == true ) {
				// Prevent apply default filter
				$field->setProperty('multilang', false);
				foreach ( $langs as $lang ){
					$meta_fields[] = new Model\PostMetaData(
						$field->name .'_'. $lang,
						$field->label .' ('. qtrans_getLanguageName($lang) .')',
						$field->element,
						$field->properties
					);
				}
			} else {
				$meta_fields[] = $field;
			}
		}
		return $meta_fields;
	}
	public static function filterFormFields( $elements ){
		static $langs;
		$meta_fields = array();
		$langs = qtrans_getSortedLanguages();
		foreach ( $elements as $field ){
			if ( $field->getProperty('multilang') == true ) {
				$class = get_class( $field );
				$value = $field->getValue();
				foreach ( $langs as $lang ){
					$new_field = new $class;
					$new_field->setLabel( $field->getLabel() .' ('. qtrans_getLanguageName($lang) .')' );
					$new_field->setName( $field->getProperty('name') .'['. $lang .']' );
					$new_field->setProperties( $field->getProperties() );
					if ( isset($value[$lang]) ) $new_field->setValue( $value[$lang] );
					$meta_fields[] = $new_field;
				}
			} else {
				$meta_fields[] = $field;
			}
		}
		return $meta_fields;
	}
}