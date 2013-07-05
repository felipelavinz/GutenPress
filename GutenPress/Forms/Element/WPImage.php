<?php

namespace GutenPress\Forms\Element;

class WPImage extends \GutenPress\Forms\FormElement{
	public function __construct( $label = '', $name = '', array $properties = array() ){
		parent::__construct( $label, $name, $properties );
	}
	public function __toString(){
		$out = '';
		if ( ! did_action('wp_enqueue_media') )
			return '<div class="error inline"><p>'. __('You need to call wp_enqueue_media() on the admin_enqueue_scripts hook', 'gutenpress') .'</p></div>';
		$class = $this->getAttribute('class');
		if ( empty($class) )
			$this->setAttribute('class', 'gp-wpimage');
		else
			$this->setAttribute('class', $class .' gp-wpimage');

		$out  .= '<div '. $this->renderAttributes() .'>';
		$value =  $this->getValue();
		if ( ! $value ) {
			// botón de subir/seleccionar imagen
			$out .= '<div class="thumb-receiver gp-wpimage-receiver"></div>';
			$out .= '<button data-uploader_title="'. esc_attr( $this->getLabel() ) .'" data-uploader_button_text="'. esc_attr( __('Select image', 'gutenpress') ) .'" class="button gp-wpimage-upload">'. __('Upload or select an existing image', 'gutenpress') .'</button>';
			$out .= ' <button class="btn-link gp-wpimage-delete hidden">'. __('Remove image', 'gutenpress') .'</button>';
			$out .= '<input class="gp-wpimage-field" type="hidden" name="'. $this->name .'">';
		} else {
			// mostrar thumbnail; botones de eliminar / reemplazar imagen
			$out .= '<div class="thumb-receiver gp-wpimage-receiver">'. wp_get_attachment_image($value, 'thumbnail') .'</div>';
			$out .= '<button data-uploader_title="'. esc_attr( $this->getLabel() ) .'" data-uploader_button_text="'. esc_attr( __('Select image', 'gutenpress') ) .'" class="button gp-wpimage-upload">'. __('Replace image', 'gutenpress') .'</button>';
			$out .= ' <button class="btn-link gp-wpimage-delete">'. __('Remove image', 'gutenpress') .'</button>';
			$out .= '<input class="gp-wpimage-field" type="hidden" name="'. $this->name .'">';
		}
		$out .= '</div>';
		\GutenPress\Assets\Assets::getInstance()->loadScript('Forms-Element-WPImage');
		return $out;
	}
}