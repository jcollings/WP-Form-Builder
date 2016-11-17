<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 04-Aug-16
 * Time: 7:23 PM
 */

function wpdf_get_uploads_dir(){

	$upload_dir = WP_CONTENT_DIR . '/uploads/wpdf/';
	if(!file_exists($upload_dir)){
		mkdir($upload_dir);
	}

	return $upload_dir;
}

function wpdf_get_uploads_url(){
	return content_url('/uploads/wpdf/');
}

function wpdf_is_submitted_form($form_id){
	$form = WPDF()->get_current_form();
	if( $form != false && $form->getName() == $form_id){
		return true;
	}
	return false;
}

#region Setup Forms

function wpdf_register_form($name, $args = array()){
	return WPDF()->register_form($name, $args);
}

#endregion

#region Display Forms

/**
 * Get form
 *
 * @param $name
 *
 * @return WPDF_Form
 */
function wpdf_get_form($name){
	return WPDF()->get_form($name);
}

/**
 * Display default output of form
 * @param $name
 */
function wpdf_display_form($name){

	$form = wpdf_get_form($name);

	$form->start();

	if($form->is_complete()) {

		// display successful message
		wpdf_display_confirmation($form);

	}else{

		// display form errors if any
		if($form->has_errors()){
			$form->errors();
		}

		$fields = $form->getFields();
		foreach($fields as $field_id => $field){
			wpdf_display_field($form, $field_id);
		}

		$form->submit();
	}

	$form->end();
}

/**
 * @param $form WPDF_Form
 * @param $field_id string
 */
function wpdf_display_field($form, $field_id){

	if(!$form->hasValidToken()){
		return;
	}

	?>
	<div class="wpdf-form-row <?php $form->classes($field_id, 'validation'); ?> <?php $form->classes($field_id, 'type'); ?>">
		<div class="wpdf-label"><?php $form->label( $field_id ); ?></div>
		<div class="wpdf-input"><?php $form->input( $field_id ); ?></div>
		<?php $form->error( $field_id ); ?>
	</div>
	<?php
}

/**
 * @param $form WPDF_Form
 */
function wpdf_display_confirmation($form){
	?>
	<div class="wpdf-form-confirmation">
		<p><?php echo $form->getConfirmationMessage(); ?></p>
	</div>
	<?php
}

#endregion

function wpdf_getIp(){
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


