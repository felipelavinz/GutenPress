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
			'label' => '%3$s',
			'labels' => array(
				'name' => '%4$s',
				'singular_name' => '%5$s',
				'menu_name' => '%6$s',
				'all_items' => '%7$s',
				'edit_item' => '%8$s',
				'view_item' => '%9$s',
				'update_item' => '%10$s',
				'add_new_item' => '%11$s',
				'new_item_name' => '%12$s',
				'parent_item' => '%13$s',
				'parent_item_colon' => '%14$s',
				'search_items' => '%15$s',
				'popular_items' => '%16$s',
				'separate_items_with_commas' => '%17$s',
				'add_or_remove_items' => '%18$s',
				'choose_from_most_used' => '%19$s',
				'not_found' => '%20$s'
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