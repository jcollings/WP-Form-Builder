<?php
/**
 * Number Field
 *
 * @package WPDF/Fields
 * @author James Collings
 * @created 03/11/2016
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class WPDF_NumberField
 *
 * Add text field
 */
class WPDF_NumberField extends WPDF_FormField {

	/**
	 * Minimum value
	 *
	 * @var int
	 */
	protected $_min = null;

	/**
	 * Maximum value
	 *
	 * @var int
	 */
	protected $_max = null;

	/**
	 * Step increment
	 *
	 * @var int
	 */
	protected $_step = null;

	/**
	 * Display Type
	 *
	 * Allowed values of input, input-range, slider, slider-range
	 *
	 * @var string
	 */
	protected $_display_type = null;

	/**
	 * WPDF_NumberField constructor.
	 *
	 * @param string $name Form name.
	 * @param string $type Form type.
	 * @param array  $args Form arguments.
	 */
	public function __construct( $name, $type = '', $args = array() ) {
		parent::__construct( $name, $type, $args );

		$this->_min = isset( $args['min'] ) ? $args['min'] : null;
		$this->_max = isset( $args['max'] ) ? $args['max'] : null;
		$this->_step = isset( $args['step'] ) ? $args['step'] : null;
		$this->_display_type = isset( $args['display_type'] ) ? $args['display_type'] : 'input';
	}

	/**
	 * Get minimum allowed value
	 */
	public function get_min_value() {
		return $this->_min;
	}

	/**
	 * Get maximum allowed value
	 */
	public function get_max_value() {
		return $this->_max;
	}

	/**
	 * Get incremental step value
	 */
	public function get_step_value() {
		return $this->_step;
	}

	/**
	 * Get display type
	 */
	public function get_display_type() {
		return $this->_display_type;
	}

	/**
	 * Display field output on public form
	 *
	 * @param WPDF_FormData $form_data Form data to be output.
	 */
	public function output( $form_data ) {

		// Allowed types input, input-range, slider, slider-range.
		switch ( $this->get_display_type() ) {
			case 'input':
			case 'slider':

				$value = $form_data->get( $this->_name );
				echo '<input type="number" name="' . esc_attr( $this->get_input_name() ) . '" value="' . esc_attr( $value ) . '" id="' . esc_attr( $this->get_id() ) . '" class="' . esc_attr( $this->get_classes() ) . '" />';
				break;
			case 'input-range':
			case 'slider-range':

				$suffix = '_min';
				$value = $form_data->get( $this->_name . $suffix );
				echo '<input type="number" name="' . esc_attr( $this->get_input_name() . $suffix ) . '" value="' . esc_attr( $value ) . '" id="' . esc_attr( $this->get_id() ) . '" class="' . esc_attr( $this->get_classes() ) . '" />';

				$suffix = '_max';
				$value = $form_data->get( $this->_name . $suffix );
				echo '<input type="number" name="' . esc_attr( $this->get_input_name() . $suffix ) . '" value="' . esc_attr( $value ) . '" id="' . esc_attr( $this->get_id() ) . '" class="' . esc_attr( $this->get_classes() ) . '" />';
				break;
		}
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

		// Save min value.
		if ( isset( $field['min'] ) && is_numeric( $field['min'] ) ) {
			$data['min'] = $field['min'];
		}

		// Save max value.
		if ( isset( $field['max'] ) && is_numeric( $field['max'] ) ) {
			$data['max'] = $field['max'];
		}

		// Save step value.
		if ( isset( $field['step'] ) && is_numeric( $field['step'] ) ) {
			$data['step'] = $field['step'];
		}

		// Save display type.
		if ( isset( $field['display_type'] ) ) {
			$data['display_type'] = $field['display_type'];
		}

		return $data;
	}
}