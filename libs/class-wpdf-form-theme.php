<?php
/**
 * Form Theme
 *
 * @package WPDF
 * @author James Collings
 * @created 22/11/2016
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class WPDF_FormTheme
 */
class WPDF_FormTheme {

	/**
	 * Form Defaults Styles
	 *
	 * @var array
	 */
	protected $_defaults = array(
		'form_bg_colour'            => '',
		'form_text_colour'          => '',
		'form_bg_error_colour'      => '#c63838',
		'form_text_error_colour'    => '#ffffff',
		'form_bg_success_colour'    => '#80cd5f',
		'form_text_success_colour'  => '#ffffff',
		'field_label_bg_colour'     => '',
		'field_label_text_colour'   => '',
		'field_input_bg_colour'     => '',
		'field_input_text_colour'   => '',
		'field_error_text_colour'   => '#c63838',
		'field_border_colour'       => '',
		'field_error_border_colour' => '#c63838',
		'button_bg_colour'          => '#000000',
		'button_text_colour'        => '#ffffff',
		'button_hover_bg_colour'    => '#333333',
		'button_hover_text_colour'  => '#ffffff',
		'checkbox_text_colour'      => '',
	);

	/**
	 * List of disabled styles
	 *
	 * @var array
	 */
	protected $_disabled = array();

	/**
	 * Theme Styles
	 *
	 * @var array
	 */
	protected $_styles = array();

	/**
	 * WPDF_FormTheme constructor.
	 *
	 * @param array $data Theme settings.
	 * @param array $disabled List of disabled styles.
	 */
	public function __construct( $data = array(), $disabled = array() ) {

		$this->_defaults = apply_filters( 'wpdf/theme_defaults', $this->_defaults );

		foreach ( $this->_defaults as $key => $val ) {
			$this->_styles[ $key ]   = isset( $data[ $key ] ) ? $data[ $key ] : $this->_defaults[ $key ];
			$this->_disabled[ $key ] = in_array( $key, $disabled, true ) ? true : false;
		}
	}

	/**
	 * Get style
	 *
	 * @param string $key Style key.
	 * @param bool   $force Get style even if its disabled.
	 *
	 * @return bool|mixed
	 */
	public function get_style( $key, $force = false ) {
		return isset( $this->_styles[ $key ] ) && ( ! $this->is_style_disabled( $key ) || $force ) ? $this->_styles[ $key ] : false;
	}

	/**
	 * Has Style
	 *
	 * @param string $key Style key.
	 *
	 * @return bool|mixed
	 */
	public function has_style( $key ) {
		return $this->get_style( $key );
	}

	/**
	 * Check to see if style is disabled
	 *
	 * @param string $key Style key.
	 *
	 * @return bool
	 */
	public function is_style_disabled( $key ) {
		return isset( $this->_disabled[ $key ] ) && true === $this->_disabled[ $key ]  ? true : false;
	}
}
