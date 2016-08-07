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

	/**
	 * WPDF_FormData constructor.
	 *
	 * @param $fields WPDF_FormField[]
	 * @param $data []
	 * @param $upload_data []
	 */
	public function __construct($fields, $data, $upload_data){

		$this->_data = array();
		$this->_raw_data = array();
		$this->_fields = $fields;

		foreach($fields as $field_id => $field){

			$this->_raw_data[$field_id] = isset($data[$field_id]) ? $field->sanitize($data[$field_id]) : false;

			if($field->isType('file')){

				if(isset($upload_data[$field_id])){

					if(isset($upload_data[$field_id]['name']) && isset($upload_data[$field_id]['error']) ){

						//$this->_data[$field_id] = $upload_data[$field_id];

						if( $upload_data[$field_id]['error'] == 0 ){

							// file uploaded

							// check upload path exists
							$upload_dir = wpdf_get_uploads_dir();

							$file_name = $upload_data[$field_id]['name'];

							if(move_uploaded_file($upload_data[$field_id]['tmp_name'], $upload_dir . $file_name )){
								$this->_data[$field_id] = $file_name;
							}
						}elseif( $upload_data[$field_id]['error'] == 4 ){
							// no file uploaded
							// load from previously stored upload
							if(isset($data[$field_id.'_uploaded'])){
								$this->_data[$field_id] = $data[$field_id.'_uploaded'];
							}
						}
					}
				}

			}else{

				if(isset($data[$field_id])){

					$type = $field->getType();
					if($type == 'radio' || $type == 'checkbox' || $type == 'select'){

						if(is_array($data[$field_id])){

							$temp = array();
							foreach($data[$field_id] as $v){
								$chosenValue = $field->getOptionValue($v);
								if($chosenValue !== false){
									$temp[] = $chosenValue;
								}
							}

							if(!empty($temp)){
								$this->_data[$field_id] = $temp;
							}

						}else{
							$chosenValue = $field->getOptionValue($data[$field_id]);
							if($chosenValue !== false){
								$this->_data[$field_id] = $chosenValue;
							}
						}

					}else{
						$this->_data[$field_id] = $field->sanitize($data[$field_id]);
					}
				}
			}
		}
	}

	public function toArray(){
		return $this->_data;
	}

	public function get($field_id){
		return isset( $this->_data[$field_id] ) ? $this->_data[$field_id] : false;
	}

	public function getRaw($field_id){
		return isset( $this->_raw_data[$field_id] ) ? $this->_raw_data[$field_id] : false;
	}

	public function getField($field_id){
		return isset( $this->_fields[$field_id] ) ? $this->_fields[$field_id] : false;
	}

	public function list_keys(){
		return array_keys($this->_data);
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
}