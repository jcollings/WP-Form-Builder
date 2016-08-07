<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 06/08/2016
 * Time: 12:31
 */
class WPDF_Notification{

	protected $_to;
	protected $_cc;
	protected $_bcc;
	protected $_from;

	protected $_subject;
	protected $_message;

	public function __construct($notification) {

		if(isset($notification['to'])){
			$this->_to = $notification['to'];
		}

		if(isset($notification['cc'])){
			$this->_cc = $notification['cc'];
		}

		if(isset($notification['bcc'])){
			$this->_bcc = $notification['bcc'];
		}

		if(isset($notification['from'])){
			$this->_from = $notification['from'];
		}

		if(isset($notification['subject'])){
			$this->_subject = $notification['subject'];
		}

		if(isset($notification['message'])){
			$this->_message = $notification['message'];
		}
	}

	/**
	 * @return mixed
	 */
	public function getTo() {
		return $this->_to;
	}

	/**
	 * @return mixed
	 */
	public function getCc() {
		return $this->_cc;
	}

	/**
	 * @return mixed
	 */
	public function getBcc() {
		return $this->_bcc;
	}

	/**
	 * @return mixed
	 */
	public function getFrom() {
		return $this->_from;
	}

	/**
	 * @return mixed
	 */
	public function getSubject() {
		return $this->_subject;
	}

	/**
	 * @return mixed
	 */
	public function getMessage() {
		return $this->_message;
	}
}