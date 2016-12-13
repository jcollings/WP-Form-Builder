<?php
/**
 * Form Notification
 *
 * @package WPDF
 * @author James Collings
 * @created 06/08/2016
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class WPDF_Notification
 */
class WPDF_Notification {

	/**
	 * Notification recipient
	 *
	 * @var string
	 */
	protected $_to;

	/**
	 * Cc
	 *
	 * @var string
	 */
	protected $_cc;

	/**
	 * Bcc
	 *
	 * @var string
	 */
	protected $_bcc;

	/**
	 * Sender address
	 *
	 * @var string
	 */
	protected $_from;

	/**
	 * Subject
	 *
	 * @var string
	 */
	protected $_subject;

	/**
	 * Message
	 *
	 * @var string
	 */
	protected $_message;

	/**
	 * Notification conditions
	 *
	 * @var array
	 */
	protected $_conditions = array();

	/**
	 * WPDF_Notification constructor.
	 *
	 * @param array $notification Notification settings.
	 */
	public function __construct( $notification ) {

		if ( isset( $notification['to'] ) ) {
			$this->_to = $notification['to'];
		}

		if ( isset( $notification['cc'] ) ) {
			$this->_cc = $notification['cc'];
		}

		if ( isset( $notification['bcc'] ) ) {
			$this->_bcc = $notification['bcc'];
		}

		if ( isset( $notification['from'] ) ) {
			$this->_from = $notification['from'];
		}

		if ( isset( $notification['subject'] ) ) {
			$this->_subject = $notification['subject'];
		}

		if ( isset( $notification['message'] ) ) {
			$this->_message = $notification['message'];
		}

		if ( isset( $notification['conditions'] ) ) {
			$this->_conditions = $notification['conditions'];
		}
	}

	/**
	 * Get recipient
	 *
	 * @return mixed
	 */
	public function get_to() {
		return $this->_to;
	}

	/**
	 * Get Cc
	 *
	 * @return mixed
	 */
	public function get_cc() {
		return $this->_cc;
	}

	/**
	 * Get Bcc
	 *
	 * @return mixed
	 */
	public function get_bcc() {
		return $this->_bcc;
	}

	/**
	 * Get from
	 *
	 * @return mixed
	 */
	public function get_from() {
		return $this->_from;
	}

	/**
	 * Get subject
	 *
	 * @return mixed
	 */
	public function get_subject() {
		return $this->_subject;
	}

	/**
	 * Get message
	 *
	 * @return mixed
	 */
	public function get_message() {
		return $this->_message;
	}

	/**
	 * Check to see if conditions have been met
	 *
	 * @param WPDF_FormData $data Form data.
	 *
	 * @return bool
	 */
	public function is_valid( $data ) {

		if ( ! empty( $this->_conditions ) ) {

			// loop through conditions and escape if not valid.
			foreach ( $this->_conditions as $field => $value ) {

				if ( is_array( $value ) ) {

					$condition = isset( $value['operator'] ) ? $value['operator'] : '=';
					$val       = isset( $value['value'] ) ? $value['value'] : '';
					switch ( $condition ) {
						case '!=':
							if ( $data->get( $field ) === $val ) {
								return false;
							}
							break;
						case '=':
						default:
							if ( $data->get( $field ) !== $val ) {
								return false;
							}
							break;
					}
				} else {
					if ( $data->get( $field ) !== $value ) {
						return false;
					}
				}
			}
		}

		return true;
	}
}
