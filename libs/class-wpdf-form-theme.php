<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 22/11/2016
 * Time: 10:22
 */
class WPDF_FormTheme{

	protected $_defaults = array(
		'form_bg_colour' => '',
		'form_text_colour' => '',
		'form_bg_error_colour' => '#c63838',
		'form_text_error_colour' => '#ffffff',
		'form_bg_success_colour' => '#80cd5f',
		'form_text_success_colour' => '#ffffff',
		'field_label_bg_colour' => '',
		'field_label_text_colour' => '',
		'field_input_bg_colour' => '',
		'field_input_text_colour' => '',
		'field_error_text_colour' => '#c63838',
		'field_border_colour' => '',
		'field_error_border_colour' => '#c63838',
		'button_bg_colour' => '#000000',
		'button_text_colour' => '#ffffff',
		'button_hover_bg_colour' => '#333333',
		'button_hover_text_colour' => '#ffffff',
		'checkbox_text_colour' => ''
	);

	protected $_disabled = array();
	protected $_styles = array();

	public function __construct($data = array(), $disabled = array()) {

		foreach($this->_defaults as $key => $val){
			$this->_styles[$key] = isset($data[$key]) ? $data[$key] : $this->_defaults[$key];
			$this->_disabled[$key] = in_array($key, $disabled) ? true : false;
		}
	}

	public function getStyle($key, $force = false) {
		return isset($this->_styles[$key]) && (!$this->isStyleDisabled($key) || $force) ? $this->_styles[$key] : false;
	}

	public function hasStyle($key){
		return $this->getStyle($key);
	}

	public function isStyleDisabled($key){
		return isset($this->_disabled[$key]) && $this->_disabled[$key] == true ? true : false;
	}
}