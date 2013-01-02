<?php

namespace GutenPress\Helpers;

class Attachment{

	protected $url;
	protected $title;
	protected $urlinfo;
	protected $pathinfo;
	protected $filesize;

	private $attachment;

	/**
	 * Construct an attachment object with common info as properties
	 * @param int $id File object ID on $wpdb->posts
	 * */
	public function __construct( $id ){
		$this->attachment = get_post( (int)$id );
		if ( ! $this->attachment )
			throw new \Exception( sprintf( __('File ID %d not found', 'gutenpress'), $id ), 404 );
		$this->url = wp_get_attachment_url( $id );
		$this->title = get_the_title( $id );
		$this->urlinfo = (object)parse_url( $this->url );
		$this->pathinfo = (object)pathinfo( $this->urlinfo->path );
		$this->filesize = static::getSize( $id );
		// @todo : get a friendy document type (e.g. "presentation", "text", "spreadsheet")
		// if ( $mime ) $this->type = the_doc_type($mime, false);
	}
	/**
	 * Magic __get so attributes can be protected and read-only
	 */
	public function __get( $key ){
		return $this->{ $key };
	}
	/**
	 * Get the file size of a given attachment
	 * @param int|object $id A WordPress attachment ID
	 * @param string $unit Measure unit. Will default to a sane human-readable unit
	 * @return string Human-readable file size
	 * @todo manually specify the unit
	 */
	public static function getSize( $attachment, $unit = '' ){
		$filepath = get_post_meta( $attachment, '_wp_attached_file', true );
		$fullpath = WP_CONTENT_DIR .'/uploads/'. $filepath;
		if( ! is_readable($fullpath) )
			return '';
		$size = filesize($fullpath);
		$sizes = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		for ($i=0; $size > 1024 && isset($sizes[$i+1]); $i++) $size /= 1024;
		$size = round($size)." ".$sizes[$i];
		return $size;
	}
}