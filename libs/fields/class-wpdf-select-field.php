<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 03/11/2016
 * Time: 21:29
 */

class WPDF_SelectField extends WPDF_FormField {

	/**
	 * @param $form_data WPDF_FormData
	 */
	public function output($form_data){

		$value = $form_data->getRaw($this->_name);

		echo '<select name="'.$this->getInputName().'" id="'.$this->getId().'" class="'.$this->getClasses().'">';

		if(isset($this->_args['empty'])){
			if( $this->_args['empty'] != false ){
				echo '<option value="">'.$this->_args['empty'].'</option>';
			}
		}else{
			echo sprintf('<option value="">%s</option>', __("Select an option", "wpdf") );
		}

		if(isset($this->_args['options']) && !empty($this->_args['options'])){
			foreach($this->_args['options'] as $key => $option){

				$selected = '';
				if(is_array($value) && in_array($key, $value)){
					$selected = 'selected="selected"';
				}elseif(!is_array($value) && !empty($value) && $key === $value){
					$selected = 'selected="selected"';
				}
				echo '<option value="'.$key.'"'.$selected.'>'.$option.'</option>';
			}
		}

		echo '</select>';
	}
}