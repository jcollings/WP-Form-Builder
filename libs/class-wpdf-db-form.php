<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 11/10/2016
 * Time: 18:53
 */

class WPDF_DB_Form extends WPDF_Form {

	public function __construct( $form_id = null ) {

		$form_id = intval($form_id);
		$post = get_post($form_id);
		if($post && $post->post_type == 'wpdf_form'){
			$this->ID = $form_id;
			$form = json_decode($post->post_content, true);
			parent::__construct("Form " . $form_id, $form['fields']);

			// load settings
			if(isset($form['settings']) && isset($form['settings']['labels']) && isset($form['settings']['labels']['submit'])){
				$this->settings($form['settings']);
			}

			// load confirmations
			if(isset($form['confirmations'])){

				foreach($form['confirmations'] as $confirmation){

					if($confirmation['type'] == 'message'){
						$this->add_confirmation('message', $confirmation['message']);
					}elseif($confirmation['type'] == 'redirect'){
						$this->add_confirmation('redirect', $confirmation['redirect_url']);
					}
				}
			}

			// load notifications
			if(isset($form['notifications']) && !empty($form['notifications'])){

				foreach($form['notifications'] as $notification){

					if(empty($notification['to'])){
						continue;
					}

					$args = array();
					if( isset($notification['from']) && !empty($notification['from']) ){
						$args['from'] = $notification['from'];
					}
					if( isset($notification['cc']) && !empty($notification['cc']) ){
						$args['cc'] = $notification['cc'];
					}
					if( isset($notification['bcc']) && !empty($notification['bcc']) ){
						$args['bcc'] = $notification['bcc'];
					}

					$this->add_notification($notification['to'], $notification['subject'], $notification['message'], $args);

				}

			}
		}
	}

	public function getDbId(){
		return $this->getId();
	}

	public function getName() {
		return 'WPDF_FORM_' . $this->ID;
	}

	public function export(){

		if(is_admin()) {
			$post = get_post( $this->ID );
			if ( $post && $post->post_type == 'wpdf_form' ) {
				return json_decode( $post->post_content, true );
			}
		}

		return false;

	}
}