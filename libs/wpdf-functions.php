<?php
/**
 * Form Functions
 *
 * @package WPDF
 * @author James Collings
 * @created 04/08/2016
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Get uploads directory path
 *
 * @return string
 */
function wpdf_get_uploads_dir() {

	$upload_dir = WP_CONTENT_DIR . '/uploads/';
	if ( ! file_exists( $upload_dir ) ) {
		mkdir( $upload_dir );
	}

	$upload_dir = WP_CONTENT_DIR . '/uploads/wpdf/';
	if ( ! file_exists( $upload_dir ) ) {
		mkdir( $upload_dir );
	}

	return $upload_dir;
}

/**
 * Get uploads directory url
 *
 * @return string
 */
function wpdf_get_uploads_url() {
	return content_url( '/uploads/wpdf/' );
}

/**
 * Check if form is submitted form.
 *
 * @param string $form_id Form id.
 *
 * @return bool
 */
function wpdf_is_submitted_form( $form_id ) {
	$form = WPDF()->get_current_form();
	if ( false !== $form && $form_id === $form->get_name() ) {
		return true;
	}
	return false;
}

/**
 * Register form
 *
 * @param string $name Form name.
 * @param array  $args Form arguments.
 *
 * @return WPDF_Form
 */
function wpdf_register_form( $name, $args = array() ) {
	return WPDF()->register_form( $name, $args );
}

/**
 * Get form
 *
 * @param string $name Form name.
 *
 * @return WPDF_Form
 */
function wpdf_get_form( $name ) {
	return WPDF()->get_form( $name );
}

/**
 * Display default output of form
 *
 * @param string $name Form name.
 */
function wpdf_display_form( $name ) {

	$form = wpdf_get_form( $name );

	$form->start();

	if ( $form->is_complete() ) {

		// display successful message.
		wpdf_display_confirmation( $form );

	} else {

		// display form errors if any.
		if ( $form->has_errors() ) {
			$form->errors();
		}

		$fields = $form->get_fields();
		foreach ( $fields as $field_id => $field ) {
			wpdf_display_field( $form, $field_id );
		}

		$form->submit();
	}

	$form->end();
}

/**
 * Display field
 *
 * @param WPDF_Form $form Form object.
 * @param string    $field_id Form field.
 */
function wpdf_display_field( $form, $field_id ) {

	if ( ! $form->has_valid_token() ) {
		return;
	}

	$id = $form->get_field( $field_id )->get_input_name() . '_' . $form->get_id();
	?>
	<div id="<?php echo esc_attr( $id ); ?>" class="wpdf-form-row <?php $form->classes( $field_id, 'validation' ); ?> <?php $form->classes( $field_id, 'type' ); ?>">
		<div class="wpdf-label"><?php $form->label( $field_id ); ?></div>
		<div class="wpdf-input"><?php $form->input( $field_id ); ?></div>
		<div class="ajax_field_error"><?php $form->error( $field_id ); ?></div>
	</div>
	<?php
}

/**
 * Display form confirmation
 *
 * @param WPDF_Form $form Form object.
 */
function wpdf_display_confirmation( $form ) {
	?>
	<div class="wpdf-form-confirmation">
		<p><?php echo esc_html( $form->get_confirmation_message() ); ?></p>
	</div>
	<?php
}

/**
 * Get user ip
 *
 * @return string
 */
function wpdf_get_ip() {
	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		//check ip from share internet
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		//to check ip is pass from proxy
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}

	return $ip;
}

/**
 * Override display of file
 *
 * @param string $value
 * @param string $field_id
 * @param WPDF_FormData $data
 *
 * @return mixed
 */
function wpdf_file_field_value($value, $field_id, $data){

	$field = $data->get_field($field_id);
	if ( $field->is_type( 'file' ) ) {
		$link = trailingslashit( $data->get_upload_folder() ) . $value;
		$value = wpdf_get_uploads_url() . $link;
	}

	return $value;
}
add_filter( 'wpdf/display_field_value', 'wpdf_file_field_value', 10, 3 );