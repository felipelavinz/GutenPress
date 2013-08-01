<?php

// Model\PostMetaData Fields
// Se debe usar a futuro el filtro 'gutenpress_metabox_field' ubicado en la clase Metabox
add_filter( 'filter_postmetadata_fields', 'multiLingualPostMetaData', 1, 1 );

// Default filter
add_filter( 'filter_form_fields', 'multilingualFormFields', 20, 1 );

function multiLingualPostMetaData( $fields ){
	static $langs;
	$meta_fields = array();
	$langs = qtrans_getSortedLanguages();
	foreach( $fields as $field ){
		if ( $field->getProperty('multilang') == true ) {
			// Prevent apply default filter
			$field->setProperty('multilang', false);
			foreach ( $langs as $lang ){
				$meta_fields[] = new GutenPress\Model\PostMetaData(
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

function multilingualFormFields( $elements ){
	static $langs;
	$meta_fields = array();
	$langs = qtrans_getSortedLanguages();
	foreach ( $elements as $field ){
		if ( $field->getProperty('multilang') == true ) {
			$class = get_class( $field );
			$value = $field->getValue();
			foreach ( $langs as $lang ){
				$field->setLabel( $field->getLabel() . ' ('. qtrans_getLanguageName($lang) .')' );
				$field->setName( $field->getProperty('name') . '['. $lang.']' );
				$field->setValue( $value[ $lang ] );
				$field->setProperty( 'value', $value[ $lang ] );
				$field->setAttribute( 'value', $value[ $lang ] );
				// Add new Element object
				$meta_fields[] = new $class(
					$field->getLabel(),
					$field->getProperty('name'),
					$field->getProperties()
				);
			}
		} else {
			$meta_fields[] = $field;
		}
	}
	$elements = $meta_fields;
	return $elements;
}