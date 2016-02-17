<?php

namespace GutenPress\Forms\Element;

class WPFile extends \GutenPress\Forms\FormElement{
	public function __construct( $label = '', $name = '', array $properties = array() ){
		parent::__construct( $label, $name, $properties );
	}
	public function __toString(){
		$out = '';
		if ( ! did_action('wp_enqueue_media') )
			return '<div class="error inline"><p>'. __('You need to call wp_enqueue_media() on the admin_enqueue_scripts hook', 'gutenpress') .'</p></div>';
		$class = $this->getAttribute('class');
		if ( empty($class) )
			$this->setAttribute('class', 'gp-wpfile');
		else
			$this->setAttribute('class', $class .' gp-wpfile');

		$out  .= '<div '. $this->renderAttributes() .'>';
		$value =  $this->getValue();
		$uniqid = uniqid('button_');
		$receiver_id = uniqid('receiver_');
		$target_id = uniqid('target_');
		if ( ! $value ) {
			// botón de subir/seleccionar imagen
			$out .= '<div class="name-receiver gp-wpfile-receiver" id="'.$receiver_id.'"></div>';
			$out .= '<button data-uploader_title="'. esc_attr( $this->getLabel() ) .'" data-uploader_button_text="'. esc_attr( __('Select file', 'gutenpress') ) .'" id="'.$uniqid.'" data-target_id="'.$target_id.'" data-receiver_id="'.$receiver_id.'" onClick="bindEventWidgetFile(this.id);return false;" class="button gp-wpfile-upload">'. __('Upload or select an existing file', 'gutenpress') .'</button>';
			$out .= ' <button class="btn-link gp-wpfile-delete hidden">'. __('Remove file', 'gutenpress') .'</button>';
			$out .= '<input class="gp-wpfile-field" type="hidden" id="'.$target_id.'" name="'. $this->name .'">';
		} else {
			// mostrar thumbnail; botones de eliminar / reemplazar imagen
			$attachment_file = get_post($value);
			$out .= '<div class="name-receiver gp-wpfile-receiver" id="'.$receiver_id.'">'. $attachment_file->post_title .'</div>';
			$out .= '<button data-uploader_title="'. esc_attr( $this->getLabel() ) .'" data-uploader_button_text="'. esc_attr( __('Select file', 'gutenpress') ) .'" id="'.$uniqid.'" data-target_id="'.$target_id.'" data-receiver_id="'.$receiver_id.'" onClick="bindEventWidgetFile(this.id);return false;" class="button gp-wpfile-upload">'. __('Replace file', 'gutenpress') .'</button>';
			$out .= ' <button class="btn-link gp-wpfile-delete">'. __('Remove file', 'gutenpress') .'</button>';
			$out .= '<input class="gp-wpfile-field" type="hidden" id="'.$target_id.'" name="'. $this->name .'" value="'. esc_attr($value) .'">';
		}
		$out .= '</div>';
		\GutenPress\Assets\Assets::getInstance()->loadScript('Forms-Element-WPFile');
		return $out;
	}
}