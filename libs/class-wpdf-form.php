<?php

class WPDF_Form{

	protected $_templates = null;
	protected $_messages = null;
	protected $_data = null;
	protected $_name = null;

	// field errors
	protected $_errors = null;

	// form error
	protected $_error = false;

	protected $_validation = null;
	protected $_rules = null;
	protected $_submitted = false;

	/**
	 * What happens after the form has been submitted, message or redirect
	 * @var array
	 */
	protected $_confirmation = [
		'type' => 'message',
		'message' => 'Form has been successfully submitted!'
	];

	protected $_has_file_field = false;

	/**
	 * @var WPDF_Notification[]
	 */
	protected $_notifications = null;

	/**
	 * @var WPDF_EmailManager
	 */
	protected $_email_manager = null;

	/**
	 * @var WPDF_FormField[]
	 */
	protected $_fields = null;

	public function __construct($name, $fields = array()) {

		$this->_name = $name;

		$this->_fields = array();
		$this->_errors = array();
		$this->_rules = array();
		$this->_notifications = array();

		// setup fields
		if(!empty($fields)) {
			foreach ( $fields as $field_id => $field ) {

				$type                       = $field['type'];
				$this->_fields[ $field_id ] = new WPDF_FormField( $field_id, $type, $field );

				if ( $type == 'file' ) {
					$this->_has_file_field = true;
				}

				// add validation rules to rules array
				if ( isset( $field['validation'] ) && ! empty( $field['validation'] )  ) {

					// validation should end up like [ [ 'type' => 'required'], ['type' => 'email'] ]
					if(is_array($field['validation'])){

						if(isset($field['validation']['type'])){
							// is in the format [ 'type' => 'required' ]
							$rule = array(
								$field['validation']
							);
						}else{
							// has been given in the same format as needed
							$rule = $field['validation'];
						}


					}else{
						// if only validation type string was give, convert to full
						$rule = array(
							array(
								'type' => $field['validation']
							)
						);
					}

					// todo: check to see if this is a valid rule
					$this->_rules[ $field_id ] = $rule;
				}
			}
		}

		$this->_data = new WPDF_FormData($this->_fields, $_POST, $_FILES);
	}

	public function process(){

		if( ! wp_verify_nonce( $_POST['wpdf_nonce'], 'wpdf_submit_form_' . $this->_name )){
			$this->_error = "An Error occurred when submitting the form, please retry.";
//			return;
		}

		if( intval($_SERVER['CONTENT_LENGTH'])>0 && count($_POST)===0 ){
			$this->_error = "An Error occurred: PHP discarded POST data because of request exceeding post_max_size.";
		}

		// clear data array
		$this->_submitted = true;
		$this->_validation = new WPDF_Validation($this->_rules);

		foreach($this->_fields as $field_id => $field){

			if($field->isType("file")){

				// check for file upload errors, before checking against field validation rules
				if(isset($_FILES[$field_id])){
					$file_data = $_FILES[$field_id];
					if($file_data['error'] !== UPLOAD_ERR_OK && $file_data['error'] !== UPLOAD_ERR_NO_FILE){
						$this->_errors[$field_id] = $this->_validation->get_upload_error($file_data['error']);
					}
				}
			}

			// todo: no need to pass all data to the validator each time
			if(!isset($this->_errors[$field_id]) && !$this->_validation->validate($field, $this->_data->toArray())){
				$this->_errors[$field_id] = $this->_validation->get_error();
			}
		}

		if(!$this->has_errors()){

			// store form data in database
			$db = new WPDF_DatabaseManager();
			$db->save_entry($this->_name, $this->_data);

			// send email notifications with template tags
			$this->_email_manager = new WPDF_EmailManager($this->_notifications);
			$this->_email_manager->send($this->_data);

			// redirect now if needed
			if($this->_confirmation['type'] == "redirect"){
				wp_redirect($this->_confirmation['redirect_url']);
				exit;
			}
		}
	}

	public function add_confirmation($type, $value){

		if($type == 'redirect'){

			$this->_confirmation = array(
				'type' => 'redirect',
				'redirect_url' => $value
			);

			return true;

		}elseif($type == 'message'){

			$this->_confirmation = array(
				'type' => 'message',
				'message' => $value
			);

			return true;
		}

		return false;
	}

	public function add_notification($to, $subject, $message, $args = array()){

		$notification = array(
			'to' => $to,
			'subject' => $subject,
			'message' => $message,
		);

		if(isset($args['cc'])){
			$notification['cc'] = $args['cc'];
		}

		if(isset($args['bcc'])){
			$notification['bcc'] = $args['bcc'];
		}

		if(isset($args['from'])){
			$notification['from'] = $args['from'];
		}

		$this->_notifications[] = new WPDF_Notification( $notification );

	}

	#region Form Output

	/**
	 * Display form opening tag
	 *
	 * @param $args array
	 */
	public function start($args = array()){

		// if is file upload form need to add

		$attrs = ' method="post" action="?wpdf_action='.$this->_name.'"';
		if($this->_has_file_field){
			$attrs .= ' enctype="multipart/form-data"';
		}

		echo "<form {$attrs}>";
	}

	/**
	 * Display form field
	 *
	 * @param $name
	 */
	public function input($name){

		$field = isset($this->_fields[$name]) ? $this->_fields[$name] : false;
		if($field){
			$field->output($this->_data);
		}
	}

	public function label($name){
		echo '<label for="'.$name.'" >'.$this->_fields[$name]->getLabel().'</label>';
	}

	public function classes($field_id, $type){

		switch($type){
			case 'validation':

				if(isset($this->_errors[$field_id])){
					echo 'has-error';
				}
				break;
			case 'type':
				echo sprintf('input-%s', $this->_fields[$field_id]->getType());
				break;
		}

	}

	public function error($field_id){
		if(isset($this->_errors[$field_id])){
			echo $this->_errors[$field_id];
		}
	}

	/**
	 * Display submit button for form
	 *
	 * @param $label
	 * @param array $args
	 */
	public function submit($label, $args = array()){

	}

	/**
	 * Display form closing tag
	 */
	public function end($submit_label = 'Send'){

		$nonce = wp_create_nonce( 'wpdf_submit_form_' . $this->_name );

		// hidden fields
		echo '<input type="hidden" name="wpdf_action" value="' . $this->_name .'" />';
		echo '<input type="hidden" name="wpdf_nonce" value="'.$nonce.'" />';

		// submit
		echo '<input type="submit" value="'.$submit_label.'" />';

		echo '</form>';
	}

	#endregion

	#region Form Errors

	/**
	 * Check to see if form has errors
	 * @return bool
	 */
	public function has_errors(){

		if(!empty($this->_errors) || $this->_error !== false){
			return true;
		}

		return false;
	}

	/**
	 * Display form error message
	 */
	public function errors(){

		if($this->_error){
			echo "<p>".$this->_error."</p>";
			return;
		}

		echo "<p>Please make sure you have corrected any errors below before resubmit the form.</p>";
		echo '<ul>';
		foreach($this->_errors as $field_id => $error){
			echo '<li>' . $this->_fields[$field_id]->getLabel() . ' - ' . $error . '</li>';
		}
		echo '</ul>';
	}

	#endregion

	#region Complete Form

	/**
	 * Check to see if form is complete
	 * @return bool
	 */
	public function is_complete(){

		// no data has been submitted
		if($this->_submitted === true && !$this->has_errors()){
			$this->getConfirmationMessage();
			return true;
		}
		return false;
	}

	/**
	 * Output form complete/confirmation/thank you message
	 */
	public function getConfirmationMessage(){

		if($this->_confirmation['type'] == "message"){
			return $this->_confirmation['message'];
		}

		return "DEFAULT: Form submitted successfully";
	}

	#endregion

	/**
	 * @return string
	 */
	public function getName() {
		return $this->_name;
	}

	public function getFieldLabel($field){

		return isset($this->_fields[$field]) ? $this->_fields[$field]->getLabel() : $field;
	}

	/**
	 * @return WPDF_FormField[]
	 */
	public function getFields() {
		return $this->_fields;
	}

	/**
	 * @param $field string
	 *
	 * @return bool|WPDF_FormField
	 */
	public function getField($field){
		return isset($this->_fields[$field]) ? $this->_fields[$field] : false;
	}
}