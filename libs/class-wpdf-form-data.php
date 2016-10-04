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

		$fields = $form->getFields();
		$this->_data = array();
		$this->_raw_data = array();
		$this->_fields = $fields;

		$this->_submitted = isset($data['wpdf_action']) && $data['wpdf_action'] == $form->getName() ? true : false;

		foreach($fields as $field_id => $field){

			$this->_raw_data[$field_id] = isset($data[$field->getInputName()]) ? $field->sanitize($data[$field->getInputName()]) : false;
			$this->_defaults[$field_id] = $field->getDefaultValue();

			if($field->isType('file')){

				if(isset($upload_data[$field->getInputName()])){

					if(isset($upload_data[$field->getInputName()]['name']) && isset($upload_data[$field->getInputName()]['error']) ){

						//$this->_data[$field_id] = $upload_data[$field_id];

						if( $upload_data[$field->getInputName()]['error'] == 0 ){

							// file uploaded

							// check upload path exists
							$upload_dir = wpdf_get_uploads_dir();

							$file_name = $upload_data[$field->getInputName()]['name'];

							if(move_uploaded_file($upload_data[$field->getInputName()]['tmp_name'], $upload_dir . $file_name )){
								$this->_data[$field_id] = $file_name;
							}
						}elseif( $upload_data[$field->getInputName()]['error'] == 4 ){
							// no file uploaded
							// load from previously stored upload
							if(isset($data[$field->getInputName().'_uploaded'])){
								$this->_data[$field_id] = $data[$field->getInputName().'_uploaded'];
							}
						}
					}
				}

			}else{

				if(isset($data[$field->getInputName()])){

					$type = $field->getType();
					if($type == 'radio' || $type == 'checkbox' || $type == 'select'){

						$sanitized_data = $field->sanitize($data[$field->getInputName()]);

						if(is_array($sanitized_data)){

							$temp = array();
							foreach($sanitized_data as $v){
								$chosenValue = $field->getOptionValue($v);
								if($chosenValue !== false){
									$temp[] = $chosenValue;
								}
							}

							if(!empty($temp)){
								$this->_data[$field_id] = $temp;
							}

						}else{
							$chosenValue = $field->getOptionValue($sanitized_data);
							if($chosenValue !== false){
								$this->_data[$field_id] = $chosenValue;
							}
						}

					}else{
						$sanitized_data = $field->sanitize($data[$field->getInputName()]);
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

		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			//check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			//to check ip is pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}

	public function isSubmitted(){
		return $this->_submitted;
	}
}