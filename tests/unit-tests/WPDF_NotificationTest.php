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
		$this->assertTrue($notification->isValid($formData));
	}

	public function testBasicConditionalNotification(){

		$form = new WPDF_Form("TestForm", array(
			'name' => array(
				'type' => 'text'
			)
		));
		$field = $form->getField('name');

		$notificationValid = new WPDF_Notification(array(
			'conditions' => array(
				'name' => 'asd'
			)
		));

		$formData = new WPDF_FormData($form, array($field->getInputName() => 'asd', 'wpdf_action' => 'TestForm'), array());
		$this->assertTrue($notificationValid->isValid($formData));

		$formData = new WPDF_FormData($form, array($field->getInputName() => 'asda', 'wpdf_action' => 'TestForm'), array());
		$this->assertFalse($notificationValid->isValid($formData));

		$formData = new WPDF_FormData($form, array('wpdf_action' => 'TestForm'), array());
		$this->assertFalse($notificationValid->isValid($formData));
	}

	public function testComplexConditionalNotification(){

		$form = new WPDF_Form("TestForm", array(
			'name' => array(
				'type' => 'text'
			)
		));
		$field = $form->getField('name');

		$notificationEq = new WPDF_Notification(array(
			'conditions' => array(
				'name' => array(
					'value' => 'asd',
					'operator' => '='
				)
			)
		));

		$formData = new WPDF_FormData($form, array($field->getInputName() => 'asd', 'wpdf_action' => 'TestForm'), array());
		$this->assertTrue($notificationEq->isValid($formData));

		$formData = new WPDF_FormData($form, array($field->getInputName() => 'asda', 'wpdf_action' => 'TestForm'), array());
		$this->assertFalse($notificationEq->isValid($formData));

		$formData = new WPDF_FormData($form, array('wpdf_action' => 'TestForm'), array());
		$this->assertFalse($notificationEq->isValid($formData));

		$notificationNotEq = new WPDF_Notification(array(
			'conditions' => array(
				'name' => array(
					'value' => 'asd',
					'operator' => '!='
				)
			)
		));

		$formData = new WPDF_FormData($form, array($field->getInputName() => 'asd', 'wpdf_action' => 'TestForm'), array());
		$this->assertFalse($notificationNotEq->isValid($formData));

		$formData = new WPDF_FormData($form, array($field->getInputName() => 'asda', 'wpdf_action' => 'TestForm'), array());
		$this->assertTrue($notificationNotEq->isValid($formData));

		$formData = new WPDF_FormData($form, array('wpdf_action' => 'TestForm'), array());
		$this->assertTrue($notificationNotEq->isValid($formData));
	}

}