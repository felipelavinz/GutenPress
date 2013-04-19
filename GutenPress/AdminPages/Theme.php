<?php

namespace GutenPress\AdminPages;

abstract class Theme extends AdminPage{
	public function __construct( $page_title, $menu_title, $capability = 'edit_theme_options', $slug = '', $callback ){
		parent::__construct( 'themes.php', $page_title, $menu_title, $capability, $slug, $callback );
	}
	protected function actionsManager(){
		add_action( $this->parent_slug .'-'. $this->slug .'_form_actions', array($this, 'formActions') );
		parent::actionsManager();
	}
	public function formActions(){
		echo '<p class="submit">';
			echo '<input type="hidden" name="action" value="'. $this->slug .'" />';
			wp_nonce_field($this->slug .'-update', '_'. $this->slug .'_nonce');
			echo '<input type="submit" value="'. __('Guardar', self::textdomain) .'" class="button-primary" />';
		echo '</p>';
	}
}