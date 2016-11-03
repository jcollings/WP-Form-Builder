<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 03/11/2016
 * Time: 21:29
 */

class WPDF_FileField extends WPDF_FormField {

	/**
	 * @param $form_data WPDF_FormData
	 */
	public function output($form_data){

		$value = $form_data->get($this->_name);

		// display name of previously uploaded file and show the file uploader to allow users to overwrite upload
		echo '<input type="'.$this->getType().'" name="'.$this->getInputName().'"  />';
		if(!empty($value)) {
			echo '<input type="hidden" name="' . $this->_name . '_uploaded" value="' . $value . '" />';
		}
	}
}