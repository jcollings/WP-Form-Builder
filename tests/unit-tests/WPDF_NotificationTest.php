<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 01/10/2016
 * Time: 21:15
 */

class WPDF_NotificationTest extends WP_UnitTestCase {

	public function testBasicNotification(){

		$form = new WPDF_Form("TestForm", array(
			'name' => array(
				'type' => 'text'
			)
		));

		$notification = new WPDF_Notification(array());
		$formData = new WPDF_FormData($form, array('name' => 'asd', 'wpdf_action' => 'TestForm'), array());
		$this->assertTrue($notification->is_valid($formData));
	}

	public function testBasicConditionalNotification(){

		$form = new WPDF_Form("TestForm", array(
			'name' => array(
				'type' => 'text'
			)
		));
		$field = $form->get_field('name');

		$notificationValid = new WPDF_Notification(array(
			'conditions' => array(
				'name' => 'asd'
			)
		));

		$formData = new WPDF_FormData($form, array( $field->get_input_name() => 'asd', 'wpdf_action' => 'TestForm'), array());
		$this->assertTrue($notificationValid->is_valid($formData));

		$formData = new WPDF_FormData($form, array( $field->get_input_name() => 'asda', 'wpdf_action' => 'TestForm'), array());
		$this->assertFalse($notificationValid->is_valid($formData));

		$formData = new WPDF_FormData($form, array('wpdf_action' => 'TestForm'), array());
		$this->assertFalse($notificationValid->is_valid($formData));
	}

	public function testComplexConditionalNotification(){

		$form = new WPDF_Form("TestForm", array(
			'name' => array(
				'type' => 'text'
			)
		));
		$field = $form->get_field('name');

		$notificationEq = new WPDF_Notification(array(
			'conditions' => array(
				'name' => array(
					'value' => 'asd',
					'operator' => '='
				)
			)
		));

		$formData = new WPDF_FormData($form, array( $field->get_input_name() => 'asd', 'wpdf_action' => 'TestForm'), array());
		$this->assertTrue($notificationEq->is_valid($formData));

		$formData = new WPDF_FormData($form, array( $field->get_input_name() => 'asda', 'wpdf_action' => 'TestForm'), array());
		$this->assertFalse($notificationEq->is_valid($formData));

		$formData = new WPDF_FormData($form, array('wpdf_action' => 'TestForm'), array());
		$this->assertFalse($notificationEq->is_valid($formData));

		$notificationNotEq = new WPDF_Notification(array(
			'conditions' => array(
				'name' => array(
					'value' => 'asd',
					'operator' => '!='
				)
			)
		));

		$formData = new WPDF_FormData($form, array( $field->get_input_name() => 'asd', 'wpdf_action' => 'TestForm'), array());
		$this->assertFalse($notificationNotEq->is_valid($formData));

		$formData = new WPDF_FormData($form, array( $field->get_input_name() => 'asda', 'wpdf_action' => 'TestForm'), array());
		$this->assertTrue($notificationNotEq->is_valid($formData));

		$formData = new WPDF_FormData($form, array('wpdf_action' => 'TestForm'), array());
		$this->assertTrue($notificationNotEq->is_valid($formData));
	}

}