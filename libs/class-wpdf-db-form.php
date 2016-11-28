<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 11/10/2016
 * Time: 18:53
 */

class WPDF_DB_Form extends WPDF_Form {

	/**
	 * Form Label
	 * @var string
	 */
	private $_label = null;


	/**
	 * Form Theme Styles
	 * @var WPDF_FormTheme
	 */
	protected $_theme = null;

	public function __construct( $form_id = null ) {

		$form_id = intval($form_id);
		$post = get_post($form_id);
		if($post && $post->post_type == 'wpdf_form'){
			$this->ID = $form_id;
			$form = is_serialized($post->post_content) ? unserialize($post->post_content) : array();

			$fields = isset($form['fields']) && !empty($form['fields']) ? $form['fields'] : array();
			parent::__construct("Form " . $form_id, $fields);

			// load settings
			if(isset($form['settings'])){
				$this->settings($form['settings']);
			}

			// load style
			$style = isset($form['theme']) ? $form['theme'] : array();
			$style_disabled = isset($form['theme_disabled']) ? $form['theme_disabled'] : array();
			$this->_theme = new WPDF_FormTheme($style, $style_disabled);

			// load form content
			$this->_content = isset($form['content']) ? $form['content'] : '';
			$this->_confirmation_location = isset($form['confirmation_location']) ? $form['confirmation_location'] : 'after';

			if(isset($form['form_label'])){
				$this->_label = $form['form_label'];
			}else{
				$this->_label = sprintf('WPDF_FORM_%d', $this->ID);
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
		return $this->get_id();
	}

	public function get_name() {
		return 'WPDF_FORM_' . $this->ID;
	}

	public function get_label(){
		return $this->_label;
	}

	/**
	 * Get Form Styling
	 *
	 * @param $key
	 *
	 * @return bool|string
	 */
	public function getStyle($key, $force = false) {
		return $this->_theme->getStyle($key, $force);
	}

	public function hasStyle($key){
		return $this->_theme->getStyle($key);
	}

	public function isStyleDisabled($key){
		return $this->_theme->isStyleDisabled($key);
	}

	public function export(){

		if(is_admin()) {
			$post = get_post( $this->ID );
			if ( $post && $post->post_type == 'wpdf_form' ) {
				return maybe_unserialize($post->post_content);
			}
		}

		return false;

	}
}