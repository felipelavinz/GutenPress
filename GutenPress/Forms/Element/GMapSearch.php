<?php

namespace GutenPress\Forms\Element;

class GMapSearch extends Input{
	protected static $type = 'text';
	protected $view_properties = array(
		'display_inline' => 0
	);
	public function __construct( $label = '', $name = '', array $properties = array() ){
		parent::__construct( $label, $name, $properties );
	}

	public function __toString(){

		if ( defined('GUTENPRESS_GMAPS_API_KEY') ) {
			throw new \Exception( __('Please define the GUTENPRESS_GMAPS_API_KEY constant with a proper Google Maps API Key', 'gutenpress') );
		}
		if ( GUTENPRESS_GMAPS_API_KEY === 'AIzaSyBAg3G1aMJzsWz7g4RdFidgSwflF4uY32A' ) {
			error_log( __('Please define GUTENPRESS_GMAPS_API_KEY with your own API Key to avoid query limits', 'gutenpress') );
		}

		// Build canvas & hidden inputs with the main input
		$name = $this->getAttribute('name');
		$value = $this->getAttribute('value');
		$out = '<div class="gp-gmapsearch">';
			// Input Address & Button Search
			$out .= '<input type="'.$this->getAttribute('type').'" name="'.$name.'[address]" class="regular-text mapAddress" value="'.$value['address'].'">';
			$out .= '<button type="button" class="mapSearch">'. _x('Search Address', 'gmap search', 'gutenpress') .'</button>';
			$out .= ' <span class="description mapError"></span>';
			// Canvas
			$out .= '<div id="'.$name.'_canvas" class="mapCanvas" style="width: 80%; height: 360px; margin-top: 5px;"></div>';
			// Hidden data: latitute - longitude
			$out .= '<input type="hidden" name="'.$name.'[lat]" class="mapLat" value="'.$value['lat'].'" >';
			$out .= '<input type="hidden" name="'.$name.'[lng]" class="mapLng" value="'.$value['lng'].'" >';
		$out .= '</div>';

		// Load Script
		\GutenPress\Assets\Assets::getInstance()->enqueueScript('googlemaps', 'http://maps.googleapis.com/maps/api/js?hl=es&key='. GUTENPRESS_GMAPS_API_KEY .'&sensor=true');
		\GutenPress\Assets\Assets::getInstance()->loadScript('jquery.ui.map');
		\GutenPress\Assets\Assets::getInstance()->loadScript('Forms-Element-GMapSearch');

		return $out;
	}
}