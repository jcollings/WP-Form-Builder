<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 06/08/2016
 * Time: 20:35
 */

class WPDF_Admin{

	protected $_messages = array();
	protected $_errors = array();

	public function __construct() {
		add_action( 'admin_menu', array($this, 'wpdf_register_pages'));
		add_action('admin_init', array( $this, 'init'));
		add_action('init', array($this, 'update_check'));
		add_action('init', array($this, 'register_form_post_type'));
		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));

		// mce editor
		add_filter('mce_external_plugins', array($this, 'enqueue_mce_scripts'));
		add_filter('mce_buttons', array($this, 'register_mce_buttons'));
		add_action('wp_ajax_wpdf_insert_dialog', array($this, 'mce_insert_dialog'));
	}

	function enqueue_scripts(){
		$version = WPDF()->get_version();
		$ext = '.min';
		if((defined('WP_DEBUG') && WP_DEBUG) || (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG)){
			$version = time();
			$ext = '';
		}

		wp_enqueue_script('tiptip', WPDF()->get_plugin_url() . 'assets/admin/js/jquery-tipTip'.$ext.'.js', array(), '1.3');
		wp_enqueue_script('wpdf-admin', WPDF()->get_plugin_url() . 'assets/admin/js/wpdf'.$ext.'.js', array('jquery-ui-draggable', 'jquery-ui-sortable', 'tiptip', 'iris'), $version);
		wp_enqueue_style('wpdf-admin', WPDF()->get_plugin_url() . 'assets/admin/css/wpdf'.$ext.'.css', array(), $version);
	}

	function mce_insert_dialog(){

		require WPDF()->get_plugin_dir() . 'templates/admin/mce-insert-dialog.php';
		die();
	}

	function register_mce_buttons($buttons){
		$buttons[] = 'wpdf';
		return $buttons;
	}

	function enqueue_mce_scripts($plugins){

		$ext = '.min';
		if(defined('WP_DEBUG') && WP_DEBUG){
			$ext = '';
		}

		$plugins['wpdf_form_btn'] = WPDF()->get_plugin_url() . 'assets/admin/js/editor-shortcode'.$ext.'.js';
		return $plugins;
	}

	function wpdf_register_pages(){

		$admin_slug = "wpdf-forms";
		add_menu_page("WP Form Builder", "Forms", "manage_options", $admin_slug, array( $this, 'wpdf_form_page'), 'dashicons-feedback', 30 );
		add_submenu_page( $admin_slug, 'New Form', 'Add Form', 'manage_options', 'admin.php?page=wpdf-forms&action=new');
	}

	function init(){
		$delete_submission = isset($_GET['delete_submission']) ? $_GET['delete_submission'] : false;
		if($delete_submission){

			$db = new WPDF_DatabaseManager();
			$db->delete_entry($delete_submission);

			$url = remove_query_arg('delete_submission');
			wp_redirect($url);
			exit();
		}

		$delete_form = isset($_GET['action']) && $_GET['action'] == 'delete-form' && isset($_GET['form_id']) ? intval($_GET['form_id']) : 0;
		if($delete_form > 0){

			$deleted = wp_delete_post($delete_form);
			if(!$deleted){
				//todo: Alert user form was not deleted
			}

			wp_redirect(admin_url('admin.php?page=wpdf-forms'));
			exit();
		}

		if(isset($_POST['wpdf-action'])){

			switch($_POST['wpdf-action']){
				case 'create-form':

					$this->create_form();
					break;
				case 'edit-form-fields':

					$this->save_form_fields();
					break;
				case 'edit-form-notifications':

					$this->save_form_notifications();
					break;
				case 'edit-form-settings':

					$this->save_form_settings();
					break;
				case 'edit-form-style':
					$this->save_form_style();
					break;
			}
		}
	}

	function update_check(){
		$config = array(
			'slug' => WPDF()->get_plugin_slug() . '/wpdf.php', // this is the slug of your plugin
			'proper_folder_name' => WPDF()->get_plugin_slug(), // this is the name of the folder your plugin lives in
			'api_url' => 'https://api.github.com/repos/jcollings/wordPress-developer-forms', // the GitHub API url of your GitHub repo
			'raw_url' => 'https://raw.github.com/jcollings/wordPress-developer-forms/master', // the GitHub raw url of your GitHub repo
			'github_url' => 'https://github.com/jcollings/wordPress-developer-forms', // the GitHub url of your GitHub repo
			'zip_url' => 'https://github.com/jcollings/wordPress-developer-forms/zipball/master', // the zip url of the GitHub repo
			'sslverify' => true, // whether WP should check the validity of the SSL cert when getting an update, see https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/2 and https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/4 for details
			'requires' => '3.0', // which version of WordPress does your plugin require?
			'tested' => '4.6', // which version of WordPress is your plugin tested up to?
			'readme' => 'README.md', // which file to use as the readme for the version number
			'access_token' => '', // Access private repositories by authorizing under Appearance > GitHub Updates when this example plugin is installed
		);
		new WP_GitHub_Updater($config);
	}

	function register_form_post_type(){
		$args = array(
			'public' => false,
			'supports' => array(
				'title',
				'editor',
				'author',
				'revisions'
			)
		);
		register_post_type( 'wpdf_form', $args );
	}

	function wpdf_form_page(){

		echo '<div class="wrap wpdf">';

		$form = isset($_GET['form']) ? wpdf_get_form($_GET['form']) : false;
		$form_id = isset($_GET['form_id']) ? intval($_GET['form_id']) : false;

		if(!$form && intval($form_id) > 0){
			$form = new WPDF_DB_Form($form_id);
		}

		$submission_id = isset($_GET['submission']) ? $_GET['submission'] : false;
		$action = isset($_GET['action']) ? $_GET['action'] : false;

		if( $action == 'new' ) {

			//$this->display_manage_form( $form );
			$this->display_create_form();

		}elseif ( $action == 'manage' && $form_id ){

			$this->display_manage_form( $form );

		}elseif ( $action == 'notifications' && $form_id ){

			$this->display_notifications_form( $form );

		}elseif ( $action == 'settings' && $form_id ) {

			$this->display_settings_form( $form );

		}elseif ( $action == 'style' && $form_id ) {

			$this->display_style_form( $form );

		}elseif($form) {

			if($submission_id){

				$this->display_submission_page($form, $submission_id);
			}else{

				$this->display_submissions_archive($form);
			}

		}else{

			$this->display_forms_archive();
		}

		echo '</div>';

	}

	public function add_error($msg){
		$this->_errors[] = $msg;
	}

	public function has_errors(){
		if(!empty($this->_errors)){
			return true;
		}
		return false;
	}

	public function get_errors(){
		return $this->_errors;
	}

	/**
	 * Display view submission page
	 *
	 * @param $form WPDF_Form
	 * @param $submission_id int
	 */
	private function display_submission_page($form, $submission_id){

		?>

		<div class="wpdf-form-manager">

			<?php $this->display_form_header('submissions', $form); ?>

			<div class="wpdf-cols">

				<div class="wpdf-left">
					<div class="wpdf-left__inside">
						<?php

						$db = new WPDF_DatabaseManager();
						$submissions = $db->get_submission($submission_id);
						$db->mark_as_read($submission_id);

						// get user name
						$user_id = isset($submissions[0]->user_id) ? $submissions[0]->user_id : 'N/A';
						$user = 'Guest';
						if(intval($user_id) > 0){
							$user = get_user_by('id', intval($user_id));
							if($user){
								$user = $user->data->user_login;
							}
						}

						// get ip
						$ip = isset($submissions[0]->ip) ? $submissions[0]->ip : 'N/A';

						// date
						$date = isset($submissions[0]->created) ? $submissions[0]->created : false;
						if($date){
							$date = date( 'M j, Y @ H:i', strtotime($date));
						}

						if(!empty($submissions)) {

							foreach ( $submissions as $submission ) {

								if ( $submission->field_type == 'virtual' ) {
									$content = apply_filters( 'wpdf/display_submission_field', $submission->content, $submission->field, $form );
								} else {
									$content = esc_html( $submission->content );
								}
								echo "<p><strong>{$form->getFieldLabel($submission->field, $submission->field_label)}</strong>:<br />{$content}</p>";
							}
						}

						$url = 'form=' .$form->getName();
						if($form->getId()){
							$url = 'form_id=' .$form->getId();
						}

						echo '<a href="'. admin_url('admin.php?page=wpdf-forms&'.$url).'" class="button">Back</a>';
						?>
					</div>
				</div>
				<div class="wpdf-right">

					<div class="wpdf-right__inside">
						<div class="wpdf-panel wpdf-panel--active">
							<div class="wpdf-panel__header">
								<p class="wpdf-panel__title">Information</p>
							</div>
							<div class="wpdf-panel__content">
								<?php if($date): ?>
									<div class="misc-pub-section curtime misc-pub-curtime">
										<span id="timestamp">Submitted on: <b><?php echo $date; ?></b></span>
									</div>
								<?php endif; ?>

								<div class="misc-pub-section misc-pub-visibility" id="visibility">
									From: <span id="post-visibility-display"><?php echo $user; ?> (<?php echo $ip; ?>)</span>
								</div>
							</div>
						</div>
					</div>

				</div>

			</div>

			<div class="wpdf-clear"></div>
		</div>
		<?php
	}

	/**
	 * Display submissions archive
	 *
	 * @param $form WPDF_Form
	 */
	private function display_submissions_archive($form){

		echo '<form method="GET">';

		echo '<input type="hidden" name="page" value="wpdf-forms" />';
		echo '<input type="hidden" name="form" value="'.$form->getName().'" />';

		?>

			<div class="wpdf-form-manager">

				<?php $this->display_form_header('submissions', $form); ?>

				<div class="wpdf-cols">

					<div class="wpdf-full">

						<div class="wpdf-table-archive">

						<?php
						//echo '<h1 style="display: block; float:left;">' . $form->getLabel() . ' Submissions</h1>';
						require 'class-wpdf-submissions-list-table.php';
						$wpdf_submissions_table = new WPDF_Submissions_List_Table( $form );
						$wpdf_submissions_table->prepare_items();

						echo $wpdf_submissions_table->search_box('Search Entries', 'wpdf-search');

						echo '<div style="clear:both"></div>';


						$wpdf_submissions_table->display();
						?>
						</div>

					</div>

				</div>

				<div class="wpdf-clear"></div>
			</div>
		<?php

		echo '</form>';
	}

	/**
	 * Display forms archive
	 */
	private function display_forms_archive(){
		$url = admin_url('admin.php?page=wpdf-forms&action=new');
		echo '<h1>Forms <a href="'.$url.'" class="button">Add New</a></h1>';

		require 'class-wpdf-forms-list-table.php';
		$wpdf_forms_table = new WPDF_Forms_List_Table();
		$wpdf_forms_table->prepare_items();

		$wpdf_forms_table->display();
	}

	/**
	 * Display forms edit header
	 *
	 * @param string $active
	 * @param WPDF_Form $form
	 */
	private function display_form_header($active = 'fields', $form){

		require WPDF()->get_plugin_dir() . 'templates/admin/settings-header.php';
	}

	/**
	 * Display form notifications page
	 *
	 * @param WPDF_DB_Form|bool $form
	 */
	private function display_notifications_form($form = false){

		require WPDF()->get_plugin_dir() . 'templates/admin/page-form-notifications.php';
	}

	/**
	 * Display form settings page
	 *
	 * @param WPDF_DB_Form|bool $form
	 */
	private function display_settings_form($form = false){

		require WPDF()->get_plugin_dir() . 'templates/admin/page-form-settings.php';

	}

	/**
	 * Display form styling page
	 *
	 * @param WPDF_DB_Form|bool $form
	 */
	private function display_style_form($form = false){
		require WPDF()->get_plugin_dir() . 'templates/admin/page-form-style.php';
	}

	/**
	 * Display form fields page
	 *
	 * @param WPDF_DB_Form|bool $form
	 */
	private function display_manage_form($form = false){

		require WPDF()->get_plugin_dir() . 'templates/admin/page-form-fields.php';
	}

	/**
	 * Display create form screen
	 */
	private function display_create_form(){

		require WPDF()->get_plugin_dir() . 'templates/admin/page-form-create.php';
	}

	/**
	 * Display form field panel
	 *
	 * @param WPDF_FormField|string $field
	 * @param bool $active
	 */
	private function display_field_panel($field, $form = null, $active = false){

		if(is_string($field)){

			switch($field){
				case 'text':
					$field = new WPDF_TextField( '', $field);
					break;
				case 'textarea':
					$field = new WPDF_TextareaField( '', $field);
					break;
				case 'select':
					$field = new WPDF_SelectField( '', $field);
					break;
				case 'radio':
					$field = new WPDF_RadioField( '', $field);
					break;
				case 'checkbox':
					$field = new WPDF_CheckboxField( '', $field);
					break;
				case 'file':
					$field = new WPDF_FileField( '', $field);
					break;
				default:
					$field = new WPDF_FormField('', $field);
					break;
			}
		}

		require WPDF()->get_plugin_dir() . 'templates/admin/field-panel.php';
	}

	private function save_form_settings(){

		$form_id = $_POST['wpdf-form'];
		$form = get_post($form_id);
		$form_data = maybe_unserialize($form->post_content);

		$settings = $_POST['wpdf_settings'];

		// save form settings array
		if(!isset($form_data['settings'])){
			$form_data['settings'] = array();
		}

		$form_data['content'] = isset($settings['form_content']) ? $settings['form_content'] : '';

		$form_data['settings'] = array(
			'labels' => array(
				'submit' => $settings['submit_label']
			)
		);

		// save form confirmations array
		if(!isset($form_data['confirmations'])){
			$form_data['confirmations'] = array();
		}

		$form_data['confirmations'] = array(
			array(
				'type' => $settings['confirmation_type'],
				'redirect_url' => $settings['confirmation_redirect'],
				'message' => $settings['confirmation_message']
			)
		);
		// save how confirmation should display
		$form_data['confirmation_location'] = isset($settings['confirmation_location']) && in_array($settings['confirmation_location'], array('replace', 'after')) ? $settings['confirmation_location'] : 'after';

		if(!empty($settings['form_label'])){
			$form_data['form_label'] = $settings['form_label'];
		}

		if(isset($settings['enable_style'])){
			$form_data['settings']['enable_style'] = $settings['enable_style'];
		}

		// save recaptcha settings
		if(isset($settings['recaptcha_public'])){
			$form_data['settings']['recaptcha_public'] = $settings['recaptcha_public'];
		}
		if(isset($settings['recaptcha_private'])){
			$form_data['settings']['recaptcha_private'] = $settings['recaptcha_private'];
		}

		$post = wp_update_post(array(
			'ID' => $form_id,
			'post_content' => serialize($form_data)
		));

		if(!is_wp_error($post)){
			wp_redirect(admin_url('admin.php?page=wpdf-forms&action=settings&form_id=' . $form_id));
			exit();
		}

		die();
	}

	private function save_form_notifications(){

		$form_id = $_POST['wpdf-form'];
		$form = get_post($form_id);
		$form_data = maybe_unserialize($form->post_content);

		$form_data['notifications'] = array();
		foreach($_POST['notification'] as $i => $notification){
			$form_data['notifications'][ $i ] = array(
				'to'      => $notification['to'],
				'subject' => $notification['subject'],
				'message' => $notification['message'],
				'from'    => $notification['from'],
				'cc'      => $notification['cc'],
				'bcc'     => $notification['bcc'],
			);
		}

		$post = wp_update_post(array(
			'ID' => $form_id,
			'post_content' => serialize($form_data)
		));

		if(!is_wp_error($post)){
			wp_redirect(admin_url('admin.php?page=wpdf-forms&action=notifications&form_id=' . $form_id));
			exit();
		}

		die();
	}

	private function save_form_style(){

		$form_id = $_POST['wpdf-form'];
		$form = get_post($form_id);
		$form_data = maybe_unserialize($form->post_content);

		$style = isset($_POST['wpdf_style']) ? $_POST['wpdf_style'] : array();
		$disabled = isset($_POST['wpdf_style_disable']) ? $_POST['wpdf_style_disable'] : array();

		$default = array(
			'form_bg_colour' => '',
			'form_text_colour' => '',
			'form_bg_error_colour' => '#c63838',
			'form_text_error_colour' => '#ffffff',
			'form_bg_success_colour' => '#80cd5f',
			'form_text_success_colour' => '#ffffff',
			'field_label_bg_colour' => '',
			'field_label_text_colour' => '',
			'field_input_bg_colour' => '',
			'field_input_text_colour' => '',
			'field_error_text_colour' => '#c63838',
			'field_border_colour' => '',
			'field_error_border_colour' => '#c63838',
			'button_bg_colour' => '#000000',
			'button_text_colour' => '#ffffff',
			'button_hover_bg_colour' => '#333333',
			'button_hover_text_colour' => '#ffffff',
			'checkbox_text_colour' => ''
		);

		if(!isset($form_data['theme'])){
			$form_data['theme'] = array();
		}
		if(!isset($form_data['theme_disabled'])){
			$form_data['theme_disabled'] = array();
		}

		foreach($default as $key => $val){
			$form_data['theme'][$key] = isset($style[$key]) ? $style[$key] : $default[$key];
			$form_data['theme_disabled'][$key] = in_array($key, $disabled) ? true : false;
		}

		$post = wp_update_post(array(
			'ID' => $form_id,
			'post_content' => serialize($form_data)
		));

		if(!is_wp_error($post)){
			wp_redirect(admin_url('admin.php?page=wpdf-forms&action=style&form_id=' . $form_id));
			exit();
		}

		die();
	}

	/**
	 * Create form with given name, then redirect to manage fields screen
	 *
	 * @return void
	 */
	private function create_form(){

		$name = sanitize_text_field($_POST['form-name']);
		if(empty($name)){
			$this->add_error("Please enter a name before creating the form.");
			return;
		}

		// add default form data
		$form_data = array(
			'form_label' => $name,
			'fields' => array(),
			'settings' => array(
				'labels' => array(
					'submit' => 'Submit'
				)
			),
			'confirmations' => array(
				array(
					'type' => 'message',
					'message' => 'The form has been submitted successfully'
				)
			),
			'notifications' => array(
				array(
					'to' => '{{admin_email}}',
					'subject' => 'New Form Submission',
					'message' => 'New Form Submission: {{fields}}'
				)
			),
		);

		$postarr = array(
			'post_title' => $name,
			'post_type' => 'wpdf_form',
			'post_status' => 'publish',
			'post_content' => serialize($form_data)
		);

		$post = wp_insert_post($postarr, true);
		if(!is_wp_error($post)){
			wp_redirect(admin_url('admin.php?page=wpdf-forms&action=manage&form_id=' . $post));
			exit();
		}else{
			$this->add_error("An error occured when creating the form.");
		}
	}

	private function save_form_fields(){

		$form_id = null;
		$old_form = null;

		if(intval($_POST['wpdf-form']) > 0){
			$form_id = intval($_POST['wpdf-form']);
			$type = get_post_type($form_id);
			if($type !== 'wpdf_form'){
				wp_redirect(admin_url('admin.php?page=wpdf-forms&action=new'));
				exit();
			}
		}

		$fields = array();

		if( isset($_POST['field']) && !empty($_POST['field']) ) {

			// fetch list of existing field ids
			$field_ids = array();
			foreach($_POST['field'] as $arr){
				if(isset($arr['id']) && !empty($arr['id'])){
					$field_ids[] = $arr['id'];
				}
			}

			foreach ( $_POST['field'] as $field ) {

				// get or randomly generate field id, should only generate on creating field
				$field_id = isset($field['id']) && !empty($field['id']) ? $field['id'] : false;
				if(!$field_id){

					// generate field id from label
					$field_id = $base_name = sanitize_title($field['label']);
					$c = 1;
					while(empty($field_id) || in_array($field_id, $field_ids)){
						$field_id = sprintf( '%s-%d', $base_name, $c);
						$c++;
					}
					$field_ids[] = $field_id;
				}

				$fields[ $field_id ] = $this->save_field( $field );
			}
		}

		$postarr = array(
			'post_type' => 'wpdf_form',
			'post_status' => 'publish'
		);

		// update existing post
		if(!is_null($form_id)){
			$postarr['ID'] = $form_id;
			$postarr['post_author'] = get_current_user_id();

			$form = get_post($form_id);
			$form_data = maybe_unserialize($form->post_content);

			if(!is_array($form_data)){
				$form_data = array();
			}

			$form_data['fields'] = $fields;
			$post_content = $form_data;

		}else{
			$post_content = array(
				'fields' => $fields,
				'settings' => array(
					'labels' => array(
						'submit' => 'Submit'
					)
				),
				'confirmations' => array(
					array(
						'type' => 'message',
						'message' => 'The form has been submitted successfully'
					)
				),
				'notifications' => array(
					array(
						'to' => '{{admin_email}}',
						'subject' => 'New Form Submission',
						'message' => 'New Form Submission: {{fields}}'
					)
				),
			);
		}

		// encode data to store
		$postarr['post_content'] = serialize($post_content);

		$post = wp_insert_post($postarr, true);
		if(!is_wp_error($post)){
			wp_redirect(admin_url('admin.php?page=wpdf-forms&action=manage&form_id=' . $post));
			exit();
		}

		die();
	}

	private function save_field($field){

		$data = array(
			'type' => $field['type'],
			'label' => $field['label'],
			'placeholder' => isset($field['placeholder']) ? $field['placeholder'] : '',
			'default' => isset($field['default']) ? $field['default'] : '',
			'extra_class' => isset($field['css_class']) ? $field['css_class'] : ''
		);

		switch($field['type']){
			case 'select':

				if($field['type'] == 'select') {
					if ( isset( $field['empty_text'] ) && ! empty( $field['empty_text'] ) ) {
						$data['empty'] = esc_attr( $field['empty_text'] );
					} else {
						$data['empty'] = false;
					}

					if( isset( $field['select_type'] ) && !empty( $field['select_type'] ) ){
						$data['select_type'] = $field['select_type'];
					}else{
						$data['select_type'] = 'single';
					}
				}

			case 'radio':
			case 'checkbox':

				$options = array();
				$defaults = array();
				foreach($field['value_labels'] as $arr_id => $label){
					$option_key = isset($field['value_keys'][$arr_id]) && !empty($field['value_keys'][$arr_id]) ? esc_attr($field['value_keys'][$arr_id]) : esc_attr($label);
					$options[$option_key] = $label;

					if(isset($field['value_default'][$arr_id])){
						$defaults[] = $option_key;
					}
				}

				$data['options'] = $options;
				$data['default'] = $defaults;

				break;
			case 'textarea':
				$data['rows'] = isset($field['rows']) ? $field['rows'] : 8;
				break;
			case 'file':
				if(isset($field['max_file_size'])){

					// find upload limits
					$post_max_size = ini_get('post_max_size');
					$upload_max_filesize = ini_get('upload_max_filesize');
					$limit = $post_max_size;
					if($limit > $upload_max_filesize){
						$limit = $upload_max_filesize;
					}

					if( intval($field['max_file_size']) > $limit || intval($field['max_file_size']) < 0){
						$data['max_file_size'] = $limit;
					}else{
						$data['max_file_size'] = intval($field['max_file_size']);
					}
				}
				if(isset($field['allowed_ext'])){
					$data['allowed_ext'] = $field['allowed_ext'];
				}
				break;
		}

		if($field['type'] == 'radio' && !empty($field['value_default'])){
			$data['default'] = $field['value_default'];
		}

		$data['validation'] = array(
			array('type' => 'required'),
			array('type' => 'email')
		);

		$rules = array();
		$data['validation'] = array();
		if(isset($field['validation']) && !empty($field['validation'])) {
			foreach ( $field['validation'] as $rule ) {

				// skip if empty value
				if(empty($rule)){
					continue;
				}

				$rule_arr = array(
					'type' => $rule['type'],
				);

				if( isset($rule['msg']) && !empty($rule['msg']) ){
					$rule_arr['msg'] = $rule['msg'];
				}

				$rules[] = $rule_arr;
			}
		}
		if(!empty($rules)){
			$data['validation'] = $rules;
		}

		return $data;
	}


}

new WPDF_Admin();