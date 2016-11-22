<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 22/11/2016
 * Time: 13:36
 */
class WPDF_Text{

	protected $_default = null;

	public function __construct() {

		$this->_default = array(
			// field validation errors
			'validation_required' => __('This field is required', "wpdf"),
			'validation_email' => __('Please enter a valid email address', "wpdf"),
			'validation_unique' => __('This value has already been previously entered', "wpdf"),
			'validation_min_length' => __('Please enter a value longer than %d', "wpdf"),
			'validation_max_length' => __('Please enter a value shorter than %d', "wpdf"),
			// upload errors
			'upload_max_size' => __("The uploaded file is to large.", "wpdf"),
			'upload_general' => __("An error occured when uploading the file.", "wpdf"),
			'upload_ini_size' => __("The uploaded file exceeds the upload_max_filesize directive in php.ini", "wpdf"),
			'upload_form_size' => __("The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form", "wpdf"),
			'upload_partial' => __("The uploaded file was only partially uploaded", "wpdf"),
			'upload_no_file' => __("No file was uploaded", "wpdf"),
			'upload_no_tmp_dir' => __("Missing a temporary folder", "wpdf"),
			'upload_cant_write' => __("Failed to write file to disk", "wpdf"),
			'upload_extension' => __("File upload stopped by extension", "wpdf"),
			'upload_unknown' => __("Unknown upload error", "wpdf"),
			// menu text
			'menu_fields' => __("Fields", "wpdf"),
			'menu_settings' => __("Settings", "wpdf"),
			'menu_style' => __("Style", "wpdf"),
			'menu_notifications' => __("Notifications", "wpdf"),
			'menu_submissions' => __("Submissions", "wpdf"),
			// shortcode
			'shortcode_error_empty_forms' => __('You currently have no forms available, please create one and try again.','wpdf'),
			'shortcode_error_selection' => __('An error occurred with your selected, please try again.','wpdf'),
		);

	}

	public function get($key, $prefix = ''){

		// if prefix provided
		if(!empty($prefix)){
			$prefix .= '_';
		}

		return isset($this->_default[$prefix . $key]) ? $this->_default[$prefix . $key] : '';
	}

}