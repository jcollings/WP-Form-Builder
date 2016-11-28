<?php

/**
 * Created by PhpStorm.
 * User: james
 * Date: 07/08/2016
 * Time: 09:32
 */
class WPDF_FormFieldTest extends WP_UnitTestCase {

	protected function htmlHasAttributes($string, $element, $attrs = array(), $exact = false){

		$parser = xml_parser_create();
		xml_parse_into_struct($parser, $string, $values);

		foreach($values as $row){

			// check if we are on the correct element
			if( strcasecmp($row['tag'], $element) === 0 && in_array($row['type'], array('complete', 'open'))){

				// to compare against to check valid
				$element_match = array();

				// check if attributes exist on element
				$attributes = isset( $row['attributes'] ) ? $row['attributes'] : false;

				// if exact match attribute count has to be the same
				if( ($attributes && count($attributes) == count($attrs)) || !$exact ){

					foreach($attrs as $attr_name => $attr_val){

						if(isset($attributes[strtoupper($attr_name)]) && $attributes[strtoupper($attr_name)] === $attr_val){
							// matches current attribute
							$element_match[$attr_name] = $attr_val;
						}
					}
				}

				if( count($attrs) == count($element_match) &&  empty(array_diff($attrs, $element_match))){
					return true;
				}
			}
		}

		return false;
	}

	protected function getFieldOutput(WPDF_FormField $field, WPDF_FormData $data){
		ob_start();
		$field->output($data);
		return ob_get_clean();
	}

	#region text field

	public function testTextField(){

		$form = new WPDF_Form("TestForm", array(
			'fname' => array(
				'type' => 'text'
			)
		));

		$field = $form->getField('fname');

		// no value
		$data = new WPDF_FormData($form, array('wpdf_action' => 'TestForm'), array());
		$this->assertTrue($this->htmlHasAttributes($this->getFieldOutput($field, $data), 'input', array( 'type' => 'text', 'name' => $field->get_input_name(), 'value' => '')));

		// basic value
		$data = new WPDF_FormData($form, array( $field->get_input_name() => 'test value', 'wpdf_action' => 'TestForm'), array());
		$this->assertTrue($this->htmlHasAttributes($this->getFieldOutput($field, $data), 'input', array( 'type' => 'text', 'name' => $field->get_input_name(), 'value' => 'test value')));
	}

	public function testTextFieldDefault(){

		$form = new WPDF_Form("TestForm", array(
			'fname' => array(
				'type' => 'text',
				'default' => 'asd'
			)
		));

		$field = $form->getField('fname');

		// default value displayed for non submitted form
		$data = new WPDF_FormData($form, array(), array());
		$this->assertTrue($this->htmlHasAttributes($this->getFieldOutput($field, $data), 'input', array( 'type' => 'text', 'name' => $field->get_input_name(), 'value' => 'asd')));

		// default value is not forced for submitted form without value
		$data = new WPDF_FormData($form, array('wpdf_action' => 'TestForm'), array());
		$this->assertTrue($this->htmlHasAttributes($this->getFieldOutput($field, $data), 'input', array( 'type' => 'text', 'name' => $field->get_input_name(), 'value' => '')));

		// default value is not forced for submitted form with value
		$data = new WPDF_FormData($form, array('wpdf_action' => 'TestForm', $field->get_input_name() => 'test-value'), array());
		$this->assertTrue($this->htmlHasAttributes($this->getFieldOutput($field, $data), 'input', array( 'type' => 'text', 'name' => $field->get_input_name(), 'value' => 'test-value')));
	}

	#endregion
}
