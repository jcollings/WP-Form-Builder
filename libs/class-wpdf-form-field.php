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
	protected $_default;
	protected $_placeholder;
	protected $_options;
	protected $_extra_class;

	public function __construct($name, $type, $args = array()) {
		$this->_name = $name;
		$this->_type = $type;
		$this->_args = $args;

		if(isset($args['label'])){
			$this->_label = $args['label'];
		}else{
			$this->_label = ucfirst($this->_name);
		}

		$this->_default = isset($args['default']) ? $args['default'] : false;
		$this->_placeholder = isset($args['placeholder']) ? $args['placeholder'] : false;
		$this->_options = isset($args['options']) && is_array($args['options']) ? $args['options'] : false;
		$this->_extra_class = isset($args['extra_class']) ? $args['extra_class'] : '';
	}

	/**
	 * Output field on the frontend
	 *
	 * @param $form_data WPDF_FormData
	 */
	public function output($form_data){

	}

	/**
	 * Display field settings in form editor
	 */
	public function displaySettings(){
		if(file_exists(WPDF()->get_plugin_dir() . 'templates/admin/fields/'.$this->getType().'.php')){
			require WPDF()->get_plugin_dir() . 'templates/admin/fields/'.$this->getType().'.php';
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
		}

		$data = wp_check_invalid_utf8( $data );
		$data = wp_kses_no_null( $data );
		return sanitize_text_field($data);
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

	public function getPlaceholder(){
		return $this->_placeholder;
	}

	public function getOptions(){
		return $this->_options;
	}

	public function getId(){
		return '';
	}

	public function getExtraClasses(){

		if(!empty($this->_extra_class) && is_string($this->_extra_class)){
			return $this->_extra_class;
		}
		return '';
	}

	public function getClasses(){
		return 'wpdf-field';
	}

	/**
	 * @return mixed|string
	 */
	public function getDefaultValue() {
		return $this->_default;
	}

	/**
	 * Format field data to store in fields array
	 *
	 * @param $field
	 *
	 * @return array
	 */
	public function save($field){

		$data = array(
			'type' => $field['type'],
			'label' => $field['label'],
			'placeholder' => isset( $field['placeholder'] ) ? $field['placeholder'] : '',
			'default' => isset( $field['default'] ) ? $field['default'] : '',
			'extra_class' => isset( $field['css_class'] ) ? $field['css_class'] : '',
		);

		$data['validation'] = array(
			array( 'type' => 'required' ),
			array( 'type' => 'email' )
		);

		$rules = array();
		$data['validation'] = array();
		if ( isset( $field['validation'] ) && !empty( $field['validation'] ) ) {
			foreach ( $field['validation'] as $rule ) {

				// skip if empty value
				if ( empty( $rule ) ) {
					continue;
				}

				$rule_arr = array(
					'type' => $rule['type'],
				);

				if( isset( $rule['msg'] ) && !empty( $rule['msg'] ) ){
					$rule_arr['msg'] = $rule['msg'];
				}

				$rules[] = $rule_arr;
			}
		}
		if( !empty( $rules ) ) {
			$data['validation'] = $rules;
		}

		return $data;
	}
}