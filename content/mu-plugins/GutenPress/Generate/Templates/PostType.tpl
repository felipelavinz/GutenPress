<?php
/*
Plugin Name: %3$s
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: %16$s
Version: 1
Author: Name Of The Plugin Author
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/

class gp_cpt_%1$s extends GutenPress\Model\PostType{
	public static function registerPostType(){
		register_post_type( '%1$s', array(
			'label' => _x('%2$s', '%1$s', 'cpt_%1$s'),
			'labels' => array(
				'name' => _x('%3$s', '%1$s', 'cpt_%1$s'),
				'singular_name' => _x('%4$s', '%1$s', 'cpt_%1$s'),
				'add_new' => _x('%5$s', '%1$s', 'cpt_%1$s'),
				'all_items' => _x('%6$s', '%1$s', 'cpt_%1$s'),
				'add_new_item' => _x('%7$s', '%1$s', 'cpt_%1$s'),
				'edit_item' => _x('%8$s', '%1$s', 'cpt_%1$s'),
				'new_item' => _x('%9$s', '%1$s', 'cpt_%1$s'),
				'view_item' => _x('%10$s', '%1$s', 'cpt_%1$s'),
				'search_items' => _x('%11$s', '%1$s', 'cpt_%1$s'),
				'not_found' => _x('%12$s', '%1$s', 'cpt_%1$s'),
				'not_found_in_trash' => _x('%13$s', '%1$s', 'cpt_%1$s'),
				'parent_item_colon' => _x('%14$s', '%1$s', 'cpt_%1$s'),
				'menu_name' => _x('%15$s', '%1$s', 'cpt_%1$s')
			),
			'description' => _x('%16$s', '%1$s', 'cpt_%1$s'),
			'public' => %17$s,
			'exclude_from_search' => %18$s,
			'publicly_queryable' => %19$s,
			'show_ui' => %20$s,
			'show_in_nav_menus' => %21$s,
			'show_in_menu' => %22$s,
			'show_in_admin_bar' => %23$s,
			'menu_position' => %24$s,
			'menu_icon' => '%25$s',
			'capability_type' => %26$s,
			'hierarchical' => %27$s,
			'supports' => %28$s,
			'has_archive' => %29$s,
			'rewrite' => %30$s,
			'query_var' => %31$s,
			'can_export' => %32$s
		) );
	}
}
add_action( 'init', array('gp_cpt_%1$s', 'registerPostType') );