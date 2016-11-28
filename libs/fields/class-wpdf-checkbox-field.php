<?php
/**
 * Class WPDF_CheckboxField
 *
 * Add checkbox field.
 */
class WPDF_CheckboxField extends WPDF_FormField {


	/**
	 * Output field on frontend
	 *
	 * @param WPDF_FormData $form_data Passed form data.
	 */
	public function output( $form_data ) {

		$value = $form_data->get_raw( $this->_name );

		if ( isset( $this->_args['options'] ) && ! empty( $this->_args['options'] ) ) {

			$name = $this->get_input_name();
			if ( $this->is_type( 'checkbox' ) ) {
				$name .= '[]';
			}

			echo '<div class="wpdf-choices">';

			foreach ( $this->_args['options'] as $key => $option ) {

				if ( is_array( $value ) ) {
					$checked = in_array( '' . $key, $value, true ) ? 'checked="checked"' : '';
				} else {
					$checked = '' . $key === $value ? 'checked="checked"' : '';
				}

				echo '<label>';
				echo '<input type="' . $this->get_type() . '" name="' . $name . '"' . $checked . ' value="' . $key . '" class="wpdf-field">' . $option;
				echo '</label>';
			}

			echo '</div>';
		}
	}

	/**
	 * Format field data to store in fields array
	 *
	 * @param array $field Field data to be passed.
	 *
	 * @return array
	 */
	public function save( $field = array() ) {

		$data = parent::save( $field );

		$options  = array();
		$defaults = array();
		foreach ( $field['value_labels'] as $arr_id => $label ) {
			$option_key             = isset( $field['value_keys'][ $arr_id ] ) && ! empty( $field['value_keys'][ $arr_id ] ) ? esc_attr( $field['value_keys'][ $arr_id ] ) : esc_attr( $label );
			$options[ $option_key ] = $label;

			if ( isset( $field['value_default'][ $arr_id ] ) ) {
				$defaults[] = $option_key;
			}
		}

		$data['options'] = $options;
		$data['default'] = $defaults;

		return $data;
	}
}