<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 06/08/2016
 * Time: 20:35
 */

class WPDF_Admin{

	protected $_messages = array();

	public function __construct() {
		add_action( 'admin_menu', array($this, 'wpdf_register_pages'));
		add_action('admin_init', array( $this, 'init'));
		add_action('init', array($this, 'update_check'));
		add_action('init', array($this, 'register_form_post_type'));
		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));

		// mce editor
		add_filter('mce_external_plugins', array($this, 'enqueue_mce_scripts'));
		add_filter('mce_buttons', array($this, 'register_mce_buttons'));
	}

	function enqueue_scripts(){
		$version = WPDF()->get_version();
		if(defined('WP_DEBUG') && WP_DEBUG){
			$version = time();
		}

		wp_enqueue_script('wpdf-admin', WPDF()->get_plugin_url() . 'assets/admin/js/wpdf.min.js', array('jquery-ui-draggable', 'jquery-ui-sortable'), $version);
		wp_enqueue_style('wpdf-admin', WPDF()->get_plugin_url() . 'assets/admin/css/wpdf.min.css', array(), $version);
	}

	function register_mce_buttons($buttons){
		$buttons[] = 'wpdf';
		return $buttons;
	}

	function enqueue_mce_scripts($plugins){
		$plugins['wpdf_form_btn'] = WPDF()->get_plugin_url() . 'assets/admin/js/editor_shortcode.js';
		return $plugins;
	}

	function wpdf_register_pages(){

		$admin_slug = "wpdf-forms";
		add_menu_page("WP Form", "Forms", "manage_options", $admin_slug, array( $this, 'wpdf_form_page'), 'dashicons-feedback', 30 );
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
				case 'edit-form-fields':

					$this->save_form_fields();
					break;
				case 'edit-form-notifications':

					$this->save_form_notifications();
					break;
				case 'edit-form-settings':

					$this->save_form_settings();
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

			$this->display_manage_form( $form );

		}elseif ( $action == 'manage' && $form_id ){

//			$form = new WPDF_DB_Form($form_id);
			$this->display_manage_form( $form );

		}elseif ( $action == 'notifications' && $form_id ){

//			$form = new WPDF_DB_Form($form_id);
			$this->display_notifications_form( $form );

		}elseif ( $action == 'settings' && $form_id ) {

//			$form = new WPDF_DB_Form( $form_id );
			$this->display_settings_form( $form );

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
	 * Display form fields page
	 *
	 * @param WPDF_DB_Form|bool $form
	 */
	private function display_manage_form($form = false){

		require WPDF()->get_plugin_dir() . 'templates/admin/page-form-fields.php';
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
		if(!empty($settings['form_label'])){
			$form_data['form_label'] = $settings['form_label'];
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
					do{
						$field_id = wp_generate_password(6, false);
					}while(in_array($field_id, $field_ids));
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
			'default' => isset($field['default']) ? $field['default'] : ''
		);

		switch($field['type']){
			case 'select':

				if(isset($field['empty_text']) && !empty($field['empty_text'])){
					$data['empty'] = esc_attr($field['empty_text']);
				}else{
					$data['empty'] = false;
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
		}

		$data['validation'] = array(
			array('type' => 'required'),
			array('type' => 'email')
		);

		$rules = array();
		$data['validation'] = array();
		if(isset($field['validation_type']) && !empty($field['validation_type'])) {
			foreach ( $field['validation_type'] as $rule ) {

				// skip if empty value
				if(empty($rule)){
					continue;
				}

				$rules[] = array(
					'type' => $rule
				);
			}
		}
		if(!empty($rules)){
			$data['validation'] = $rules;
		}

		return $data;
	}


}

new WPDF_Admin();