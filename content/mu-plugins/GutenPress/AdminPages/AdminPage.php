<?php

namespace GutenPress\AdminPages;

abstract class AdminPage{
	protected $slug;
	protected $icon;
	public $messages;
	protected $callback;
	protected $capability;
	protected $page_title;
	protected $menu_title;
	protected $parent_slug;

	public function __construct( $parent_slug, $page_title, $menu_title, $capability, $slug = '', $callback ){
		if ( !$slug ) $slug = sanitize_html_class( $page_title );
		$this->parent_slug = $parent_slug;
		$this->slug        = $slug;
		$this->callback    = $callback;
		$this->page_title  = $page_title;
		$this->menu_title  = $menu_title;
		$this->capability  = $capability ? $capability : 'manage_options';
		$this->icon        = pathinfo( $this->parent_slug, PATHINFO_FILENAME );
		$this->actions_manager();
		$this->default_messages();
	}

	protected function actionsManager(){
		add_action( 'admin_init', array($this, 'update') );
		add_action( 'admin_menu', array($this, 'addMenuPage') );
	}
	public function addMenuPage(){
		add_submenu_page($this->parent_slug, $this->page_title, $this->menu_title, $this->capability, $this->slug, array($this, 'settingsPage') );
	}
	private function defaultMessages(){
		$this->messages = array(
			0 => '',
			1 => '<strong>'. __('Las opciones se han guardado correctamente', self::textdomain) .'</strong>'
		);
	}
	public function settingsPage(){
		$data = get_option($this->slug);
		echo '<div class="wrap">';
			echo '<div id="icon-'. $this->icon .'" class="icon32"><br /></div>';
			echo '<h2>'. $this->page_title .'</h2>';
			if ( isset( $_GET['message'] ) && array_key_exists( $_GET['message'], $this->messages ) ) :
				echo '<div class="updated settings-error" id="setting-error-settings_updated"> ';
				echo '<p>'. $this->messages[$_GET['message']] .'</p>';
				echo '</div>';
			endif;
			echo '<form action="'. admin_url( $this->parent_slug .'?page='. $this->slug ) .'" method="post">';
				call_user_func( $this->callback, $data, $this );
				do_action( $this->parent_slug .'-'. $this->slug .'_form_actions' );
			echo '</form>';
		echo '</div>';
	}
	public function update(){
		if ( isset($_POST['action']) && $_POST['action'] === $this->slug ) {
			if ( wp_verify_nonce( $_POST['_'. $this->slug .'_nonce'], $this->slug .'-update' ) && current_user_can($this->capability) ) {
				foreach ( $_POST[$this->slug] as $k => $v ){
					if ( is_string($v) ) $_POST[$this->slug][$k] = stripslashes_deep($v);
				}
				update_option( $this->slug , $_POST[$this->slug] );
				/**
				 * Need to update something after we've saved theme settings?
				 * Hook into this action
				 * */
				do_action( 'after_save_'. $this->parent_slug .'_settings' );
				wp_safe_redirect( admin_url( $this->parent_slug .'?page='. $this->slug .'&message=1'), 303 );
			}
		}
	}
	public function getFieldId($id){
		return esc_attr( $this->slug .'_'. $id );
	}
	public function getFieldName($name){
		return $this->slug .'['. $name .']';
	}
}