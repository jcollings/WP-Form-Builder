<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 03/11/2016
 * Time: 21:29
 */

class WPDF_RadioField extends WPDF_FormField {

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

	/**
	 * Format field data to store in fields array
	 *
	 * @param $field
	 *
	 * @return array
	 */
	public function save( $field = array() ) {

		$data = parent::save( $field );

		$options = array();
		$defaults = array();
		foreach($field['value_labels'] as $arr_id => $label){
			$option_key = isset($field['value_keys'][$arr_id]) && !empty($field['value_keys'][$arr_id]) ? esc_attr($field['value_keys'][$arr_id]) : esc_attr($label);
			$options[$option_key] = $label;

			if(isset($field['value_default'][$arr_id])){
				$defaults[] = $option_key;
			}
		}

		$data['options'] = $options;
		$data['default'] = $defaults;

		if ( !empty( $field['value_default'] ) ) {
			$data['default'] = $field['value_default'];
		}

		return $data;
	}
}