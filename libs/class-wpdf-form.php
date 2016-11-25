<?php

class WPDF_Form{

	protected $_templates = null;
	protected $_messages = null;

	/**
	 * @var WPDF_FormData
	 */
	protected $_data = null;

	protected $ID = null;

	protected $_name = null;
	protected $_content = null;

	// field errors
	protected $_errors = null;

	// form error
	protected $_error = false;

	protected $_validation = null;
	protected $_rules = null;
	protected $_submitted = false;
	protected $_token = false;

	protected $_settings = null;
	protected $_settings_default = array();

	/**
	 * What happens after the form has been submitted, message or redirect
	 * @var array
	 */
	protected $_confirmation = array();
	protected $_confirmation_location = 'after';

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
	protected $_fields = array();

	protected $_field_display_conds = array();

	protected $_modules = array();

	public function __construct($name, $fields = array()) {

		// Setup default values
		$this->_name = $name;
		$this->_fields = array();
		$this->_errors = array();
		$this->_rules = array();
		$this->_notifications = array();
		$this->_settings_default = array(
			'database' => 'yes',
			'labels' => array(
				'submit' => __('Send', "wpdf")
			)
		);
		$this->_confirmation = array(
			'type' => 'message',
			'message' => __('Form has been successfully submitted!', "wpdf")
		);

		// load default settings
		$this->_settings = apply_filters('wpdf/form_settings', $this->_settings_default, $this->getId() );

		// setup fields
		if(!empty($fields)) {
			foreach ( $fields as $field_id => $field ) {

				$type                       = $field['type'];
				switch($type){
					case 'text':
						$this->_fields[ $field_id ] = new WPDF_TextField( $field_id, $type, $field );
						break;
					case 'textarea':
						$this->_fields[ $field_id ] = new WPDF_TextareaField( $field_id, $type, $field );
						break;
					case 'select':
						$this->_fields[ $field_id ] = new WPDF_SelectField( $field_id, $type, $field );
						break;
					case 'radio':
						$this->_fields[ $field_id ] = new WPDF_RadioField( $field_id, $type, $field );
						break;
					case 'checkbox':
						$this->_fields[ $field_id ] = new WPDF_CheckboxField( $field_id, $type, $field );
						break;
					case 'file':
						$this->_fields[ $field_id ] = new WPDF_FileField( $field_id, $type, $field );
						break;
				}
				//$this->_fields[ $field_id ] = new WPDF_FormField( $field_id, $type, $field );

				if ( $type == 'file' ) {
					$this->_has_file_field = true;
				}

				// get display rules
				$this->extract_display_conditions($field_id, $field);

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

		// now all fields have been initialized
		if(!empty($fields)) {
			foreach ( $fields as $field_id => $field ) {

				// setup display conditions
				$this->extract_display_conditions( $field_id, $field );
			}
		}

		$this->_data = new WPDF_FormData($this, $_POST, $_FILES);
	}

	/**
	 * Build array of field display conditions
	 *
	 * @param $field_id string Name of field
	 * @param $field array
	 */
	private function extract_display_conditions($field_id, $field){

		if(isset($field['display_conditions']) && is_array($field['display_conditions']) && !empty($field['display_conditions'])){

			$name = $this->_fields[ $field_id ]->getInputName();
			$this->_field_display_conds[$name] = array();

			foreach($field['display_conditions'] as $f => $v){

				$operator = '=';
				$value = $v;

				if(is_array($v)){
					$operator = isset($v['operator']) && $v['operator'] == '!=' ? '!=' : $operator;
					$value = isset($v['value']) ? $v['value'] : $value;
				}

				$target = $this->_fields[$f];
				$this->_field_display_conds[$name][] = array(
					'field' => $target->getInputName(),
					'field_type' =>  $target->getType(),
					'operator' => $operator,
					'value' => $value,
				);
			}
		}
	}

	public function process(){

		if( ! wp_verify_nonce( $_POST['wpdf_nonce'], 'wpdf_submit_form_' . $this->getName() )){
			$this->setError( __("An Error occurred when submitting the form, please retry.", "wpdf") );
		}

		if( intval($_SERVER['CONTENT_LENGTH'])>0 && count($_POST)===0 ){
			$this->setError( __("An Error occurred: PHP discarded POST data because of request exceeding post_max_size.", "wpdf") );
		}


		// clear data array
		$this->_submitted = true;
		$this->_validation = new WPDF_Validation($this->_rules);

		// make sure valid token is present
		$token = $this->getToken(false);
		if(!isset($token) || !$this->verifyToken($token)){
			$this->setError( __("Your session has expired", "wpdf") );
		}

		// load modules
		// todo: modules should be loaded after form has been registered with all settings
		$this->load_modules();

		$form_data = $this->_data->toArray();

		foreach($this->_fields as $field_id => $field){

			if($field->isType("file")){

				// check for file upload errors, before checking against field validation rules
				if(isset($_FILES[$field->getInputName()])){
					$file_data = $_FILES[$field->getInputName()];

					if($file_data['error'] !== UPLOAD_ERR_NO_FILE && !$field->isValidExt($file_data)){
						$this->_errors[$field_id] = WPDF()->text->get('invalid_ext', 'upload');
					}elseif($file_data['error'] !== UPLOAD_ERR_NO_FILE && !$field->isAllowedSize($file_data)){
						$this->_errors[$field_id] = sprintf(WPDF()->text->get('max_size', 'upload'), $field->getMaxFileSize());
					}elseif($file_data['error'] !== UPLOAD_ERR_OK && $file_data['error'] !== UPLOAD_ERR_NO_FILE){
						$this->_errors[$field_id] = $this->_validation->get_upload_error($file_data['error'], $field->getMaxFileSize());
					}

				}
			}

			if(!isset($this->_errors[$field_id]) && !$this->_validation->validate($field, $form_data)){
				$this->_errors[$field_id] = $this->_validation->get_error();
			}
		}

		if(!$this->has_errors()){

			// validate reCaptcha
			if( !$this->verifyRecaptcha()){
				$this->setError( __("The reCAPTCHA wasn't entered correctly. Go back and try it again.", 'wpdf') );
				return;
			}

			$submit = apply_filters( 'wpdf/process_form', true, $this, $this->_data );
			if(!$submit){
				return;
			}

			// store form data in database
			if($this->get_setting('database') == 'yes' && !defined('WPDF_PREVIEW')) {
				$db = new WPDF_DatabaseManager();
				$db->save_entry( $this->getName(), $this->_data );
			}

			// send email notifications with template tags
			if ( ! empty( $this->_notifications ) ) {
				$this->_email_manager = new WPDF_EmailManager( $this->_notifications );
				$this->_email_manager->send( $this->_data );
			}

			// redirect now if needed
			if ( $this->_confirmation['type'] == "redirect" ) {
				wp_redirect( $this->_confirmation['redirect_url'] );
				exit;
			}

			$this->clearToken();
			// on form complete
			do_action('wpdf/form_complete', $this, $this->_data);
		}
	}

	public function load_modules(){

		$modules = apply_filters('wpdf/list_modules', array());

		foreach($modules as $module_id => $module){

			// check if class exists
			// todo: How should we handle errors like this, report it?
			if(!class_exists($module)){
				throw new Error("WPDF Module could not be loaded: " . $module);
			}

			// check if class key exists
			if($this->get_setting($module_id)){
				$this->_modules[$module_id] = new $module;
			}
		}
	}

	public function settings($settings){

		$this->_settings = array_replace_recursive($this->_settings, $settings);
	}

	public function get_setting($setting){
		return isset($this->_settings[$setting]) ? $this->_settings[$setting] : false;
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

		if(isset($args['conditions'])){
			$notification['conditions'] = $args['conditions'];
		}

		$this->_notifications[] = new WPDF_Notification( $notification );

	}

	public function add_field_error($field, $message){
		$this->_errors[$field] = $message;
	}

	public function add_error($message){
		$this->_error = $message;
	}

	#region Form Output

	/**
	 * Display form opening tag
	 *
	 * @param $args array
	 */
	public function start($args = array()){

		// if is file upload form need to add

		$attrs = ' method="post"';
		if($this->_has_file_field){
			$attrs .= ' enctype="multipart/form-data"';
		}

		if(isset($args['id'])){
			$attrs .= sprintf( ' id="%s"', esc_attr( $args['id'] ) );

			if(!isset($args['action'])){
				$attrs .= sprintf( ' action="#%s"', esc_attr( $args['id'] ) );
			}
		}

		if(isset($args['action'])){
			$attrs .= sprintf( ' action="#%s"', esc_attr( $args['action'] ) );
		}

		$classes = 'wpdf-form ';

		if(isset($args['class'])){
			$classes .= esc_attr( $args['class'] );
		}

		if($this->get_setting('enable_layout_css') == 'enabled'){
			$classes .= ' wpdf-form__layout';
		}

		$attrs .= sprintf( ' class="%s"', $classes );

		//todo: output js data attributes
		if(!empty($this->_field_display_conds)){
			$attrs .= sprintf( " data-wpdf-display='%s'", json_encode($this->_field_display_conds));
		}

		echo "<form {$attrs}>";
		?>
		<div class="wpdf-form-title">
			<h1><?php echo $this->getLabel(); ?></h1>
		</div>
		<div class="wpdf-form-copy">
			<?php echo $this->getContent(); ?>
		</div>
		<?php
	}

	public function getFieldName($field_id){

		$field = isset($this->_fields[$field_id]) ? $this->_fields[$field_id] : false;
		if($field){
			return $field->getInputName();
		}

		return false;
	}

	public function getFieldValue($field_id, $default = false){

		$data = $this->_data->get($field_id);
		if($data){
			return $data;
		}

		return $default;
	}

	public function getValidationRules(){
		return $this->_rules;
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

		$classes = array();
		$classes[] = $this->_fields[$field_id]->getExtraClasses();

		switch($type){
			case 'validation':

				if(isset($this->_errors[$field_id])){
					$classes[] = 'wpdf-has-error';
				}
				break;
			case 'type':
				$classes[] = sprintf('wpdf-input-%s', $this->_fields[$field_id]->getType());
				break;
		}

		echo implode(' ', $classes);
	}

	public function error($field_id){

		$beforeError = '<span class="wpdf-field-error">';
		$afterError = '</span>';

		if(isset($this->_errors[$field_id])){
			echo $beforeError . $this->_errors[$field_id] . $afterError;
		}
	}

	/**
	 * Display submit button for form
	 *
	 * @param $label
	 * @param array $args
	 */
	public function submit($label = false, $args = array()){

		if(!$this->hasValidToken()){
			return;
		}

		// output recaptcha
		$this->outputRecaptcha();

		if(empty($label)){
			$label = $this->_settings['labels']['submit'];
		}
		echo '<input type="submit" value="'.$label.'" class="wpdf-button wpdf-submit-button" />';
	}

	/**
	 * Display form closing tag
	 */
	public function end(){

		// hidden fields
		wp_nonce_field( 'wpdf_submit_form_' . $this->getName(), 'wpdf_nonce' );
		echo '<input type="hidden" name="wpdf_action" value="' . $this->getName() .'" />';
		echo '<input type="hidden" name="wpdf_token" value="' . $this->getToken() .'" />';

		echo '</form>';
	}

	#endregion

	#region Form Errors

	/**
	 * Set form error
	 * @param $error
	 */
	public function setError($error){
		$this->_error = $error;
	}

	/**
	 * Add form error
	 * @param $error
	 */
	public function addError($error){
		$this->_errors[] = $error;
	}

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
	 * Check to see if form has valid token
	 *
	 * @return bool
	 */
	public function hasValidToken(){

		$token = $this->getToken(false);
		if(!$this->_submitted || $this->verifyToken($token)){
			return true;
		}

		return false;
	}

	/**
	 * Display form error message
	 */
	public function errors(){

		echo '<div class="wpdf-form-error">';

		if($this->_error){
			echo "<p>".$this->_error."</p>";
		}else {

			echo sprintf( "<p>%s</p>", __( "Please make sure you have corrected any errors below before resubmitting the form.", "wpdf" ) );
			echo '<ul class="wpdf-form-errors">';
			foreach ( $this->_errors as $field_id => $error ) {
				echo '<li>' . $this->_fields[ $field_id ]->getLabel() . ' - ' . $error . '</li>';
			}
			echo '</ul>';
		}
		echo '</div>';
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

		return __("Form submitted successfully", "wpdf");
	}

	#endregion

	/**
	 * @return string
	 */
	public function getName() {
		return $this->_name;
	}

	public function getLabel(){
		return $this->_name;
	}

	/**
	 * Get form content
	 *
	 * @return null|string
	 */
	public function getContent(){

		// don't show form content if confirmation location is set to replace
		if($this->_submitted && $this->getConfirmationLocation() == 'replace'){
			return '';
		}

		return $this->_content;
	}

	public function getId(){
		return $this->ID;
	}

	public function getFieldLabel($field, $fallback = null){

		if(!is_null($fallback)){
			return isset($this->_fields[$field]) ? $this->_fields[$field]->getLabel() : $fallback;
		}

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

	protected function getConfirmationLocation(){
		return $this->_confirmation_location;
	}

	#region Session Token

	/**
	 * Get current form token
	 *
	 * @param bool $generate flag to allow token generation
	 *
	 * @return string
	 */
	public function getToken($generate = true){

		// form has not been submitted
		if($generate && !$this->_submitted && !$this->_token){

			// generate fresh token
			do{
				$this->_token = wp_generate_password(12, false);
			}while(get_transient('wpdf_token_' .$this->_token) !== false);

			// store transient token
			set_transient('wpdf_token_' . $this->_token, array(
				'ip' => wpdf_getIp(),
				'time' => time()
			), HOUR_IN_SECONDS);
		}elseif($this->_submitted && isset($_REQUEST['wpdf_token'])){
			$this->_token = $_REQUEST['wpdf_token'];
		}

		return $this->_token;
	}

	/**
	 * Make sure token is valid
	 *
	 * @param string $token
	 *
	 * @return bool
	 */
	public function verifyToken($token){

		if($token && !empty($token)){

			$transient = get_transient('wpdf_token_' . $token);
			if($transient){
				if(isset($transient['ip']) && $transient['ip'] == wpdf_getIp()){
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Clear active token
	 *
	 * @return bool
	 */
	public function clearToken(){
		if($this->verifyToken($this->_token)){
			return delete_transient('wpdf_token_' . $this->_token);
		}
		return false;
	}

	#endregion

	#region reCaptcha Validation

	/**
	 * Check to see if recaptcha has been setup form the form
	 *
	 * @return bool
	 */
	public function recaptchaSetup(){
		if( $this->get_setting('recaptcha_private') && $this->get_setting('recaptcha_public') ){
			return true;
		}
		return false;
	}

	/**
	 * Check to see if recaptcha is a valid response
	 *
	 * @return bool
	 */
	public function verifyRecaptcha(){

		// escape if recaptcha is not setup
		if(!$this->recaptchaSetup()){
			return true;
		}

		$secretKey =  $this->get_setting('recaptcha_private');
		$captcha = $_POST['g-recaptcha-response'];
		$response= json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']), true);
		if( isset($response['success']) && $response['success'] === true){
			return true;
		}

		return false;
	}

	/**
	 * Display recaptcha field
	 */
	public function outputRecaptcha(){

		// escape if recaptcha is not setup
		if(!$this->recaptchaSetup()){
			return;
		}

		$publicKey = $this->get_setting('recaptcha_public');
		?>
		<div class="wpdf-form-row wpdf-input-captcha">
			<div class="g-recaptcha" data-sitekey="<?php echo $publicKey; ?>"></div>
		</div>
		<?php
	}

	#endregion
}