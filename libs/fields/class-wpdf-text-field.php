<?php
/**
 * Text Field
 *
 * @package WPDF/Fields
 * @author James Collings
 * @created 03/11/2016
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class WPDF_TextField
 *
 * Add text field
 */
class WPDF_TextField extends WPDF_FormField {

	/**
	 * Display field output on public form
	 *
	 * @param WPDF_FormData $form_data Form data to be output.
	 */
	public function output( $form_data ) {

		$value = $form_data->get( $this->_name );
		echo '<input type="' . esc_attr( $this->get_type() ) . '" name="' . esc_attr( $this->get_input_name() ) . '" value="' . esc_attr( $value ) . '" id="' . esc_attr( $this->get_id() ) . '" class="' . esc_attr( $this->get_classes() ) . '" />';
	}
}