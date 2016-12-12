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

		$value = $form_data->get( $this->_name );

		// Allowed types input, input-range, slider, slider-range.
		switch ( $this->get_display_type() ) {
			case 'input':
			case 'slider':

				$input_type = 'slider' === $this->get_display_type() ? 'hidden' : 'number';

				echo '<div class="wpdf-number--single ' . esc_attr( $this->get_display_type() ) . '">';
				echo '<input type="' . esc_attr( $input_type ) . '" name="' . esc_attr( $this->get_input_name() ) . '" value="' . esc_attr( $value ) . '" id="' . esc_attr( $this->get_id() ) . '" class="' . esc_attr( $this->get_classes() ) . '" />';

				if ( 'slider' === $this->get_display_type() ) {
					$this->display_slider_element();
				}

				echo '</div>';

			break;
			case 'input-range':
			case 'slider-range':

				$input_type = 'slider-range' === $this->get_display_type() ? 'hidden' : 'number';

				echo '<div class="wpdf-number--range ' . esc_attr( $this->get_display_type() ) . '">';

				$suffix = '[min]';
				$min_value = is_array( $value ) && isset( $value['min'] ) ? $value['min'] : '';
				echo '<input type="' . esc_attr( $input_type ) . '" name="' . esc_attr( $this->get_input_name() . $suffix ) . '" value="' . esc_attr( $min_value ) . '" id="' . esc_attr( $this->get_id() ) . '" class="' . esc_attr( $this->get_classes() ) . '" />';

				if ( 'input-range' === $this->get_display_type() ) {
					echo '<span class="wpdf-range-text">to</span>';
				}

				$suffix = '[max]';
				$max_value = is_array( $value ) && isset( $value['max'] ) ? $value['max'] : '';
				echo '<input type="' . esc_attr( $input_type ) . '" name="' . esc_attr( $this->get_input_name() . $suffix ) . '" value="' . esc_attr( $max_value ) . '" id="' . esc_attr( $this->get_id() ) . '" class="' . esc_attr( $this->get_classes() ) . '" />';

				if ( 'slider-range' === $this->get_display_type() ) {
					$this->display_slider_element();
				}

				echo '</div>';

			break;
		}
	}

	/**
	 * Display div to generate slider
	 */
	protected function display_slider_element() {

		$range  = 'no';
		if ( 'slider-range' === $this->get_display_type() ) {
			$range = 'yes';
		}

		echo '<div class="wpdf-range-slider" data-range="' . esc_attr( $range ) . '" data-min="' . esc_attr( $this->get_min_value() ) . '" data-max="' . esc_attr( $this->get_max_value() ) . '" data-step="' . esc_attr( $this->get_step_value() ) . '"></div>';
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