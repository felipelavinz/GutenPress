<?php

namespace GutenPress\Helpers;

class AddImageSize{
	private static $byPostType;
	public static function forPostType( $post_type, $size, $width = 0, $height = 0, $crop = true ) {
		static::$byPostType[ $post_type ][ $size ] = array(
			'width' => absint( $width ),
			'height' => absint( $height ),
			'crop' => (bool)$crop
		);
	}
	public static function filterImageSizes( $sizes ){
		// the image is not being uploaded to an specific post
		if ( empty($_REQUEST['post_id']) ) {
			return $sizes;
		}
		$parent_id = absint( $_REQUEST['post_id'] );
		return static::addSizes( $parent_id, $sizes );
	}
	public static function filterRegenerateThumbnailImageSizes( $sizes ){
		// not regenerate thumbnails
		if ( ! isset( $_REQUEST['action'], $_REQUEST['id'] ) || $_REQUEST['action'] !== 'regeneratethumbnail' ) {
			return $sizes;
		}
		$attachment_id = absint( $_REQUEST['id'] );
		$attachment    = get_post( $attachment_id );
		$parent_id     = $attachment->post_parent;
		return static::addSizes( $parent_id, $sizes );
	}
	private static function addSizes( $image_parent_id, $sizes ){
		$parent = get_post( $image_parent_id );
		// we can't relate to this post
		if ( empty($parent) || ! $parent instanceof \WP_Post || ! isset($parent->post_type) ) {
			return $sizes;
		}
		// there are no registered sizes for this post_type
		if ( ! isset(static::$byPostType[ $parent->post_type] ) ) {
			return $sizes;
		}
		$post_type_sizes = Arrays::filterRecursive( static::$byPostType[ $parent->post_type ] );
		if ( empty($post_type_sizes) ) {
			return $sizes;
		}
		foreach ( $post_type_sizes as $name => $attrs ) {
			$sizes[ $name ] = $attrs;
		}
		return $sizes;
	}
}

add_filter('intermediate_image_sizes_advanced', array('\GutenPress\Helpers\AddImageSize', 'filterImageSizes'));
add_filter('intermediate_image_sizes_advanced', array('\GutenPress\Helpers\AddImageSize', 'filterRegenerateThumbnailImageSizes'));