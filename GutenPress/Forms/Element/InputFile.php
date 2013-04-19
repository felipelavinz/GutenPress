<?php

namespace GutenPress\Forms\Element;

class InputFile extends Input{
	protected static $type = 'file';
	public function __toString(){
		static $is_admin;
		$is_admin = is_admin();
		$value = $this->getValue();
		// if input has a value, show a link to the file
		if ( $value && is_numeric($value) ) {
			// if value it's numeric, it's probably referencing a WP upload
			try {
				$attach = new \GutenPress\Helpers\Attachment( $value );
				$out = '';
				$out .= '<p id="'. $this->getAttribute('id') .'-wrap">';
					$out .= '<a href="'. $attach->url .'">'. $attach->title .'</a> ('. $attach->filesize .')';
					if ( $is_admin ) {
						$out .= ' - <a href="'. admin_url('post.php?post='. $value .'&amp;action=edit') .'">'. __('edit attachment', 'gutenpress') .'</a>';
						/* translators: remove attachment link */
						$out .= '<span class="hide-if-no-js"> - <a href="#remove-attachment" data-field-id="'. $this->getAttribute('id') .'" class="deletion remove-attachment">'. _x('remove', 'remove attachment', 'gutenpress') .'</a></span>';
					}
					// add the previous value as hidden field, to preserve between editions
					$this->setAttribute( 'type', 'hidden' );
					$out .= parent::__toString();
				$out .= '</p>';

				// add a hidden upload, in case you would like to replace the file
				$this->setAttribute( 'type', 'file' );
				$this->setAttribute( 'disabled', 'disabled' );
				$this->setAttribute( 'class', 'hidden' );
				$this->setAttribute( 'id', $this->getAttribute('id') .'-upload' );
				$out .= parent::__toString();
				$out .= $this->addScript();

				return $out;
			} catch ( \Exception $attach ) {
				return parent::__toString();
			}
			return $out;
		} else {
			return parent::__toString();
		}
	}
	private function addScript(){
		static $done;
		if ( $done === true )
			return;
$out = <<<EOL
<script type="text/javascript">
jQuery(document).ready(function(\$){
	\$('.remove-attachment').on('click', function(){
		var target = \$(this).data('field-id');
		\$('#' + target +'-wrap').fadeOut('fast', function(){
			\$(this).remove();
			\$('#'+ target +'-upload').fadeIn('fast', function(){
				\$(this).removeAttr('disabled');
			});
		});
	});
})
</script>
EOL;
		return $out;
		$done = true;
	}
}