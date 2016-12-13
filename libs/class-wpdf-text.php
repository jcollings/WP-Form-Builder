<?php
/**
 * Text library
 *
 * @package WPDF
 * @author James Collings
 * @created 22/11/2016
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Class WPDF_Text
 */
class WPDF_Text {

	/**
	 * Default text
	 *
	 * @var array|null
	 */
	protected $_default = null;

	/**
	 * WPDF_Text constructor.
	 */
	public function __construct() {

		$this->_default = array(
			// field validation errors.
			'validation_required'         => __( 'This field is required', 'wpdf' ),
			'validation_email'            => __( 'Please enter a valid email address', 'wpdf' ),
			'validation_unique'           => __( 'This value has already been previously entered', 'wpdf' ),
			'validation_min_length'       => __( 'Please enter a value longer than %d', 'wpdf' ),
			'validation_max_length'       => __( 'Please enter a value shorter than %d', 'wpdf' ),
			// upload errors.
			'upload_max_size'             => __( 'The uploaded file is to large. (max size: %dmb)', 'wpdf' ),
			'upload_general'              => __( 'An error occured when uploading the file.', 'wpdf' ),
			'upload_ini_size'             => __( 'The uploaded file exceeds the upload_max_filesize directive in php.ini', 'wpdf' ),
			'upload_form_size'            => __( 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form', 'wpdf' ),
			'upload_partial'              => __( 'The uploaded file was only partially uploaded', 'wpdf' ),
			'upload_no_file'              => __( 'No file was uploaded', 'wpdf' ),
			'upload_no_tmp_dir'           => __( 'Missing a temporary folder', 'wpdf' ),
			'upload_cant_write'           => __( 'Failed to write file to disk', 'wpdf' ),
			'upload_extension'            => __( 'File upload stopped by extension', 'wpdf' ),
			'upload_unknown'              => __( 'Unknown upload error', 'wpdf' ),
			'upload_invalid_ext'          => __( 'The upload file type is not allowed', 'wpdf' ),
			// menu text.
			'menu_fields'                 => __( 'Fields', 'wpdf' ),
			'menu_settings'               => __( 'Settings', 'wpdf' ),
			'menu_style'                  => __( 'Style', 'wpdf' ),
			'menu_notifications'          => __( 'Notifications', 'wpdf' ),
			'menu_submissions'            => __( 'Submissions', 'wpdf' ),
			// shortcode.
			'shortcode_error_empty_forms' => __( 'You currently have no forms available, please create one and try again.', 'wpdf' ),
			'shortcode_error_selection'   => __( 'An error occurred with your selected, please try again.', 'wpdf' ),
			// general.
			'general_form_saved' => __( 'Changes have been saved.', 'wpdf' ),

			// General field text.
			'fields.general.label.help' => __( 'Text displayed before the field on the form', 'wpdf' ),
			'fields.general.label.label' => __( 'Label', 'wpdf' ),
			'fields.general.placeholder.help' => __( 'Text displayed in the field when no value is entered', 'wpdf' ),
			'fields.general.placeholder.label' => __( 'Placeholder', 'wpdf' ),
			'fields.general.css_classes.help' => __( 'Add custom css classes to field output', 'wpdf' ),
			'fields.general.css_classes.label' => __( 'CSS Classes', 'wpdf' ),
			'fields.general.delete.help' => __( 'Delete field from form', 'wpdf' ),
			'fields.general.toggle.help' => __( 'Toggle display of field settings', 'wpdf' ),
			'fields.validation.heading.text' => __( 'Validation Rules', 'wpdf' ),
			'fields.validation.heading.help' => __( 'Add rules to validate data entered', 'wpdf' ),
			'fields.validation.button.text' => __( 'Add validation Rule', 'wpdf' ),
			'fields.validation.type.option.placeholder' => __( 'Choose Validation Type', 'wpdf' ),
			'fields.validation.type.option.required' => __( 'Required', 'wpdf' ),
			'fields.validation.type.option.email' => __( 'Email', 'wpdf' ),
			'fields.validation.type.option.unique' => __( 'Unique', 'wpdf' ),
			'fields.validation.message.label' => __( 'Validation Message', 'wpdf' ),
			'fields.validation.message.help' => __( 'Enter text to be displayed, or leave blank to display default text.', 'wpdf' ),

			// Text field text.
			'fields.text.default.help' => __( 'Default value shown when the form is loaded', 'wpdf' ),
			'fields.text.default.label' => __( 'Default Value', 'wpdf' ),

			// Textarea field text.
			'fields.textarea.default.help' => __( 'Default value shown when the form is loaded', 'wpdf' ),
			'fields.textarea.default.label' => __( 'Default Value', 'wpdf' ),
			'fields.textarea.rows.help' => __( 'Changes the height of the text area by how many rows are displayed', 'wpdf' ),
			'fields.textarea.rows.label' => __( 'Rows', 'wpdf' ),

			// File file text.
			'fields.file.max_file_size.label' => __( 'Maximum file size (Server Limit: %d)', 'wpdf' ),
			'fields.file.max_file_size.help' => __( 'Maximum size of file allowed to be uploaded', 'wpdf' ),
			'fields.file.allowed_ext.label' => __( 'Allowed Extensions', 'wpdf' ),
			'fields.file.allowed_ext.help' => __( 'Comma separated list of allowed file extensions (jpg,jpeg,png)', 'wpdf' ),

			// Number field text.
			'fields.number.type.help' => __( 'Field type to display', 'wpdf' ),
			'fields.number.type.label' => __( 'Type', 'wpdf' ),
			'fields.number.type.option.input' => __( 'Number Input', 'wpdf' ),
			'fields.number.type.option.input-range' => __( 'Number Range Input', 'wpdf' ),
			'fields.number.type.option.slider' => __( 'Number Slider', 'wpdf' ),
			'fields.number.type.option.slider-range' => __( 'Number Range Slider', 'wpdf' ),
			'fields.number.min.help' => __( 'Minimum allowed value', 'wpdf' ),
			'fields.number.min.label' => __( 'Minimum', 'wpdf' ),
			'fields.number.max.help' => __( 'Maximum allowed value', 'wpdf' ),
			'fields.number.max.label' => __( 'Maximum', 'wpdf' ),
			'fields.number.step.help' => __( 'Number increment', 'wpdf' ),
			'fields.number.step.label' => __( 'Number Increment', 'wpdf' ),
			'fields.number.default.help' => __( 'Default Value', 'wpdf' ),
			'fields.number.default.label' => __( 'Default Value', 'wpdf' ),

			// Checkbox field text.
			'fields.checkbox.values.heading.label' => __( 'Values', 'wpdf' ),
			'fields.checkbox.values.heading.help' => __( 'List of available options', 'wpdf' ),
			'fields.checkbox.values.label.label' => __( 'Label', 'wpdf' ),
			'fields.checkbox.values.label.help' => __( 'Text displayed in dropdown', 'wpdf' ),
			'fields.checkbox.values.value.label' => __( 'Value', 'wpdf' ),
			'fields.checkbox.values.value.help' => __( 'Value stored in when selected, leave blank to auto generate from label', 'wpdf' ),
			'fields.checkbox.values.default.label' => __( 'Default', 'wpdf' ),
			'fields.checkbox.values.default.help' => __( 'Check value to make default when form is loaded', 'wpdf' ),

			// Radio field text.
			'fields.radio.values.heading.label' => __( 'Values', 'wpdf' ),
			'fields.radio.values.heading.help' => __( 'List of available options', 'wpdf' ),
			'fields.radio.values.label.label' => __( 'Label', 'wpdf' ),
			'fields.radio.values.label.help' => __( 'Text displayed in dropdown', 'wpdf' ),
			'fields.radio.values.value.label' => __( 'Value', 'wpdf' ),
			'fields.radio.values.value.help' => __( 'Value stored in when selected, leave blank to auto generate from label', 'wpdf' ),
			'fields.radio.values.default.label' => __( 'Default', 'wpdf' ),
			'fields.radio.values.default.help' => __( 'Check value to make default when form is loaded', 'wpdf' ),

			// Select field text.
			'fields.select.empty_text.label' => __( 'Empty Text', 'wpdf' ),
			'fields.select.empty_text.help' => __( 'Default value shown when the form is loaded, leave empty to not show', 'wpdf' ),
			'fields.select.select_type.label' => __( 'Select Type', 'wpdf' ),
			'fields.select.select_type.help' => __( 'Multiple selects allow the user to select multiple values instead of only a single value.', 'wpdf' ),
			'fields.select.select_type.single.label' => __( 'Single', 'wpdf' ),
			'fields.select.select_type.multiple.label' => __( 'Multiple', 'wpdf' ),
			'fields.select.values.heading.label' => __( 'Values', 'wpdf' ),
			'fields.select.values.heading.help' => __( 'List of available options', 'wpdf' ),
			'fields.select.values.label.label' => __( 'Label', 'wpdf' ),
			'fields.select.values.label.help' => __( 'Text displayed in dropdown', 'wpdf' ),
			'fields.select.values.value.label' => __( 'Value', 'wpdf' ),
			'fields.select.values.value.help' => __( 'Value stored in when selected, leave blank to auto generate from label', 'wpdf' ),
			'fields.select.values.default.label' => __( 'Default', 'wpdf' ),
			'fields.select.values.default.help' => __( 'Check value to make default when form is loaded', 'wpdf' ),
		);

	}

	/**
	 * Get text string
	 *
	 * @param string $key String key.
	 * @param string $prefix Prefix form key.
	 *
	 * @return mixed|string
	 */
	public function get( $key, $prefix = '' ) {

		// if prefix provided.
		if ( ! empty( $prefix ) ) {
			$prefix .= '_';
		}

		return isset( $this->_default[ $prefix . $key ] ) ? $this->_default[ $prefix . $key ] : '';
	}

}
