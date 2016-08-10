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

	public function testTextField(){

		$field = new WPDF_FormField("fname", "text");

		// no value
		$data = new WPDF_FormData(array("fname" => $field), array(), array());
		$this->assertTrue($this->htmlHasAttributes($this->getFieldOutput($field, $data), 'input', array('type' => 'text', 'name' => $field->getInputName(), 'value' => '')));

		// basic value
		$data = new WPDF_FormData(array("fname" => $field), array($field->getInputName() => 'test value'), array());
		$this->assertTrue($this->htmlHasAttributes($this->getFieldOutput($field, $data), 'input', array('type' => 'text', 'name' => $field->getInputName(), 'value' => 'test value')));
	}
}
