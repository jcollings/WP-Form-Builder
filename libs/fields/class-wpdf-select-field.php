<?php

/**
 * Class WPDF_SelectField
 *
 * Add select field
 */
class WPDF_SelectField extends WPDF_FormField {

	/**
	 * Get select empty field text
	 *
	 * @var string
	 */
	protected $_empty;

	/**
	 * Type of select
	 *
	 * @var string $_select_type Select type multiple or single.
	 */
	protected $_select_type;

	/**
	 * WPDF_SelectField constructor.
	 *
	 * @param string $name Field name.
	 * @param string $type Field type.
	 * @param array  $args Field arguments.
	 */
	public function __construct( $name, $type, $args = array() ) {
		parent::__construct( $name, $type, $args );

		$this->_empty       = isset( $args['empty'] ) ? $args['empty'] : 'Please select an option';
		$this->_select_type = isset( $args['select_type'] ) ? $args['select_type'] : 'single';
	}

	/**
	 * Display field output on public form
	 *
	 * @param WPDF_FormData $form_data Form data to be output.
	 */
	public function output( $form_data ) {

		$value = $form_data->getRaw( $this->_name );
		$attrs = '';
		$name  = $this->get_input_name();
		if ( $this->get_select_type() === 'multiple' ) {
			$attrs .= ' multiple="multiple"';
			$name .= '[]';
		}

		echo '<select name="' . $name . '" id="' . $this->get_id() . '" class="' . $this->get_classes() . '"' . $attrs . '>';

		// only show empty value if not multiple select.
		if ( $this->get_select_type() !== 'multiple' ) {
			if ( isset( $this->_args['empty'] ) ) {
				if ( false !== $this->_args['empty'] ) {
					echo '<option value="">' . $this->_args['empty'] . '</option>';
				}
			} else {
				echo sprintf( '<option value="">%s</option>', __( "Select an option", "wpdf" ) );
			}
		}

		if ( isset( $this->_args['options'] ) && ! empty( $this->_args['options'] ) ) {
			foreach ( $this->_args['options'] as $key => $option ) {

				$selected = '';
				if ( is_array( $value ) && in_array( $key, $value, true ) ) {
					$selected = 'selected="selected"';
				} elseif ( ! is_array( $value ) && ! empty( $value ) && $key === $value ) {
					$selected = 'selected="selected"';
				}
				echo '<option value="' . $key . '"' . $selected . '>' . $option . '</option>';
			}
		}

		echo '</select>';
	}

	/**
	 * Get empty text
	 *
	 * @return mixed|string
	 */
	public function get_empty() {
		return $this->_empty;
	}

	/**
	 * Get select type
	 *
	 * @return mixed|string
	 */
	public function get_select_type() {
		return $this->_select_type;
	}

	/**
	 * Format field data to store in fields array
	 *
	 * @param array $field Field data.
	 *
	 * @return array
	 */
	public function save( $field = array() ) {

		$data = parent::save( $field );

		if ( isset( $field['empty_text'] ) && ! empty( $field['empty_text'] ) ) {
			$data['empty'] = esc_attr( $field['empty_text'] );
		} else {
			$data['empty'] = false;
		}

		if ( isset( $field['select_type'] ) && ! empty( $field['select_type'] ) ) {
			$data['select_type'] = $field['select_type'];
		} else {
			$data['select_type'] = 'single';
		}

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