<?php

namespace GutenPress\Forms\Element;

class WPGallery extends \GutenPress\Forms\FormElement{
	public function __construct( $label = '', $name = '', array $properties = array() ){
		parent::__construct( $label, $name, $properties );
	}
	public function __toString(){
		$out = '';
		if ( ! did_action('wp_enqueue_media') )
			return '<div class="error inline"><p>'. __('You need to call wp_enqueue_media() on the admin_enqueue_scripts hook', 'gutenpress') .'</p></div>';
		$class = $this->getAttribute('class');
		if ( empty($class) )
			$this->setAttribute('class', 'gp-wpgallery');
		else
			$this->setAttribute('class', $class .' gp-wpgallery');

		$this->setAttribute('data-name', $this->name);

		$out  .= '<div '. $this->renderAttributes() .'>';
		$value =  $this->getValue();
		if ( ! $value ) {
			// botón de subir/seleccionar imagen
			$out .= '<div class="thumb-receiver gp-wpgallery-receiver gp-wpgallery-sortable"></div>';
			$out .= '<div class="gp-wpgallery-controls">';
				$out .= '<button data-name="'. esc_attr( $this->name ) .'" data-uploader_title="'. esc_attr( $this->getLabel() ) .'" data-uploader_button_text="'. esc_attr( __('Select images', 'gutenpress') ) .'" class="button gp-wpgallery-create">'. __('Select Images for Gallery', 'gutenpress') .'</button>';
				$out .= ' <button data-name="'. esc_attr( $this->name ) .'" data-uploader_title="'. esc_attr( $this->getLabel() ) .'" data-uploader_button_text="'. esc_attr( __('Add images', 'gutenpress') ) .'" class="button gp-wpgallery-add hidden">'. __('Add Images to Gallery', 'gutenpress') .'</button>';
				$out .= ' <button class="btn-link gp-wpgallery-delete hidden">'. __('Remove images', 'gutenpress') .'</button>';
			$out .= '</div>';
		} else {
			// mostrar thumbnail; botones de eliminar / reemplazar imagen
			$out .= '<div class="thumb-receiver gp-wpgallery-receiver gp-wpgallery-sortable">'. $this->formatItems( $value ) .'</div>';
			$out .= '<div class="gp-wpgallery-controls">';
				$out .= '<button data-name="'. esc_attr( $this->name ) .'" data-uploader_title="'. esc_attr( $this->getLabel() ) .'" data-uploader_button_text="'. esc_attr( __('Select images', 'gutenpress') ) .'" class="button gp-wpgallery-create hidden">'. __('Select Images for Gallery', 'gutenpress') .'</button>';
				$out .= ' <button data-name="'. esc_attr( $this->name ) .'" data-uploader_title="'. esc_attr( $this->getLabel() ) .'" data-uploader_button_text="'. esc_attr( __('Add images', 'gutenpress') ) .'" class="button gp-wpgallery-add">'. __('Add Images to Gallery', 'gutenpress') .'</button>';
				$out .= ' <button class="btn-link gp-wpgallery-delete">'. __('Remove all images', 'gutenpress') .'</button>';
			$out .= '</div>';
		}
		$out .= '</div>';
		$assets = \GutenPress\Assets\Assets::getInstance();
		$assets->enqueueRegisteredScript( 'jquery-ui-sortable' );
		$assets->loadScript( 'Forms-Element-WPGallery' );
		return $out;
	}
	private function formatItems( $values ){
		$out = '';
		$img_size = $this->getProperty('img_size') ? $this->getProperty('img_size') : 'thumbnail';
		if ( is_array($values) ) {
			foreach ( $values as $val ) {
				$out .= $this->formatItem( $val, $img_size );
			}
		} else {
			return $this->formatItem( $values, $img_size );
		}
		return $out;
	}
	private function formatItem( $id, $img_size = 'thumbnail' ){
		$img = wp_get_attachment_image_src( $id, $img_size, false );
		$title = esc_attr( get_the_title( $id ) );
		$out  = '<div class="sortable gp-wpgallery-item gp-wpgallery-sortable-item attachment" style="width:'. $img[1] .'px;height:'. $img[2] .'px">';
			$out .= '<img src="'. esc_url($img[0]) .'" alt="'. $title .'" title="'. $title .'" width="'. $img[1] .'" heigt="'. $img[2] .'">';
			$out .= '<input type="hidden" name="'. $this->name .'[]" value="'. esc_attr($id) .'">';
			$out .= '<a class="close media-modal-icon" href="#" title="'. esc_attr_x('Remove', 'remove gallery item', 'gutenpress') .'"></a>';
		$out .= '</div>';
		return $out;
	}
}