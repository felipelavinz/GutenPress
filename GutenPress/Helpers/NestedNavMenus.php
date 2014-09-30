<?php

namespace GutenPress\Helpers;

class NestedNavMenus{
	public static function getVerticalMenuId(){
		$menu_items_ids = static::getAssociatedMenuItems();
		if ( empty($menu_items_ids) )
			return null;
		$menu_items_objects = new \WP_Query(array(
			'post__in' => $menu_items_ids,
			'post_type' => 'nav_menu_item',
			'posts_per_page' => -1,
			'fields' => 'ids'
		));
		if ( ! $menu_items_objects->have_posts() )
			return null;

		$menus_items_children = static::getMenuItemChildren( (array)$menu_items_objects->posts );

		if ( ! $menus_items_children->have_posts() ) {
			$associated_menus = wp_get_object_terms( $menu_items_ids, 'nav_menu' );
			return isset($associated_menus[0]->term_id) ? $associated_menus[0]->term_id : null;
		}

		/**
		 * Check if one of the children it's a nested menu
		 */
		foreach ( $menus_items_children->posts as $child ) {
			if ( $child->_menu_item_type === 'taxonomy' && $child->_menu_item_object === 'nav_menu' ) {
				return (int)$child->_menu_item_object_id;
			}
		}

		$associated_menus = wp_get_object_terms( wp_list_pluck($menus_items_children->posts, 'ID'), 'nav_menu' );

		return isset($associated_menus[0]->term_id) ? $associated_menus[0]->term_id : null;

	}

	public static function getMenuItemChildren( $menu_item_parent ){
		return new \WP_Query(array(
			'post_type' => 'nav_menu_item',
			'posts_per_page' => -1,
			'meta_query' => array(
				array(
					'key' => '_menu_item_menu_item_parent',
					'value' => $menu_item_parent,
					'compare' => 'IN',
					'type' => 'NUMERIC'
				)
			)
		));
	}

	public static function deeperNavMenu( $args = array() ){
		$vertical_menu_id = static::getVerticalMenuId();
		if ( ! $vertical_menu_id )
			return;

		$echo = isset( $args['echo'] ) && (bool)$args['echo'];

		$args['echo'] = false;
		$args['menu'] = (int)$vertical_menu_id;

		add_filter('wp_get_nav_menu_items', array(__CLASS__, 'filterMenuItems'), 10, 3);

		$wp_nav_menu = wp_nav_menu( $args );

		remove_filter('wp_get_nav_menu_items', array(__CLASS__, 'filterMenuItems'), 10);

		if ( $echo ) {
			echo $wp_nav_menu;
		} else {
			return $wp_nav_menu;
		}
	}

	public static function filterMenuItems( $items, $menu, $args ){
		$menu_items_ids = static::getAssociatedMenuItems();
		$menu_items_children = static::getMenuItemChildren( $menu_items_ids );
		$parents = array();
		foreach ( $menu_items_children->posts as $child ) {
			$parents[] = $child->ID;
			if ( $child->_menu_item_type === 'taxonomy' && $child->_menu_item_object === 'nav_menu' ) {
				return $items;
			}
		}

		$current_item_descendants = array();

		$items_by_parent = array();
		foreach ( $items as $item ) {
			$items_by_parent[ $item->menu_item_parent ][] = $item;
		}

		$level_parents = array_merge( $menu_items_ids, $parents );

		do {
			$deeper_level_parents = array();
			foreach ( $items_by_parent as $parent => $children ) {
				if ( in_array($parent, $level_parents) ) {
					foreach ( $children as $child ) {
						$current_item_descendants[] = $child;
						$deeper_level_parents[] = $child->ID;
					}
				}
			}
			$level_parents = $deeper_level_parents;
		} while ( ! empty($level_parents) );

		return $current_item_descendants;
	}

	/**
	 * Get nav menus associated to a certain thing
	 * @param  [type] $something [description]
	 * @return [type]            [description]
	 * @todo   Add conditions for URLs (links to archive pages or other objects)
	 */
	public static function getAssociatedMenuItems( $something = null ){
		if ( is_null($something) ) {
			$something = get_queried_object();
		}
		if ( $something instanceof \WP_Post ) {
			$object_id = $something->ID;
			$object_type = 'post_type';
			$taxonomy = '';
		} elseif ( isset($something->term_id) ) {
			$object_id = $something->term_id;
			$object_type = 'taxonomy';
			$taxonomy = $something->taxonomy;
		} elseif ( isset($something->name, $something->labels, $something->capability_type) ) {
			$url = get_post_type_archive_link( $something->name );
			return static::getLinkAssociatedMenuItems( $url );
		}
		return wp_get_associated_nav_menu_items( $object_id, $object_type, $taxonomy );
	}
	private static function getLinkAssociatedMenuItems( $url ){
		$associated_items = new \WP_Query(array(
			'post_type' => 'nav_menu_item',
			'meta_query' => array(
				array(
					'key' => '_menu_item_type',
					'value' => 'custom'
				),
				array(
					'key' => '_menu_item_url',
					'value' => trim( $url )
				)
			),
			'fields' => 'ids'
		));
		return $associated_items->posts;
	}
	public static function allowNestedMenus(){
		$nav_menu_tax = get_taxonomy('nav_menu');
		if ( is_object($nav_menu_tax) ) {
			$nav_menu_tax->show_in_nav_menus = 1;
		}
	}
}

add_action( 'init', array('GutenPress\Helpers\NestedNavMenus', 'allowNestedMenus'), 99 );