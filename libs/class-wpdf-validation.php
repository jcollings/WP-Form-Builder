<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 04-Aug-16
 * Time: 8:13 PM
 */

class WPDF_Validation{

	protected $_error = null;
	protected $_rules = array();
	protected $_validation_msgs = array();


	public function __construct($rules = array()) {
		$this->_rules = $rules;

		$this->_validation_msgs = array(
			'required' => __('This field is required', "wpdf"),
			'email' => __('Please enter a valid email address', "wpdf"),
			'min_length' => __('Please enter a value longer than %d', "wpdf"),
			'max_length' => __('Please enter a value shorter than %d', "wpdf")
		);
	}

	/**
	 * @param $field WPDF_FormField
	 * @param $post_data
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function validate($field, $post_data){

		// hook into field validation
		$error = apply_filters('wpdf/validate_field', null, $field->getName(), $post_data);
		if(!empty($error)){
			$this->_error = $error;
			return false;
		}

		if(isset($this->_rules[$field->getName()])) {
			if(!empty($this->_rules[$field->getName()])) {
				foreach ( $this->_rules[ $field->getName() ] as $rule ) {
					$type = isset( $rule['type'] ) ? $rule['type'] : false;
					$args = array();

					if(is_array($type) && count($type) > 0){

						// validation type is first element in array, args follow after
						$t = array_shift($type);
						$args = $type;
						$type = $t;
					}

					switch ( $type ) {
						case 'required':
							if ( ! $this->is_required($field->getName(), $post_data) ) {
								$this->set_error($rule);
								return false;
							}
							break;
						case 'email':
							if ( ! $this->is_valid_email($field->getName(), $post_data) ) {
								$this->set_error($rule);
								return false;
							}
							break;
						case 'min_length':

							// throw error if no argumments passed
							if(count($args) == 0){
								throw new Exception(__("No argument for minimum length validation", "wpdf"));
							}

							if( !$this->is_min_length($field->getName(), $args[0], $post_data) ){
								$this->set_error($rule);
								return false;
							}
							break;
						case 'max_length':

							// throw error if no argumments passed
							if(count($args) == 0){
								throw new Exception(__("No argument for maximum length validation", "wpdf"));
							}

							if( !$this->is_max_length($field->getName(), $args[0], $post_data) ){
								$this->set_error($rule);
								return false;
							}
							break;
					}
				}
			}
		}

		return true;
	}

	public function get_error(){
		return $this->_error;
	}

	public function get_upload_error($code){

		if(WP_DEBUG) {

			switch ( $code ) {
				case UPLOAD_ERR_INI_SIZE:
					$message = __("The uploaded file exceeds the upload_max_filesize directive in php.ini", "wpdf");
					break;
				case UPLOAD_ERR_FORM_SIZE:
					$message = __("The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form", "wpdf");
					break;
				case UPLOAD_ERR_PARTIAL:
					$message = __("The uploaded file was only partially uploaded", "wpdf");
					break;
				case UPLOAD_ERR_NO_FILE:
					$message = __("No file was uploaded", "wpdf");
					break;
				case UPLOAD_ERR_NO_TMP_DIR:
					$message = __("Missing a temporary folder", "wpdf");
					break;
				case UPLOAD_ERR_CANT_WRITE:
					$message = __("Failed to write file to disk", "wpdf");
					break;
				case UPLOAD_ERR_EXTENSION:
					$message = __("File upload stopped by extension", "wpdf");
					break;

				default:
					$message = __("Unknown upload error", "wpdf");
					break;
			}

			return $message;

		}else{

			if($code == UPLOAD_ERR_INI_SIZE){
				$message = __("The uploaded file is to large.", "wpdf");
			}else{
				$message = __("An error occured when uploading the file.", "wpdf");
			}

			return $message;
		}
	}

	protected function set_error($rule){

		// default error message
		$error = '';

		$type = $rule['type'];
		$args = array();

		if(is_array($type)){
			$temp = array_shift($type);
			$args = $type;
			$type = $temp;
		}

		// check for custom error message in rule
		// todo: vsprintf need to check amount of arguments with string, so no errors will be thrown
		if(isset($rule['msg'])){
			$error = vsprintf( $rule['msg'] , $args );
		}elseif(isset($this->_validation_msgs[$type])){
			$error = vsprintf ( $this->_validation_msgs[$type], $args );
		}

		// save error for later
		$this->_error = $error;
	}

	/**
	 * Required Field
	 *
	 * @param $field string
	 * @param $post_data array
	 *
	 * @return bool
	 */
	protected function is_required($field, $post_data){

		if(isset($post_data[$field]) && !empty($post_data[$field])){
			return true;
		}

		return false;
	}

	/**
	 * Valid email address
	 *
	 * @param $field string
	 * @param $post_data array
	 *
	 * @return bool
	 */
	protected function is_valid_email($field, $post_data){

		// escape early as data is not found
		if(!isset($post_data[$field]) || empty($post_data[$field])){
			return true;
		}

		if(is_email($post_data[$field])){
			return true;
		}

		return false;
	}

	/**
	 * Minimum character requirement
	 *
	 * @param $field string
	 * @param $length int
	 * @param $post_data array
	 *
	 * @return bool
	 */
	public function is_min_length($field, $length, $post_data){

		// escape early as data is not found
		if(!isset($post_data[$field]) || empty($post_data[$field])){
			return true;
		}

		if(strlen($post_data[$field]) >= $length){
			return true;
		}

		return false;
	}

	/**
	 * Maximum character requirement
	 *
	 * @param $field string
	 * @param $length int
	 * @param $post_data array
	 *
	 * @return bool
	 */
	public function is_max_length($field, $length, $post_data){

		// escape early as data is not found
		if(!isset($post_data[$field]) || empty($post_data[$field])){
			return true;
		}

		if(strlen($post_data[$field]) <= $length){
			return true;
		}

		return false;
	}
}