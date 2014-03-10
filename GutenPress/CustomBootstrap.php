<?php

// enable multilanguage support on forms (requires qtranslate)
// add_filter('gutenpress_form_elements', array('\GutenPress\Helpers\Multilanguage', 'filterFormFields'));

add_filter('gutenpress_generate_prefix', 'filter_generator_prefix', 10, 3);

function filter_generator_prefix( $prefix, $class, $var ){
	error_log( print_r(func_get_args(), true) );
	return $prefix;
}