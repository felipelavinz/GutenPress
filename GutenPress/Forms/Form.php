<?php

namespace GutenPress\Forms;

class Form extends Element{
	protected $id;
	protected $view;
	protected $elements = array();

	protected static $element_attributes = array(
		'accept-charset',
		'action',
		'autocomplete',
		'enctype',
		'method',
		'name',
		'novalidate',
		'target'
	);

	/**
	 * Build an HTML form
	 * @param string $id Form "id" attribute
	 * @param string $view The name of a "View" class. Must extend \GutenPress\Forms\View
	 * @param array $properties A list of properties, most likely form attributes
	 * @param array $elements An array of elements (optional)
	 */
 	public function __construct( $id = 'gp_form', $view = '', array $properties = array(), array $elements = array() ){
		// set the form "id" attribute
		$properties['id'] = $id;
		$this->id = $id;

		// set the default view
		if ( empty($view) ) {
			$view = apply_filters('guttenpress_form_view', '\GutenPress\Forms\View\WPWide');
		}

		// the selected view must extend the base view class
		if ( is_subclass_of($view, '\GutenPress\Forms\View') ) {
			$this->view = $view;
		} else {
			throw new \GutenPress\Helpers\Exception( __('The $view parameter must be a subclass of \GutenPress\Forms\View') );
		}

		// add a set of elements
		if ( ! empty($elements) ) {
			foreach ( $elements as $element ) {
				$this->addElement( $element );
			}
		}

		// define some default attributes
		$properties = wp_parse_args( $properties, array(
			'action' => \GutenPress\Helpers\Urls::getCurrent( true ),
			'method' => 'post'
		) );

		parent::__construct( $properties );

	}

	public function getName( $name ){
		return $this->id .'['. $name .']';
	}

	public function getId( $id ){
		return $this->id .'-'. $id;
	}

	public function addElement( Element $element ){
		$this->elements[] = $element;
		return $this;
	}

	public function __toString(){
		$out  = '';
		$view = new $this->view( $this, $this->elements );
		$out .= '<form'. $this->renderAttributes() .'>';
			$out .= $view;
		$out .= '</form>';
		return $out;
	}

}