<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 03/11/2016
 * Time: 21:29
 */

class WPDF_TextField extends WPDF_FormField {

	/**
	 * @param $form_data WPDF_FormData
	 */
	public function output($form_data){

		$value = $form_data->get($this->_name);
		echo '<input type="'.$this->getType().'" name="'.$this->getInputName().'" value="'.$value.'" id="'.$this->getId().'" class="'.$this->getClasses().'" />';
	}
}