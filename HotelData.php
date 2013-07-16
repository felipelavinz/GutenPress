<?php
/*
Plugin Name: Configuración de hoteles
Plugin URI: http://www.clerkhotel.com
Description: Permite ajustar varias opciones del hotel
Version: 0.1
Author: Felipe Lavín - Basilio Cáceres - AyerViernes S.A.
Author URI: http://www.ayerviernes.com
License: © AyerViernes
*/

use GutenPress\Forms\Element as Element;

final class HotelData{
	private static $instance;

	/**
	 * Integrar acciones en WordPress
	 */
	private function __construct(){

		add_action('admin_menu', array($this , 'addHotelData'));
		add_action('admin_init', array($this, 'saveHotelData'));
		add_action('admin_enqueue_scripts', array($this, 'enqueueMediaScripts'));
	}

	public function enqueueMediaScripts(){
		if ( get_current_screen()->id === 'toplevel_page_hotel-data' ) {
			wp_enqueue_media();
		}
	}

	public static function getInstance(){
		if ( !isset(self::$instance) ){
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}
	public function __clone(){
		trigger_error('Clone is not allowed.', E_USER_ERROR);
	}

	/**
	 * Agrega la pantalla de configuración del sitio a un sub-sitio
	 * Se muestra como submenu del Tablero
	 */
	public function addHotelData(){
		add_menu_page('Información general del Hotel', 'Info del Hotel', 10, 'hotel-data', array($this, 'HotelData'));
	}

	/**
	 * Información del hotel
	 * @return [type] [description]
	 */
	public static function getHotelData(){
		return get_option('hotel_data');
	}

	/**
	 * Información del hotel
	 * @return [type] [description]
	 */
	public static function getHotelDataItem( $item ){
		$data = get_option('hotel_data');
		return ( isset( $data[ $item ] ) ? $data[ $item ] : false );
	}

	/**
	 * Pantalla de administración de configuraciones del sitio
	 * @return void
	 */
	public function HotelData(){
		if ( isset($_GET['id']) ) {
			$bloginfo = get_blog_details( $_GET['id'] );
		} else {
			$bloginfo = get_blog_details();
		}

		// Get Hotel Data store on options
		$data = $this->getHotelData();

		echo '<div class="wrap">';
			screen_icon('ms-admin');
			echo '<h2>Información del Hotel:</h2>';

			if ( isset($_GET['update']) && $_GET['update']  === 'updated' ) :
				echo '<div class="updated">';
					echo '<p>La configuración del Hotel se ha actualizado correctamente</p>';
				echo '</div>';
			endif;

			$form = new \GutenPress\Forms\Form('hotel-data');
			$form->setProperties( array(
				'enctype' => 'multipart/form-data'
			) );
			$form->addElement( new Element\InputText(
				'Nombre de tu hotel',
				'hotel_name',
				array(
					'value' => $bloginfo->blogname,
					'description' => 'Indica el nombre del hotel, que aparecerá en la cabecera del sitio web.'
				)
			) )->addElement( new Element\InputText(
				'Tagline',
				'hotel_tagline',
				array(
					'value' => get_option('blogdescription'),
					'description' => 'Frase o tagline que usualmente acompaña tu logo. No es necesario completarla.'
				)
			) )->addElement( new Element\GMapSeach(
				'Dirección',
				'hotel_address',
				array(
					'type' => 'text',
					'value' => (array)$data['hotel_address'],
					'description' => 'Dirección completa del hotel: Calle - Nº, Comuna, País'
				)
			) )->addElement( new Element\InputText(
				'Teléfono',
				'hotel_phone',
				array(
					'value' => $data['hotel_phone'],
					'maxlength' => '12',
					'description' => 'Número de teléfono oficial del hotel'
				)
			) )->addElement( new Element\InputText(
				'Fax',
				'hotel_fax',
				array(
					'value' => $data['hotel_fax'],
					'placeholder' => '56-02-2155555',
					'maxlength' => '12',
					'description' => 'Si posees un número de fax, agrégalo'
				)
			) )->addElement( new Element\InputEmail(
				'Email',
				'hotel_email',
				array(
					'value' => $data['hotel_email'],
					'description' => 'Email público del hotel. Por ejemplo: contacto@tuhotel.com'
				)
			) )->addElement( new Element\WPImage(
				'Logo',
				'hotel_logo',
				array(
					'value' => $data['hotel_logo'],
					'description' => 'Añade tu logo'
				)
			) )->addElement( new Element\InputText(
				'Facebook',
				'hotel_facebook',
				array(
					'value' => $data['hotel_facebook'],
					'class' => 'widefat'
				)
			) )->addElement( new Element\InputText(
				'Twitter',
				'hotel_twitter',
				array(
					'value' => $data['hotel_twitter'],
					'class' => 'widefat'
				)
			) )->addElement( new Element\InputText(
				'TripAdvisor',
				'hotel_tripadvisor',
				array(
					'value' => $data['hotel_tripadvisor'],
					'class' => 'widefat'
				)
			) );

			$form->addElement( new Element\InputSubmit(
				'Guardar cambios',
				'submit',
				array(
					'class' => 'primary-action'
				)
			) )->addElement( new Element\InputHidden(
				'action',
				'set-hotel-data'
			) )->addElement( new Element\InputHidden(
				'blogid',
				$bloginfo->blog_id
			) )->addElement( new Element\WPNonce(
				'set-hotel-data-'. $bloginfo->blog_id,
				'_save-hotel-data-nonce'
			) );
			echo $form;
		echo '</div>';
	}

	public function saveHotelName($hotel_name){
		update_option('blogname', $hotel_name);
	}

	public function saveHotelTagline($hotel_tagline){
		update_option('blogdescription', $hotel_tagline);
	}

	/**
	 * Guardar la información del hotel
	 * Valida la información del nonce y chequea los permisos correspondientes
	 * @return void
	 */
	public function saveHotelData(){
		if ( !isset($_POST['action']) )
			return;
		if ( $_POST['action'] !== 'set-hotel-data' )
			return;

		$blogid = absint( $_POST['blogid'] );

		if ( ! wp_verify_nonce( $_POST['_save-hotel-data-nonce'], 'set-hotel-data-'. $blogid) || ! current_user_can('admin') )
			wp_die('No tienes autorización para modificar estas opciones');

		// Hotel Name
		$this->saveHotelName( strip_tags( $_POST['hotel_name'] ) );
		// Tagline
		$this->saveHotelTagline( strip_tags( $_POST['hotel_tagline'] ) );

		// Store Data
		$data = array(
			'hotel_name' => strip_tags( $_POST['hotel_name'] ),
			'hotel_tagline' => strip_tags( $_POST['hotel_tagline'] ),
			'hotel_address' => $_POST['hotel_address'],
			'hotel_phone' => $_POST['hotel_phone'],
			'hotel_fax' => $_POST['hotel_fax'],
			'hotel_email' => filter_var( $_POST['hotel_email'], FILTER_VALIDATE_EMAIL ),
			'hotel_logo' => $_POST['hotel_logo'],
			'hotel_facebook' => filter_var( $_POST['hotel_facebook'], FILTER_VALIDATE_URL ),
			'hotel_twitter' => filter_var( $_POST['hotel_twitter'], FILTER_VALIDATE_URL ),
			'hotel_tripadvisor' => filter_var( $_POST['hotel_tripadvisor'], FILTER_VALIDATE_URL ),
		);
		$this->updateHotelData($data);

		// WP Redirect
		wp_redirect( admin_url('admin.php?page=hotel-data&update=updated'), 303 );
		exit;
	}

	public function updateHotelData( $data ){
		// Guardar Data
		update_option('hotel_data', $data );
	}
}
// Instantiate the class object
HotelData::getInstance();
