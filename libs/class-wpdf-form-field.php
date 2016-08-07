<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 05/08/2016
 * Time: 18:58
 */

class WPDF_FormField{

	protected $_name;
	protected $_type;
	protected $_label;
	protected $_args;

	public function __construct($name, $type, $args = array()) {
		$this->_name = $name;
		$this->_type = $type;
		$this->_args = $args;

		if(isset($args['label'])){
			$this->_label = $args['label'];
		}else{
			$this->_label = ucfirst($this->_name);
		}

	}

	/**
	 * @param $form_data WPDF_FormData
	 */
	public function output($form_data){

		switch($this->getType()){
			case 'textarea':

				$value = $form_data->get($this->_name);

				echo '<textarea name="'.$this->getInputName().'" id="'.$this->getId().'" class="'.$this->getClasses().'">'.$value.'</textarea>';
				break;
			case 'select':

				$value = $form_data->getRaw($this->_name);

				echo '<select name="'.$this->getInputName().'" id="'.$this->getId().'" class="'.$this->getClasses().'">';

				if(isset($this->_args['empty'])){
					if( $this->_args['empty'] != false ){
						echo '<option value="">'.$this->_args['empty'].'</option>';
					}
				}else{
					echo '<option value="">Select an option</option>';
				}

				if(isset($this->_args['options']) && !empty($this->_args['options'])){
					foreach($this->_args['options'] as $key => $option){
						$selected = "".$key === $value ? 'selected="selected"' : '';
						echo '<option value="'.$key.'"'.$selected.'>'.$option.'</option>';
					}
				}

				echo '</select>';

				break;
			case 'file':

				$value = $form_data->get($this->_name);

				// display name of previously uploaded file and show the file uploader to allow users to overwrite upload
				echo '<input type="'.$this->getType().'" name="'.$this->getInputName().'"  />';
				if(!empty($value)) {
					echo '<input type="hidden" name="' . $this->_name . '_uploaded" value="' . $value . '" />';
				}
				break;
			case 'radio':
			case 'checkbox':

				$value = $form_data->getRaw($this->_name);

				if(isset($this->_args['options']) && !empty($this->_args['options'])){

					$name = $this->getInputName();
					if($this->isType('checkbox')){
						$name .= '[]';
					}

					foreach($this->_args['options'] as $key => $option){

						if(is_array($value)){
							$checked = in_array("".$key, $value) ? 'checked="checked"' : '';
						}else{
							$checked = "".$key === $value ? 'checked="checked"' : '';
						}

						echo '<label>';
						echo '<input type="'.$this->getType().'" name="'.$name.'"'.$checked.' value="'.$key.'" >' . $option;
						echo '</label>';
					}
				}

				break;

			case 'text':
			default:

				$value = $form_data->get($this->_name);
				echo '<input type="'.$this->getType().'" name="'.$this->getInputName().'" value="'.$value.'" id="'.$this->getId().'" class="'.$this->getClasses().'" />';

				break;
		}

	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->_type;
	}

	public function isType($type){
		if($this->_type == $type){
			return true;
		}
		return false;
	}

	public function getInputName(){
		return 'wpdf_field_' . $this->_name;
	}

	public function getOptionValue($key){

		if(isset($this->_args['options']) && !empty($this->_args['options']) && array_key_exists($key, $this->_args['options'])){
			return $this->_args['options'][$key];
		}

		return false;
	}

	public function sanitize($data){

		if(is_array($data)){

			$output = array();

			if(!empty($data)) {
				foreach ( $data as $k => $d ) {
					$output[ $k ] = $this->sanitize( $d );
				}
			}

			return $output;

		}else{
			switch($this->getType()){
				case 'select':
				case 'radio':
				case 'checkbox':
					return esc_attr($data);
				default:
					return sanitize_text_field($data);
			}
		}


	}

	/**
	 * @return string
	 */
	public function getLabel() {
		return $this->_label;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->_name;
	}

	public function getId(){
		return '';
	}

	public function getClasses(){
		return '';
	}
}