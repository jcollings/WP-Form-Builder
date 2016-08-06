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

#region Setup Forms

function wpdf_register_form($name, $args = array()){

	WPDF()->register_form($name, $args);
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

#endregion


