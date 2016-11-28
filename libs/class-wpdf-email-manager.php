<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 06/08/2016
 * Time: 12:35
 */

class WPDF_EmailManager{

	/**
	 * List of available tags
	 * @var array
	 */
	protected $_tags = array();

	/**
	 * @var WPDF_Notification[]
	 */
	protected $_notifications = array();

	protected $_template = array(
		'field_html' => "<p>{{field_name}}:<br />{{field_value}}</p>",
		'field_text' => "\n{{field_name}}:\n{{field_value}}"
	);

	protected $_email_type = 'html';

	public function __construct($notifications) {
		$this->_notifications = $notifications;
	}


	/**
	 * @param $data WPDF_FormData
	 *
	 * @return bool
	 */
	public function send($data){

		if(empty($this->_notifications)){
			// no notifications were needing to be sent
			return true;
		}

		/**
		 * List of template tags when displayed in message
		 */
		$template_tags = array();

		/**
		 * List of template tags when displayed in to,cc,bcc, subject
		 */
		$raw_template_tags = array();

		if($this->_email_type == 'html'){
			add_filter('wp_mail_content_type', array( $this, 'set_mail_content_type'));
		}

		$all = '';
		$fields = $data->toArray();
		foreach($fields as $field_id => $value){

			// dont show empty fields
			if($value === ""){
				continue;
			}

			$field = $data->getField($field_id);
			if($field->is_type('file')){

				if($this->_email_type == 'html'){
					$value = '<a href="' . wpdf_get_uploads_url() . $value . '">'.$value.'</a>';
				}else{
					$value = wpdf_get_uploads_url() . $value;
				}
			}

			$tag = $this->setup_merge_tag("field_" . $field_id);
			$raw_template_tags[$tag] = $value;
			$template_tags[$tag] = $this->parse_merge_tags( $this->_template['field_'.$this->_email_type], array(
				'{{field_name}}' => $field->get_label(),
				'{{field_value}}' => $value
			));

			if(!$field->is_type('password')){
				// dont add password fields to {{fields}} tag
				$all .= $tag;
			}
		}

		// add admin_email merge tag to both raw and html tags
		$raw_template_tags[$this->setup_merge_tag('admin_email')] = $template_tags[$this->setup_merge_tag('admin_email')] = get_option('admin_email');

		$template_tags[$this->setup_merge_tag('fields')] = $all;
		$template_tags = array_reverse($template_tags, true);
		$this->_tags = array_keys($template_tags);



		// loop through notifications, setup, and send
		foreach($this->_notifications as $notification){

			if(!$notification->isValid($data)){
				continue;
			}


			$headers = array();

			$to = $this->parse_merge_tags($notification->getTo(), $raw_template_tags);
			$cc = $this->parse_merge_tags($notification->getCc(), $raw_template_tags);
			if(!empty($cc)){
				$headers[] = 'Cc: ' . $cc;
			}

			$bcc = $this->parse_merge_tags($notification->getBcc(), $raw_template_tags);
			if(!empty($bcc)){
				$headers[] = 'Bcc: ' . $bcc;
			}

			$from = $this->parse_merge_tags($notification->getFrom(), $raw_template_tags);
			if(!empty($from)){
				$headers[] = 'From: ' . $from;
			}

			$subject = $this->parse_merge_tags($notification->getSubject(), $raw_template_tags);
			$message = $this->parse_merge_tags($notification->getMessage(), $template_tags);

			wp_mail($to, $subject, $message, $headers);
		}

		if($this->_email_type == 'html'){
			remove_filter('wp_mail_content_type', array( $this, 'set_mail_content_type'));
		}

		return true;

	}

	public function set_mail_content_type(){
		return 'text/html';
	}

	protected function setup_merge_tag($tag){
		return "{{" . $tag . "}}";
	}

	protected function parse_merge_tags($content, $tags){

		foreach($tags as $tag => $tag_content){

			$replacement = $tag_content;
			if(is_array($replacement)){
				$replacement = implode(",", $replacement);
			}

			$content = preg_replace( "/{$tag}/im", $replacement, $content );
		}

		return $content;
	}
}