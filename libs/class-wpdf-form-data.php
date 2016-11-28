<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 05/08/2016
 * Time: 18:45
 */
class WPDF_FormData{

	protected $_data = null;
	protected $_raw_data = null;
	protected $_fields = null;
	protected $_defaults = array();

	/**
	 * If the form has been submitted
	 *
	 * @var bool
	 */
	private $_submitted = true;

	/**
	 * WPDF_FormData constructor.
	 *
	 * @param $form WPDF_Form
	 * @param $data []
	 * @param $upload_data []
	 */
	public function __construct($form, $data, $upload_data){

		$fields = $form->get_fields();
		$this->_data = array();
		$this->_raw_data = array();
		$this->_fields = $fields;

		$this->_submitted = isset($data['wpdf_action']) && $data['wpdf_action'] == $form->get_name() ? true : false;

		foreach($fields as $field_id => $field){

			$this->_raw_data[$field_id] = isset($data[$field->get_input_name()]) ? $field->sanitize($data[$field->get_input_name()]) : false;
			$this->_defaults[$field_id] = $field->get_default_value();

			if($field->is_type('file')){

				if(isset($upload_data[$field->get_input_name()])){

					if( isset($upload_data[$field->get_input_name()]['name']) && isset($upload_data[$field->get_input_name()]['error']) ){

						//$this->_data[$field_id] = $upload_data[$field_id];

						if( $upload_data[$field->get_input_name()]['error'] == 0
						&& $field->isValidExt($upload_data[$field->get_input_name()])
						&& $field->isAllowedSize($upload_data[$field->get_input_name()])
						){

							// file uploaded

							// check upload path exists
							$upload_dir = wpdf_get_uploads_dir();

							$file_name = $upload_data[$field->get_input_name()]['name'];

							if(move_uploaded_file($upload_data[$field->get_input_name()]['tmp_name'], $upload_dir . $file_name )){
								$this->_data[$field_id] = $file_name;
							}
						}elseif( $upload_data[$field->get_input_name()]['error'] == 4 ){
							// no file uploaded
							// load from previously stored upload
							if(isset($data[ $field->get_input_name() . '_uploaded'])){
								$this->_data[$field_id] = $data[ $field->get_input_name() . '_uploaded'];
							}
						}
					}
				}

			}else{

				if(isset($data[$field->get_input_name()])){

					$type = $field->get_type();
					if($type == 'radio' || $type == 'checkbox' || $type == 'select'){

						$sanitized_data = $field->sanitize($data[$field->get_input_name()]);

						if(is_array($sanitized_data)){

							$temp = array();
							foreach($sanitized_data as $v){
								$chosenValue = $field->get_option_value($v);
								if($chosenValue !== false){
									$temp[] = $chosenValue;
								}
							}

							if(!empty($temp)){
								$this->_data[$field_id] = $temp;
							}

						}else{
							$chosenValue = $field->get_option_value($sanitized_data);
							if($chosenValue !== false){
								$this->_data[$field_id] = $chosenValue;
							}
						}

					}else{
						$sanitized_data = $field->sanitize($data[$field->get_input_name()]);
						if($sanitized_data !== ""){
							// only add non empty data
							$this->_data[$field_id] = $sanitized_data;
						}
					}
				}
			}
		}
	}

	public function toArray(){

		$temp = array();
		foreach($this->_data as $k => $v){
			$temp[$k] = $this->get($k);
		}
		return $temp;
	}

	public function get($field_id){

		if($this->isSubmitted()){
			$result = isset( $this->_data[$field_id] ) ? $this->_data[$field_id] : false;
			$result = $this->unslashValue($result);
		}else{
			$result = $this->_defaults[$field_id];
		}

		$result = apply_filters('wpdf/field_data', $result, $field_id);
		return $result;
	}

	public function getRaw($field_id){

		if($this->isSubmitted()){
			$result = isset( $this->_raw_data[$field_id] ) ? $this->_raw_data[$field_id] : false;
		}else{
			$result = $this->_defaults[$field_id];
		}

		return $result;
	}

	public function getField($field_id){
		return isset( $this->_fields[$field_id] ) ? $this->_fields[$field_id] : false;
	}

	public function list_keys(){
		return array_keys($this->_data);
	}

	public function unslashValue($value){

		// remove slashes from data and email
		if(is_array($value)){
			foreach($value as &$val){
				$val = wp_unslash( trim( $val ) );
			}
		}else{
			$value = wp_unslash( trim( $value ) );
		}

		return $value;
	}

	public function getIp(){

		return wpdf_getIp();
	}

	public function isSubmitted(){
		return $this->_submitted;
	}
}