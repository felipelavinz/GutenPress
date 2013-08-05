<?php
/**
 * @package GutenPress
 * @version 0.1
 */
/*
Plugin Name: GutenPress
Plugin URI:
Description: Register autoload instances to manage GutenPress files and other bootstraping actions
Author: Felipe Lavin
Version: 0.1
Author URI: http://www.yukei.net
*/

function gp_register_autoload(){

	require_once __DIR__ .'/GutenPress/Autoload/SplClassLoader.php';

	// register GutenPress autoloader
	$GutenPress = new SplClassLoader('GutenPress', __DIR__);
	$GutenPress->register();

}
// call immediately, to avoid issues with network-activated plugins
gp_register_autoload();

if ( is_readable(__DIR__ .'/GutenPress/CustomBootstrap.php' ) ) {
	include_once __DIR__ .'/GutenPress/CustomBootstrap.php';
}

add_action('plugins_loaded', 'gp_admin_bootstrap');
function gp_admin_bootstrap(){
	if ( ! is_admin() )
		return;

	load_muplugin_textdomain('gutenpress', 'GutenPress/i18n/' );
	add_action('admin_enqueue_scripts', 'gp_admin_enqueue_scripts');
	add_action('admin_print_footer_scripts', 'gp_admin_print_footer_scripts');

	// post type model generator
	$PostTypeBuilder = GutenPress\Build\PostType::getInstance();
	$CustomTaxonomyBuilder = GutenPress\Build\Taxonomy::getInstance();

	do_action('gp_admin_bootstrap');
}

function gp_admin_enqueue_scripts(){
	// register css and javascript assets
	// instantiate class
	$Assets = \GutenPress\Assets\Assets::getInstance();
	$Assets->setPrefix('gp-admin-');
	// register assets
	$Assets->enqueueScript(
		'head_js-loader',
		$Assets->scriptUrl('head.load')
	);
	$Assets->enqueueStyle(
		'form-styles',
		$Assets->styleUrl('FormStyles')
	);
	do_action('gp_admin_register_assets', $Assets);
}

function gp_admin_print_footer_scripts(){
	$Assets = \GutenPress\Assets\Assets::getInstance();
	$Assets->loadEnqueuedScripts();
}

/**
 * Conditionally define some used constants.
 * All constants defined here should be customized hooking into init with a lower priority.
 * You can use the CustomBootstrap.php file to do that
 * @return void
 */
function gp_pluggable_constants(){
	if ( ! defined('GUTENPRESS_GMAPS_API_KEY') ) {
		define('GUTENPRESS_GMAPS_API_KEY', 'AIzaSyBAg3G1aMJzsWz7g4RdFidgSwflF4uY32A');
	}
}
add_action('init', 'gp_pluggable_constants', 9999);
