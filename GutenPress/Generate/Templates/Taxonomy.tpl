<?php
/*
Plugin Name: %3$s
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: %30$s Taxonomy
Version: 1
Author: Name Of The Plugin Author
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/

function register%30$sTaxonomy(){
	register_taxonomy(
		'%1$s',
		%2$s,
		array(
			'label' => _x('%3$s', '%1$s', 'custom_tax_%1$s'),
			'labels' => array(
				'name' => _x('%4$s', '%1$s', 'custom_tax_%1$s'),
				'singular_name' => _x('%5$s', '%1$s', 'custom_tax_%1$s'),
				'menu_name' => _x('%6$s', '%1$s', 'custom_tax_%1$s'),
				'all_items' => _x('%7$s', '%1$s', 'custom_tax_%1$s'),
				'edit_item' => _x('%8$s', '%1$s', 'custom_tax_%1$s'),
				'view_item' => _x('%9$s', '%1$s', 'custom_tax_%1$s'),
				'update_item' => _x('%10$s', '%1$s', 'custom_tax_%1$s'),
				'add_new_item' => _x('%11$s', '%1$s', 'custom_tax_%1$s'),
				'new_item_name' => _x('%12$s', '%1$s', 'custom_tax_%1$s'),
				'parent_item' => _x('%13$s', '%1$s', 'custom_tax_%1$s'),
				'parent_item_colon' => _x('%14$s', '%1$s', 'custom_tax_%1$s'),
				'search_items' => _x('%15$s', '%1$s', 'custom_tax_%1$s'),
				'popular_items' => _x('%16$s', '%1$s', 'custom_tax_%1$s'),
				'separate_items_with_commas' => _x('%17$s', '%1$s', 'custom_tax_%1$s'),
				'add_or_remove_items' => _x('%18$s', '%1$s', 'custom_tax_%1$s'),
				'choose_from_most_used' => _x('%19$s', '%1$s', 'custom_tax_%1$s'),
				'not_found' => _x('%20$s', '%1$s', 'custom_tax_%1$s')
			),
			'public' => %21$s,
			'show_ui' => %22$s,
			'show_in_nav_menus' => %23$s,
			'show_tagcloud' => %24$s,
			'show_admin_column' => %25$s,
			'hierarchical' => %26$s,
			'query_var' => %27$s,
			'rewrite' => %28$s,
			'sort' => %29$s
		)
	);
}
add_action('init', 'register%30$sTaxonomy');