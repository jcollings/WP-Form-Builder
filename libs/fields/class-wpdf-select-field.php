<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 03/11/2016
 * Time: 21:29
 */

class WPDF_SelectField extends WPDF_FormField {

	protected $_empty;
	protected $_select_type;

	public function __construct( $name, $type, $args = array() ) {
		parent::__construct( $name, $type, $args );

		$this->_empty = isset($args['empty']) ? $args['empty'] : 'Please select an option';
		$this->_select_type = isset($args['select_type']) ? $args['select_type'] : 'single';
	}

	/**
	 * @param $form_data WPDF_FormData
	 */
	public function output($form_data){

		$value = $form_data->getRaw($this->_name);
		$attrs = '';
		$name = $this->getInputName();
		if($this->getSelectType() == 'multiple'){
			$attrs .= ' multiple="multiple"';
			$name .= '[]';
		}

		echo '<select name="'.$name.'" id="'.$this->getId().'" class="'.$this->getClasses().'"'.$attrs.'>';

		// only show empty value if not multiple select
		if($this->getSelectType() !== 'multiple') {
			if ( isset( $this->_args['empty'] ) ) {
				if ( $this->_args['empty'] != false ) {
					echo '<option value="">' . $this->_args['empty'] . '</option>';
				}
			} else {
				echo sprintf( '<option value="">%s</option>', __( "Select an option", "wpdf" ) );
			}
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

	/**
	 * @return mixed|string
	 */
	public function getEmpty() {
		return $this->_empty;
	}

	/**
	 * @return mixed|string
	 */
	public function getSelectType() {
		return $this->_select_type;
	}
}