<?php

/**
 * Class WPDF_FormField
 *
 * Base field class
 */
class WPDF_FormField {

	/**
	 * Field Id
	 *
	 * @var string $_name
	 */
	protected $_name;

	/**
	 * Field Type
	 *
	 * @var string $_type
	 */
	protected $_type;

	/**
	 * Field Label
	 *
	 * @var string $_label
	 */
	protected $_label;

	/**
	 * Raw Field arguments
	 *
	 * @var array $_args
	 */
	protected $_args;

	/**
	 * Field default value
	 *
	 * @var bool|mixed
	 */
	protected $_default;

	/**
	 * Field placeholder text
	 *
	 * @var bool|mixed
	 */
	protected $_placeholder;

	/**
	 * Field options
	 *
	 * @var bool|mixed
	 */
	protected $_options;

	/**
	 * Field row classes
	 *
	 * @var mixed|string
	 */
	protected $_extra_class;

	/**
	 * WPDF_FormField constructor.
	 *
	 * @param string $name Field name.
	 * @param string $type Field type.
	 * @param array  $args Field arguments.
	 */
	public function __construct( $name, $type, $args = array() ) {
		$this->_name = $name;
		$this->_type = $type;
		$this->_args = $args;

		if ( isset( $args['label'] ) ) {
			$this->_label = $args['label'];
		} else {
			$this->_label = ucfirst( $this->_name );
		}

		$this->_default     = isset( $args['default'] ) ? $args['default'] : false;
		$this->_placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : false;
		$this->_options     = isset( $args['options'] ) && is_array( $args['options'] ) ? $args['options'] : false;
		$this->_extra_class = isset( $args['extra_class'] ) ? $args['extra_class'] : '';
	}

	/**
	 * Output field on the frontend
	 *
	 * @param WPDF_FormData $form_data Field data use to generate output.
	 */
	public function output( $form_data ) {

	}

	/**
	 * Display field settings in form editor
	 */
	public function display_settings() {
		if ( file_exists( WPDF()->get_plugin_dir() . 'templates/admin/fields/' . $this->get_type() . '.php' ) ) {
			require WPDF()->get_plugin_dir() . 'templates/admin/fields/' . $this->get_type() . '.php';
		}
	}

	/**
	 * Get field type
	 *
	 * @return string
	 */
	public function get_type() {
		return $this->_type;
	}

	/**
	 * Check to see if field is of type
	 *
	 * @param string $type Type to compare with.
	 *
	 * @return bool
	 */
	public function is_type( $type ) {
		if ( $this->_type === $type ) {
			return true;
		}

		return false;
	}

	/**
	 * Get input field name
	 *
	 * @return string
	 */
	public function get_input_name() {
		return 'wpdf_field_' . $this->_name;
	}

	/**
	 * Get option value by key
	 *
	 * @param string $key option key.
	 *
	 * @return bool
	 */
	public function get_option_value( $key ) {

		if ( isset( $this->_args['options'] ) && ! empty( $this->_args['options'] ) && array_key_exists( $key, $this->_args['options'] ) ) {
			return $this->_args['options'][ $key ];
		}

		return false;
	}

	/**
	 * Sanitize field data
	 *
	 * @param array $data field submitted data.
	 *
	 * @return array|string
	 */
	public function sanitize( $data ) {

		if ( is_array( $data ) ) {

			$output = array();

			if ( ! empty( $data ) ) {
				foreach ( $data as $k => $d ) {
					$output[ $k ] = $this->sanitize( $d );
				}
			}

			return $output;
		}

		$data = wp_check_invalid_utf8( $data );
		$data = wp_kses_no_null( $data );

		return sanitize_text_field( $data );
	}

	/**
	 * Get field label
	 *
	 * @return string
	 */
	public function get_label() {
		return $this->_label;
	}

	/**
	 * Get field name
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->_name;
	}

	/**
	 * Get field placeholder text
	 *
	 * @return bool|mixed
	 */
	public function get_placeholder() {
		return $this->_placeholder;
	}

	/**
	 * Get field options
	 *
	 * @return bool|mixed
	 */
	public function get_options() {
		return $this->_options;
	}

	/**
	 * Get field id
	 *
	 * @return string
	 */
	public function get_id() {
		return '';
	}

	/**
	 * Get field row extra classes
	 *
	 * @return mixed|string
	 */
	public function get_extra_classes() {

		if ( ! empty( $this->_extra_class ) && is_string( $this->_extra_class ) ) {
			return $this->_extra_class;
		}

		return '';
	}

	/**
	 * Get field input classes
	 *
	 * @return string
	 */
	public function get_classes() {
		return 'wpdf-field';
	}

	/**
	 * Get default value
	 *
	 * @return mixed|string
	 */
	public function get_default_value() {
		return $this->_default;
	}

	/**
	 * Format field data to store in fields array
	 *
	 * @param array $field File data to be parsed.
	 *
	 * @return array
	 */
	public function save( $field ) {

		$data = array(
			'type'        => $field['type'],
			'label'       => $field['label'],
			'placeholder' => isset( $field['placeholder'] ) ? $field['placeholder'] : '',
			'default'     => isset( $field['default'] ) ? $field['default'] : '',
			'extra_class' => isset( $field['css_class'] ) ? $field['css_class'] : '',
		);

		$data['validation'] = array(
			array( 'type' => 'required' ),
			array( 'type' => 'email' ),
		);

		$rules              = array();
		$data['validation'] = array();
		if ( isset( $field['validation'] ) && ! empty( $field['validation'] ) ) {
			foreach ( $field['validation'] as $rule ) {

				// skip if empty value.
				if ( empty( $rule ) ) {
					continue;
				}

				$rule_arr = array(
					'type' => $rule['type'],
				);

				if ( isset( $rule['msg'] ) && ! empty( $rule['msg'] ) ) {
					$rule_arr['msg'] = $rule['msg'];
				}

				$rules[] = $rule_arr;
			}
		}
		if ( ! empty( $rules ) ) {
			$data['validation'] = $rules;
		}

		return $data;
	}
}
