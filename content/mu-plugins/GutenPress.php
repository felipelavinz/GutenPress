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

add_action('muplugins_loaded', 'gp_register_autoload');
function gp_register_autoload(){

	require_once WPMU_PLUGIN_DIR .'/GutenPress/Autoload/SplClassLoader.php';

	// register GutenPress autoloader
	$GutenPress = new SplClassLoader('GutenPress', WPMU_PLUGIN_DIR);
	$GutenPress->register();

}

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
	// $PostMetaBuilder = GutenPress\Build\PostMeta::getInstance();

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