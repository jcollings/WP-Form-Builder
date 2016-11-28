<?php

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
		echo '<input type="' . $this->get_type() . '" name="' . $this->get_input_name() . '" value="' . $value . '" id="' . $this->get_id() . '" class="' . $this->get_classes() . '" />';
	}
}