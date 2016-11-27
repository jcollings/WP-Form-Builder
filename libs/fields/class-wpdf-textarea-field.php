<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 03/11/2016
 * Time: 21:29
 */

class WPDF_TextareaField extends WPDF_FormField {

	protected $_rows = 8;

	public function __construct( $name, $type, $args = array() ) {
		parent::__construct( $name, $type, $args );

		$this->_rows = isset($args['rows']) ? $args['rows'] : $this->_rows;
	}

	/**
	 * @return int
	 */
	public function getRows() {
		return $this->_rows;
	}

	/**
	 * @param $form_data WPDF_FormData
	 */
	public function output($form_data){

		$value = $form_data->get($this->_name);
		echo '<textarea name="'.$this->getInputName().'" id="'.$this->getId().'" class="'.$this->getClasses().'" rows="'.$this->getRows().'">'.$value.'</textarea>';
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
		$data['rows'] = isset($field['rows']) ? $field['rows'] : 8;

		return $data;
	}
}