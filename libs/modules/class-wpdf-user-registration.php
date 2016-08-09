<?php
/**
 * WordPress user registration module
 *
 * Created by PhpStorm.
 * User: james
 * Date: 09/08/2016
 * Time: 23:13
 */

class WPDF_UserRegistration{

	public function __construct() {
		add_filter( 'wpdf/process_form', array($this, 'process_form'), 10, 3 );
	}

	/**
	 * Register wp user based on form configuration
	 *
	 * @param $result bool
	 * @param $form WPDF_Form
	 * @param $data WPDF_FormData
	 *
	 * @return bool
	 */
	function process_form($result, $form, $data){

		$settings = $form->get_setting('user_registration');
		$fields = $settings['fields'];

		// no user login field present
		if(!isset($fields['user_login'])){
			return false;
		}

		$user_arr = array();

		foreach($fields as $key => $field_id){

			// escape if field doesn't exist in form
			if($form->getField($field_id) == false){
				return false;
			}

			$user_arr[$key] = $data->get($field_id);
		}

		// if result is true, then register the user
		$user = wp_insert_user( $user_arr );
		if ( is_wp_error( $user ) ) {

			switch($user->get_error_code()){

				case 'existing_user_login':
					$form->add_field_error( $fields['user_login'], $user->get_error_message() );
					break;

				case 'existing_user_email':
					$form->add_field_error( $fields['user_email'], $user->get_error_message() );
					break;

				default:
					$form->add_error($user->get_error_message());
					break;

			}

			return false;
		}

		return $result;
	}

}

// some way to register modules
function wpdf_register_userreg($modules = array()){

	$modules['user_registration'] = 'WPDF_UserRegistration';

	return $modules;
}
add_filter('wpdf/list_modules', 'wpdf_register_userreg');