<?php
/**
 * User Registration Module
 *
 * @package WPDF/Pro
 * @author James Collings
 * @created 10/08/2016
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class WPDF_UserRegistration
 */
class WPDF_UserRegistration {

	/**
	 * Registered User ID
	 *
	 * @var int
	 */
	private $_user = false;

	/**
	 * WPDF_UserRegistration constructor.
	 */
	public function __construct() {
		add_filter( 'wpdf/process_form', array( $this, 'process_form' ), 10, 3 );
		add_filter( 'wpdf/save_virtual_fields', array( $this, 'save_virtual_fields' ), 10, 1 );
	}

	/**
	 * Register wp user based on form configuration
	 *
	 * @param bool          $result Form status.
	 * @param WPDF_Form     $form Submitted form.
	 * @param WPDF_FormData $data Submitted Form data.
	 *
	 * @return bool
	 */
	function process_form( $result, $form, $data ) {

		$settings = $form->get_setting( 'user_registration' );
		$fields   = $settings['fields'];

		// no user login field present.
		if ( ! isset( $fields['user_login'] ) ) {
			return false;
		}

		$user_arr = array();

		foreach ( $fields as $key => $field_id ) {

			// escape if field doesn't exist in form.
			if ( false === $form->get_field( $field_id ) ) {
				return false;
			}

			$user_arr[ $key ] = $data->get( $field_id );
		}

		if ( ! isset( $user_arr['user_pass'] ) ) {
			$user_arr['user_pass'] = null;
		}

		// if result is true, then register the user.
		$this->_user = wp_insert_user( $user_arr );
		if ( is_wp_error( $this->_user ) ) {

			switch ( $this->_user->get_error_code() ) {

				case 'existing_user_login':
					$form->add_field_error( $fields['user_login'], $this->_user->get_error_message() );
					break;

				case 'existing_user_email':
					$form->add_field_error( $fields['user_email'], $this->_user->get_error_message() );
					break;

				default:
					$form->add_error( $this->_user->get_error_message() );
					break;

			}

			return false;
		}

		return $result;
	}

	/**
	 * Save custom fields to database
	 *
	 * @param array $fields Form fields.
	 *
	 * @return mixed
	 */
	function save_virtual_fields( $fields ) {

		if ( intval( $this->_user ) > 0 ) {
			$fields['user_id'] = $this->_user;
		}

		return $fields;
	}
}

/**
 * Change display of submission entry
 *
 * Display link to registered user, instead of just the user id.
 *
 * @param string $content submitted data for field.
 * @param string $field_id field id.
 *
 * @return string
 */
function display_submission_field( $content, $field_id ) {

	switch ( $field_id ) {
		case 'user_id':
			$user = get_user_by( 'id', $content );
			if ( $user ) {
				$content = sprintf( 'Registered user: <a href="' . admin_url( 'user-edit.php?user_id=' ) . '%d">%s</a>', $content, $user->data->user_login );
			}
			break;
	}

	return $content;
}

add_filter( 'wpdf/display_submission_field', 'display_submission_field', 10, 2 );

/**
 * Register user registration module
 *
 * @param array $modules list of registered modules.
 *
 * @return array
 */
function wpdf_register_userreg( $modules = array() ) {

	$modules['user_registration'] = 'WPDF_UserRegistration';

	return $modules;
}

add_filter( 'wpdf/list_modules', 'wpdf_register_userreg' );
