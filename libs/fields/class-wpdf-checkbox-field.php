<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 03/11/2016
 * Time: 21:29
 */

class WPDF_CheckboxField extends WPDF_FormField {

	/**
	 * @param $form_data WPDF_FormData
	 */
	public function output($form_data){

		$value = $form_data->getRaw($this->_name);

		if(isset($this->_args['options']) && !empty($this->_args['options'])){

			$name = $this->getInputName();
			if($this->isType('checkbox')){
				$name .= '[]';
			}

			echo '<div class="wpdf-choices">';

			foreach($this->_args['options'] as $key => $option){

				if(is_array($value)){
					$checked = in_array("".$key, $value) ? 'checked="checked"' : '';
				}else{
					$checked = "".$key === $value ? 'checked="checked"' : '';
				}

				echo '<label>';
				echo '<input type="'.$this->getType().'" name="'.$name.'"'.$checked.' value="'.$key.'" class="wpdf-field">' . $option;
				echo '</label>';
			}

			echo '</div>';
		}
	}
}