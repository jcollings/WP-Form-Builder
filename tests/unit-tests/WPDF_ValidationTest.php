<?php

/**
 * Created by PhpStorm.
 * User: james
 * Date: 04-Aug-16
 * Time: 8:18 PM
 */
class WPDF_ValidationTest extends WP_UnitTestCase {

	public function testRequiredValidation(){

		$validation = new WPDF_Validation([
			'fname' => [
				[
					'type' => 'required',
					'msg' => 'This field is required'
				]
			]
		]);

		$field = new WPDF_FormField('fname', ['type' => 'required']);

		$this->assertFalse($validation->validate($field, []));
		$this->assertFalse($validation->validate($field, ['fname' => '']));
		$this->assertTrue($validation->validate($field, ['fname' => 'asd']));
	}

	public function testEmailValidation(){
		$validation = new WPDF_Validation([
			'email' => [
				[
					'type' => 'email',
					'msg' => 'Please enter a valid email'
				]
			]
		]);

		$field = new WPDF_FormField('email', ['type' => 'email']);

		$this->assertTrue($validation->validate($field, []));
		$this->assertTrue($validation->validate($field, ['email' => '']));
		$this->assertFalse($validation->validate($field, ['email' => 'cake']));
		$this->assertFalse($validation->validate($field, ['email' => 'cake@cake']));
		$this->assertTrue($validation->validate($field, ['email' => 'cake@cake.com']));
	}

	public function testRequiredEmailValidation(){
		$validation = new WPDF_Validation([
			'email' => [
				[
					'type' => 'email',
					'msg' => 'Please enter a valid email'
				],
				[
					'type' => 'required',
					'msg' => 'This field is required'
				]
			]
		]);

		$field = new WPDF_FormField('email', ['type' => 'text']);

		$this->assertFalse($validation->validate($field, []));
		$this->assertFalse($validation->validate($field, ['email' => '']));
		$this->assertFalse($validation->validate($field, ['email' => 'cake']));
		$this->assertFalse($validation->validate($field, ['email' => 'cake@cake']));
		$this->assertTrue($validation->validate($field, ['email' => 'cake@cake.com']));
	}

	public function testMinLengthValidation(){
		$validation = new WPDF_Validation([
			'fname' => [
				[
					'type' => ['min_length', 5],
				]
			]
		]);

		$field = new WPDF_FormField('fname', ['type' => 'text']);

		$this->assertTrue($validation->validate($field, []));
		$this->assertTrue($validation->validate($field, ['fname' => '']));
		$this->assertFalse($validation->validate($field, ['fname' => 'a']));
		$this->assertFalse($validation->validate($field, ['fname' => 'aaaa']));
		$this->assertTrue($validation->validate($field, ['fname' => 'aaaaa']));
	}

	public function testMaxLengthValidation(){
		$validation = new WPDF_Validation([
			'fname' => [
				[
					'type' => ['max_length', 4],
				]
			]
		]);

		$field = new WPDF_FormField('fname', ['type' => 'text']);

		$this->assertTrue($validation->validate($field, []));
		$this->assertTrue($validation->validate($field, ['fname' => '']));
		$this->assertTrue($validation->validate($field, ['fname' => 'a']));
		$this->assertTrue($validation->validate($field, ['fname' => 'aaaa']));
		$this->assertFalse($validation->validate($field, ['fname' => 'aaaaa']));
	}

	public function testMinMaxLengthValidation(){
		$validation = new WPDF_Validation([
			'fname' => [
				[
					'type' => ['max_length', 4],
				],
				[
					'type' => ['min_length', 2],
				]
			]
		]);

		$field = new WPDF_FormField('fname', ['type' => 'text']);

		$this->assertTrue($validation->validate($field, []));
		$this->assertTrue($validation->validate($field, ['fname' => '']));
		$this->assertFalse($validation->validate($field, ['fname' => 'a']));
		$this->assertTrue($validation->validate($field, ['fname' => 'aa']));
		$this->assertTrue($validation->validate($field, ['fname' => 'aaa']));
		$this->assertTrue($validation->validate($field, ['fname' => 'aaaa']));
		$this->assertFalse($validation->validate($field, ['fname' => 'aaaaa']));
	}

	public function testRequiredMinMaxLengthValidation(){
		$validation = new WPDF_Validation([
			'fname' => [
				[
					'type' => 'required'
				],
				[
					'type' => ['max_length', 4]
				],
				[
					'type' => ['min_length', 2]
				]
			]
		]);

		$field = new WPDF_FormField('fname', ['type' => 'text']);

		$this->assertFalse($validation->validate($field, []));
		$this->assertFalse($validation->validate($field, ['fname' => '']));
		$this->assertFalse($validation->validate($field, ['fname' => 'a']));
		$this->assertTrue($validation->validate($field, ['fname' => 'aa']));
		$this->assertTrue($validation->validate($field, ['fname' => 'aaa']));
		$this->assertTrue($validation->validate($field, ['fname' => 'aaaa']));
		$this->assertFalse($validation->validate($field, ['fname' => 'aaaaa']));
	}

}
