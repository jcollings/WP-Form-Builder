<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 01/10/2016
 * Time: 21:15
 */

class WPDF_NotificationTest extends WP_UnitTestCase {

	public function testBasicNotification(){
		$field = new WPDF_FormField('name', 'text');
		$notification = new WPDF_Notification(array());
		$formData = new WPDF_FormData(array($field), array('name' => 'asd'), array());
		$this->assertTrue($notification->isValid($formData));
	}

	public function testBasicConditionalNotification(){
		$field = new WPDF_FormField('name', 'text');

		$notificationValid = new WPDF_Notification(array(
			'conditions' => array(
				'name' => 'asd'
			)
		));

		$formData = new WPDF_FormData(array('name' => $field), array($field->getInputName() => 'asd'), array());
		$this->assertTrue($notificationValid->isValid($formData));

		$formData = new WPDF_FormData(array('name' => $field), array($field->getInputName() => 'asda'), array());
		$this->assertFalse($notificationValid->isValid($formData));

		$formData = new WPDF_FormData(array('name' => $field), array(), array());
		$this->assertFalse($notificationValid->isValid($formData));
	}

	public function testComplexConditionalNotification(){
		$field = new WPDF_FormField('name', 'text');

		$notificationEq = new WPDF_Notification(array(
			'conditions' => array(
				'name' => array(
					'value' => 'asd',
					'operator' => '='
				)
			)
		));

		$formData = new WPDF_FormData(array('name' => $field), array($field->getInputName() => 'asd'), array());
		$this->assertTrue($notificationEq->isValid($formData));

		$formData = new WPDF_FormData(array('name' => $field), array($field->getInputName() => 'asda'), array());
		$this->assertFalse($notificationEq->isValid($formData));

		$formData = new WPDF_FormData(array('name' => $field), array(), array());
		$this->assertFalse($notificationEq->isValid($formData));

		$notificationNotEq = new WPDF_Notification(array(
			'conditions' => array(
				'name' => array(
					'value' => 'asd',
					'operator' => '!='
				)
			)
		));

		$formData = new WPDF_FormData(array('name' => $field), array($field->getInputName() => 'asd'), array());
		$this->assertFalse($notificationNotEq->isValid($formData));

		$formData = new WPDF_FormData(array('name' => $field), array($field->getInputName() => 'asda'), array());
		$this->assertTrue($notificationNotEq->isValid($formData));

		$formData = new WPDF_FormData(array('name' => $field), array(), array());
		$this->assertTrue($notificationNotEq->isValid($formData));
	}

}