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
		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
	}

	function enqueue_scripts(){
		wp_enqueue_script('wpdf-admin', WPDF()->get_plugin_url() . 'assets/admin/js/main.js', array('jquery-ui-draggable', 'jquery-ui-sortable'), WPDF()->get_version());
	}

	function wpdf_register_pages(){

		$forms = WPDF()->get_forms();

		$admin_slug = "wpdf-forms";
		add_menu_page("WP Form", "Forms", "manage_options", $admin_slug, array( $this, 'wpdf_form_page'), 'dashicons-feedback', 30 );

		if(!empty($forms)) {
			foreach ( $forms as $form_id => $form ) {
				add_submenu_page( $admin_slug, $form->getName(), $form->getName(), "manage_options", 'admin.php?page=wpdf-forms&form=' . $form_id );
			}
		}
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

	function wpdf_form_page(){

		echo '<div class="wrap wpdf">';

		$form = isset($_GET['form']) ? wpdf_get_form($_GET['form']) : false;
		$submission_id = isset($_GET['submission']) ? $_GET['submission'] : false;
		$action = isset($_GET['action']) ? $_GET['action'] : false;

		if( $action == 'new' || ( $action == 'manage' && $form ) ){

			$this->display_manage_form($form);

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
		<h1>Submission</h1>
		<?php echo '<a href="'. admin_url('admin.php?page=wpdf-forms&form='.$form->getName()).'" class="button">Back</a>'; ?>

		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">

				<div id="post-body-content">

					<div class="postbox">
						<div class="inside">
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
							?>
						</div>
					</div>
				</div>

				<div id="postbox-container-1" class="postbox-container">
					<div class="postbox">
						<h2 class="hndle"><span>Information</span></h2>
						<div class="inside">
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

			</div><!-- /post-body -->
			<br class="clear">
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

		echo '<h1 style="display: block; float:left;">Form: ' . $form->getName() . '</h1>';

		require 'class-wpdf-submissions-list-table.php';
		$wpdf_submissions_table = new WPDF_Submissions_List_Table( $form );
		$wpdf_submissions_table->prepare_items();

		echo $wpdf_submissions_table->search_box('Search Entries', 'wpdf-search');

		echo '<div style="clear:both"></div>';
		echo '</form>';

		$wpdf_submissions_table->display();
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

	private function display_manage_form($form = false){
		?>
		<div class="wpdf-form-manager">

			<div class="wpdf-header">

			</div>
			<div class="wpdf-cols">
				<div class="wpdf-left">
					<div class="wpdf-left__inside">
						<div class="wpdf-fields">
							<ul id="sortable">
								<li class="placeholder">Drop field here to add to the form</li>
							</ul>
						</div>
					</div>

				</div>
				<div class="wpdf-right">
					<div class="wpdf-right__inside">
						<div class="wpdf-panel wpdf-panel--active">
							<div class="wpdf-panel__header">
								<p class="wpdf-panel__title">Available Fields</p>
							</div>
							<div class="wpdf-panel__content">
								<ul class="wpdf-field-list">
									<li class="draggable ui-state-highlight" data-field="text"><a href="#">Text</a></li>
									<li class="draggable ui-state-highlight" data-field="textarea"><a href="#">Textarea</a></li>
									<li class="draggable ui-state-highlight" data-field="dropdown"><a href="#">Dropdown</a></li>
									<li class="draggable ui-state-highlight" data-field="checkbox"><a href="#">Checkbox</a></li>
									<li class="draggable ui-state-highlight" data-field="radio"><a href="#">Radio</a></li>
								</ul>
							</div>
						</div>
					</div>

				</div>
			</div>

			<div class="wpdf-clear"></div>
		</div>

		<div id="field-placeholder" style="display:none;">
			<div class="wpdf-panel" data-field-type="text">
				<div class="wpdf-panel__header">
					Field: Text
				</div>
				<div class="wpdf-panel__content">
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolorem enim esse illum laboriosam nemo nulla, placeat tempore! Ea eius est illum incidunt nihil. Cupiditate eaque enim esse harum, quo reprehenderit?</p>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolorem enim esse illum laboriosam nemo nulla, placeat tempore! Ea eius est illum incidunt nihil. Cupiditate eaque enim esse harum, quo reprehenderit?</p>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolorem enim esse illum laboriosam nemo nulla, placeat tempore! Ea eius est illum incidunt nihil. Cupiditate eaque enim esse harum, quo reprehenderit?</p>
				</div>
			</div>
			<div class="wpdf-panel" data-field-type="textarea">
				<div class="wpdf-panel__header">
					Field: Textarea
				</div>
				<div class="wpdf-panel__content">
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolorem enim esse illum laboriosam nemo nulla, placeat tempore! Ea eius est illum incidunt nihil. Cupiditate eaque enim esse harum, quo reprehenderit?</p>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolorem enim esse illum laboriosam nemo nulla, placeat tempore! Ea eius est illum incidunt nihil. Cupiditate eaque enim esse harum, quo reprehenderit?</p>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolorem enim esse illum laboriosam nemo nulla, placeat tempore! Ea eius est illum incidunt nihil. Cupiditate eaque enim esse harum, quo reprehenderit?</p>
				</div>
			</div>
			<div class="wpdf-panel" data-field-type="dropdown">
				<div class="wpdf-panel__header">
					Field: Dropdown
				</div>
				<div class="wpdf-panel__content">
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolorem enim esse illum laboriosam nemo nulla, placeat tempore! Ea eius est illum incidunt nihil. Cupiditate eaque enim esse harum, quo reprehenderit?</p>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolorem enim esse illum laboriosam nemo nulla, placeat tempore! Ea eius est illum incidunt nihil. Cupiditate eaque enim esse harum, quo reprehenderit?</p>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolorem enim esse illum laboriosam nemo nulla, placeat tempore! Ea eius est illum incidunt nihil. Cupiditate eaque enim esse harum, quo reprehenderit?</p>
				</div>
			</div>
			<div class="wpdf-panel" data-field-type="checkbox">
				<div class="wpdf-panel__header">
					Field: Checkbox
				</div>
				<div class="wpdf-panel__content">
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolorem enim esse illum laboriosam nemo nulla, placeat tempore! Ea eius est illum incidunt nihil. Cupiditate eaque enim esse harum, quo reprehenderit?</p>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolorem enim esse illum laboriosam nemo nulla, placeat tempore! Ea eius est illum incidunt nihil. Cupiditate eaque enim esse harum, quo reprehenderit?</p>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolorem enim esse illum laboriosam nemo nulla, placeat tempore! Ea eius est illum incidunt nihil. Cupiditate eaque enim esse harum, quo reprehenderit?</p>
				</div>
			</div>
			<div class="wpdf-panel" data-field-type="radio">
				<div class="wpdf-panel__header">
					Field: Radio
				</div>
				<div class="wpdf-panel__content">
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolorem enim esse illum laboriosam nemo nulla, placeat tempore! Ea eius est illum incidunt nihil. Cupiditate eaque enim esse harum, quo reprehenderit?</p>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolorem enim esse illum laboriosam nemo nulla, placeat tempore! Ea eius est illum incidunt nihil. Cupiditate eaque enim esse harum, quo reprehenderit?</p>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolorem enim esse illum laboriosam nemo nulla, placeat tempore! Ea eius est illum incidunt nihil. Cupiditate eaque enim esse harum, quo reprehenderit?</p>
				</div>
			</div>
		</div>

		<style>
			.wpdf-form-manager{
				border:1px solid #dedede;
			}
			.wpdf-header{
				height: 70px;
				background: #FFF;
				width: 100%;
				border-bottom:1px solid #dedede;
			}
			.wpdf-clear{
				clear:both;
			}
			.wpdf-cols{
				display: table;
			}
			.wpdf-left, .wpdf-right{
				/*float:left;*/
				min-height: 500px;
				display: table-cell;
			}
			.wpdf-left{
				background: #f7f7f7;
				width: 70%;
			}
			.wpdf-right{
				background: #ececec;
				width: 30%;
			}

			/**/
			.wpdf-panel{
				background: #f7f7f7;
			}
			.wpdf-panel--active .wpdf-panel__content{
				display: block;
			}
			.wpdf-panel__header{
				padding:15px;
				border-bottom:1px solid #ececec;
			}
			.wpdf-panel__title{
				font-size:14px;
				margin: 0;
				padding: 0;
			}
			.wpdf-panel__content{
				display: none;
				margin: 7px;
				overflow: hidden;
			}

			.wpdf-right__inside, .wpdf-left__inside{
				margin: 20px;
			}

			.wpdf-field-list{
				margin: 0 0 7px;
				padding:0;
				list-style: none;
				overflow: hidden;
			}
			.wpdf-field-list li{
				margin: 0;
				padding: 0;
				width: 50%;
				float:left;
			}
			.wpdf-field-list a{
				background: #ececec;
				padding: 15px 0;
				margin: 7px;
				display: block;
				text-align: center;
				text-decoration: none;
				color: #444444;
			}

			/**/

			#sortable {
				list-style-type: none;
				margin: 0;
				padding: 0;
				min-height: 50px;
			}
			#sortable li {
				margin:5px 0;
			}
			/*.ui-state-default{
				background: #999;
				width: 100%;
				position: relative;
			}*/
			/*.wpdf-fields .ui-state-highlight {
				background: #999999;
				height: 20px;
				width: 100%;
			}*/
			
			/**
			**/
			.wpdf-fields .wpdf-panel{
				background: #ffffff;
			}
			.wpdf-fields .wpdf-panel__header{
				border: 1px solid #ececec;
			}
			/*.wpdf-fields .placeholder{
				border: 1px dashed #999999;
				padding: 15px;
			}*/

			/*The element used to show the future position of the item currently being sorted.*/
			/*.ui-sortable-placeholder{
				margin: 10px;
				background: red;
			}*/

			/*The element shown while dragging a sortable item. The element actually used depends on the*/
			.ui-sortable-helper{
				background: yellow;
				padding: 0;
				margin:0;
			}


			/*The handle of each sortable item, specified using the handle option. By default, each sortable item itself is also the handle.*/
			/*.ui-sortable-handle{
				margin: 10px;
				background: green;
			}*/

			/*The sortable element.*/
			.ui-sortable{
				margin: 10px;
				background: none;
			}

			/*.wpdf-dropped-item{
				margin: 10px;
				background: cyan;
			}*/

			.sortable-placeholder{
				height: 30px;
				border: 1px dashed #b4b9be;
			}

		</style>
		<?php
	}
}

new WPDF_Admin();