<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 11/10/2016
 * Time: 18:53
 */

class WPDF_DB_Form extends WPDF_Form {

	protected $ID = null;

	public function __construct( $form_id = null ) {

		$form_id = intval($form_id);
		$post = get_post($form_id);
		if($post && $post->post_type == 'wpdf_form'){
			$this->ID = $form_id;
			$form = json_decode($post->post_content, true);
			parent::__construct("Form " . $form_id, $form['fields']);
		}
	}

	public function getDbId(){
		return $this->ID;
	}
}