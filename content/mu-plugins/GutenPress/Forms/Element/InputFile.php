<?php

namespace GutenPress\Forms\Element;

class InputFile extends Input{
	protected static $type = 'file';
	public function __toString(){
		$value = $this->getValue();
		// if input has a value, show a link to the file
		if ( $value && is_numeric($value) ) {
			// if value it's numeric, it's probably referencing a WP upload
			$attach = get_post($value);
			if ( ! empty($attach) ) {
				$out = '';
				$this->setAttribute( 'type', 'hidden' );
				$attachment_link = wp_get_attachment_url( $attach->ID );
				$out .= '<a href="'. $attachment_link .'">'. $attach->post_title .'</a>';
				$out .= parent::__toString();
			}
			return $out;
		} else {
			return parent::__toString();
		}
	}
}