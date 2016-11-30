<?php
/**
 * Textarea Field
 *
 * @package WPDF/Fields
 * @author James Collings
 * @created 03/11/2016
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class WPDF_TextareaField
 *
 * Add textarea field
 */
class WPDF_TextareaField extends WPDF_FormField {

	/**
	 * Textarea rows attribute
	 *
	 * @var int
	 */
	protected $_rows = 8;

	/**
	 * WPDF_TextareaField constructor.
	 *
	 * @param string $name Field name.
	 * @param string $type Field type.
	 * @param array  $args Field arguments.
	 */
	public function __construct( $name, $type, $args = array() ) {
		parent::__construct( $name, $type, $args );

		$this->_rows = isset( $args['rows'] ) ? $args['rows'] : $this->_rows;
	}

	/**
	 * Get textarea rows attribute
	 *
	 * @return int
	 */
	public function get_rows() {
		return $this->_rows;
	}

	/**
	 * Display field output on public form
	 *
	 * @param WPDF_FormData $form_data Form data to be output.
	 */
	public function output( $form_data ) {

		$value = $form_data->get( $this->_name );
		echo '<textarea name="' . esc_attr( $this->get_input_name() ) . '" id="' . esc_attr( $this->get_id() ) . '" class="' . esc_attr( $this->get_classes() ) . '" rows="' . esc_attr( $this->get_rows() ) . '">' . esc_textarea( $value ) . '</textarea>';
	}

	/**
	 * Format field data to store in fields array
	 *
	 * @param array $field Field data.
	 *
	 * @return array
	 */
	public function save( $field = array() ) {

		$data         = parent::save( $field );
		$data['rows'] = isset( $field['rows'] ) ? $field['rows'] : 8;

		return $data;
	}
}