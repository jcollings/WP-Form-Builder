<?php
/**
 * Form Data
 *
 * @package WPDF
 * @author James Collings
 * @created 05/08/2016
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class WPDF_FormData
 */
class WPDF_FormData {

	/**
	 * Form data
	 *
	 * @var array
	 */
	protected $_data = null;

	/**
	 * Raw form data
	 *
	 * @var array
	 */
	protected $_raw_data = null;

	/**
	 * Form fields
	 *
	 * @var WPDF_FormField[]
	 */
	protected $_fields = null;

	/**
	 * Form data defaults
	 *
	 * @var array
	 */
	protected $_defaults = array();

	/**
	 * If the form has been submitted
	 *
	 * @var bool
	 */
	private $_submitted = true;

	/**
	 * Upload directory
	 *
	 * @var string
	 */
	private $_upload_dir = null;

	/**
	 * Current Form
	 *
	 * @var WPDF_Form
	 */
	private $_form = null;

	/**
	 * WPDF_FormData constructor.
	 *
	 * @param WPDF_Form $form  Form object.
	 */
	public function __construct( $form ) {

		$this->_data     = array();
		$this->_raw_data = array();
		$this->_form = $form;
		$this->_fields   = $form->get_fields();
		$this->_submitted = $form->is_submitted() === true ? true : false;
		$this->_upload_dir = $form->get_upload_folder();
	}

	/**
	 * Load data from form submission
	 *
	 * @param array     $data Post data.
	 * @param array     $upload_data File upload data.
	 */
	public function form_data( $data, $upload_data ) {

		foreach ( $this->_fields as $field_id => $field ) {

			$this->_raw_data[ $field_id ] = isset( $data[ $field->get_input_name() ] ) ? $field->sanitize( $data[ $field->get_input_name() ] ) : false;
			$this->_defaults[ $field_id ] = $field->get_default_value();

			if ( $field->is_type( 'file' ) ) {

				if ( isset( $upload_data[ $field->get_input_name() ] ) ) {

					if ( isset( $upload_data[ $field->get_input_name() ]['name'] ) && isset( $upload_data[ $field->get_input_name() ]['error'] ) ) {

						if ( 0 === $upload_data[ $field->get_input_name() ]['error']
						     && $field->is_valid_ext( $upload_data[ $field->get_input_name() ] )
						     && $field->is_allowed_size( $upload_data[ $field->get_input_name() ] )
						) {

							// check upload path exists.
							$raw_file_name = sanitize_file_name( $upload_data[ $field->get_input_name() ]['name'] );

							// create directory if needed.
							$upload_dir = wpdf_get_uploads_dir($this->_form);
							$upload_dir = trailingslashit( $upload_dir ) . trailingslashit( $this->_upload_dir );
							if ( ! file_exists( $upload_dir ) ) {
								mkdir( $upload_dir );
							}

							// Randomly generate prefix.
							do {
								$prefix = substr( md5( time() ), 0, 5 );
								$file_name = $field->get_name() . '-' . $prefix . '-' . $raw_file_name;
							} while ( file_exists( $upload_dir . $file_name ) );

							if ( move_uploaded_file( $upload_data[ $field->get_input_name() ]['tmp_name'], $upload_dir . $file_name ) ) {
								$this->_data[ $field_id ] = $file_name;
							}
						} elseif ( 4 === $upload_data[ $field->get_input_name() ]['error'] ) {
							// no file uploaded, load from previously stored upload.
							if ( isset( $data[ $field->get_input_name() . '_uploaded' ] ) ) {
								$this->_data[ $field_id ] = $data[ $field->get_input_name() . '_uploaded' ];
							}
						}
					}
				}
			} else {

				if ( isset( $data[ $field->get_input_name() ] ) ) {

					$type = $field->get_type();
					if ( 'radio' === $type || 'checkbox' === $type || 'select' === $type ) {

						$sanitized_data = $field->sanitize( $data[ $field->get_input_name() ] );

						if ( is_array( $sanitized_data ) ) {

							$temp = array();
							foreach ( $sanitized_data as $v ) {
								$chosen_value = $field->get_option_value( $v );
								if ( false !== $chosen_value ) {
									$temp[] = $chosen_value;
								}
							}

							if ( ! empty( $temp ) ) {
								$this->_data[ $field_id ] = $temp;
							}
						} else {
							$chosen_value = $field->get_option_value( $sanitized_data );
							if ( false !== $chosen_value ) {
								$this->_data[ $field_id ] = $chosen_value;
							}
						}
					} else {
						$sanitized_data = $field->sanitize( $data[ $field->get_input_name() ] );
						if ( '' !== $sanitized_data ) {
							// only add non empty data.
							$this->_data[ $field_id ] = $sanitized_data;
						}
					}
				}
			}
		}
	}

	public function submission_data( $submission_data = array() ){

		if ( empty( $submission_data ) ) {
			return;
		}

		$this->_submitted = true;
		
		$this->_fields = array();

		foreach ($submission_data as $data){
			$field_id = $data->field;
			$this->_data[ $field_id ] = $data->content;
			$field = array(
				'label' => $data->field_label
			);

			$type = $data->field_type;
			switch ( $type ) {
				case 'text':
					$this->_fields[ $field_id ] = new WPDF_TextField( $field_id, $type, $field );
					break;
				case 'textarea':
					$this->_fields[ $field_id ] = new WPDF_TextareaField( $field_id, $type, $field );
					break;
				case 'select':
					$this->_fields[ $field_id ] = new WPDF_SelectField( $field_id, $type, $field );
					break;
				case 'radio':
					$this->_fields[ $field_id ] = new WPDF_RadioField( $field_id, $type, $field );
					break;
				case 'checkbox':
					$this->_fields[ $field_id ] = new WPDF_CheckboxField( $field_id, $type, $field );
					break;
				case 'file':
					$this->_fields[ $field_id ] = new WPDF_FileField( $field_id, $type, $field );
					break;
				case 'number':
					$this->_fields[ $field_id ] = new WPDF_NumberField( $field_id, $type, $field );
					break;
				default:
					$this->_fields[ $field_id ] = new WPDF_FormField( $field_id, $type, $field );
					break;
			}

		}
	}

	/**
	 * Convert data object to array
	 *
	 * @return array
	 */
	public function to_array() {

		$temp = array();
		foreach ( $this->_data as $k => $v ) {
			$temp[ $k ] = $this->get( $k );
		}

		return $temp;
	}

	/**
	 * Get field data
	 *
	 * @param string $field_id Field id.
	 *
	 * @return mixed
	 */
	public function get( $field_id ) {

		if ( $this->is_submitted() ) {
			$result = isset( $this->_data[ $field_id ] ) ? $this->_data[ $field_id ] : false;
			$result = $this->unslash_value( $result );
		} else {
			$result = isset( $this->_defaults[ $field_id ] ) ? $this->_defaults[ $field_id ] : '';
		}

		$result = apply_filters( 'wpdf/field_data', $result, $field_id );

		return $result;
	}

	/**
	 * Get raw data.
	 *
	 * @param string $field_id Field id.
	 *
	 * @return bool|mixed
	 */
	public function get_raw( $field_id ) {

		if ( $this->is_submitted() ) {
			$result = isset( $this->_raw_data[ $field_id ] ) ? $this->_raw_data[ $field_id ] : false;
		} else {
			$result = $this->_defaults[ $field_id ];
		}

		return $result;
	}

	/**
	 * Get field by id
	 *
	 * @param string $field_id Field id.
	 *
	 * @return bool|WPDF_FormField
	 */
	public function get_field( $field_id ) {
		$found = isset( $this->_fields[ $field_id ] ) ? $this->_fields[ $field_id ] : false;

		if ( ! $found ){
			return new WPDF_FormField($field_id, 'virtual');
		}

		return $found;
	}

	/**
	 * Get upload directory
	 *
	 * @return string
	 */
	public function get_upload_folder() {
		return $this->_upload_dir;
	}

	/**
	 * List keys
	 *
	 * @return array
	 */
	public function list_keys() {
		return array_keys( $this->_data );
	}

	/**
	 * Unslash Value
	 *
	 * @param string $value Value to unslash.
	 *
	 * @return array|string
	 */
	public function unslash_value( $value ) {

		// remove slashes from data and email.
		if ( is_array( $value ) ) {
			foreach ( $value as &$val ) {
				$val = wp_unslash( trim( $val ) );
			}
		} else {
			$value = wp_unslash( trim( $value ) );
		}

		return $value;
	}

	/**
	 * Get form ip
	 *
	 * @return mixed
	 */
	public function get_ip() {

		return wpdf_get_ip();
	}

	/**
	 * Has form been submitted
	 *
	 * @return bool
	 */
	public function is_submitted() {
		return $this->_submitted;
	}

	public function save_virtual_field($field_id, $value){

		$this->_data[ $field_id ] = $value;
	}
}
