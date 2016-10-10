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
	}

	function enqueue_scripts(){
		wp_enqueue_script('wpdf-admin', WPDF()->get_plugin_url() . 'assets/admin/js/wpdf.min.js', array('jquery-ui-draggable', 'jquery-ui-sortable'), WPDF()->get_version());
		wp_enqueue_style('wpdf-admin', WPDF()->get_plugin_url() . 'assets/admin/css/wpdf.min.css', array(), WPDF()->get_version());
	}

	function wpdf_register_pages(){

		$forms = WPDF()->get_forms();

		$admin_slug = "wpdf-forms";
		add_menu_page("WP Form", "Forms", "manage_options", $admin_slug, array( $this, 'wpdf_form_page'), 'dashicons-feedback', 30 );
		add_submenu_page( $admin_slug, 'New Form', 'Add Form', 'manage_options', 'admin.php?page=wpdf-forms&action=new');

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

		if(isset($_POST['wpdf-action'])){

			switch($_POST['wpdf-action']){
				case 'edit-form':

					// process data
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

	/**
	 * @param string $form
	 */
	private function display_manage_form($form = null){

		$available_fields = array('text', 'textarea', 'dropdown', 'checkbox', 'radio');

		?>
		<form action="" method="post">

			<input type="hidden" name="wpdf-action" value="edit-form" />
			<input type="hidden" name="wpdf-form" value="<?php echo $form; ?>" />
		<div class="wpdf-form-manager">

			<div class="wpdf-header">
				<div class="wpdf-form-actions">
					<input type="submit" value="Update" />
				</div>
			</div>
			<div class="wpdf-cols">
				<div class="wpdf-left">
					<div class="wpdf-left__inside">
						<div class="wpdf-fields">
							<ul id="sortable">
<!--								<li class="placeholder">Drop field here to add to the form</li>-->

								<li class="ui-state-highlight ui-draggable ui-draggable-handle wpdf-dropped-item" data-field="text" style="width: auto; height: auto; right: auto; bottom: auto;">
									<?php $this->display_field_panel('dropdown', true); ?>
								</li>

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
									<?php foreach($available_fields as $field): ?>
									<li class="draggable ui-state-highlight" data-field="<?php echo $field; ?>"><a href="#"><?php echo ucfirst($field); ?></a></li>
									<?php endforeach; ?>
								</ul>
							</div>
						</div>
					</div>

				</div>
			</div>

			<div class="wpdf-clear"></div>
		</div>
		</form>

		<div id="field-placeholder" style="display:none;">
			<?php
			foreach($available_fields as $field){
				$this->display_field_panel($field);
			}
			?>
		</div>
		<?php
	}

	private function display_field_panel($field, $active = false){
		?>
		<div class="wpdf-panel <?php echo $active == true ? 'wpdf-panel--active' : ''; ?>" data-field-type="<?php echo $field; ?>">
			<div class="wpdf-panel__header">
				Field: <?php echo ucfirst($field); ?>
			</div>
			<div class="wpdf-panel__content">

				<?php
				// general fields
				?>
				<div class="wpdf-field-row">
					<div class="wpdf-col wpdf-col__half">
						<label for="" class="wpdf-label">Name</label>
						<input type="text" class="wpdf-input" name="field[][name]">
					</div>
				</div>
				<div class="wpdf-field-row">
					<div class="wpdf-col wpdf-col__half">
						<label for="" class="wpdf-label">Label</label>
						<input type="text" class="wpdf-input" name="field[][label]">
					</div>
					<div class="wpdf-col wpdf-col__half">
						<label for="" class="wpdf-label">Placeholder</label>
						<input type="text" class="wpdf-input" name="field[][placeholder]">
					</div>
				</div>

				<div class="wpdf-field-row">
					<div class="wpdf-col wpdf-col__half">
						<label for="" class="wpdf-label">Order</label>
						<input type="text" class="wpdf-input" name="field[][order]">
					</div>
				</div>

				<?php
				// specific fields based on field type
				switch($field):
					case 'text':
						?>
				<div class="wpdf-field-row">
					<div class="wpdf-col wpdf-col__half">
						<label for="" class="wpdf-label">Default Value</label>
						<input type="text" class="wpdf-input" name="field[][default]">
					</div>
				</div>
						<?php
						break;
					case 'textarea':
						?>
						<div class="wpdf-field-row">
							<div class="wpdf-col wpdf-col__full">
								<label for="" class="wpdf-label">Default Value</label>
								<textarea class="wpdf-input" name="field[][default]"></textarea>
							</div>
						</div>
						<?php
						break;
					case 'dropdown':
					case 'radio':
					case 'checkbox':
						?>
						<div class="wpdf-field-row">
							<div class="wpdf-col wpdf-col__full">

								<strong>Values</strong>

								<table width="100%">
									<thead>
									<tr>
										<th>Name</th>
										<th>Value</th>
										<th>Default?</th>
										<th>_</th>
									</tr>
									</thead>
									<tbody class="wpdf-repeater" data-template-name="field_value_repeater">
									<tr class="wpdf-repeater-row wpdf-repeater__template">
										<td><input title="Name" type="text" class="wpdf-input" name="field[][value_labels][]"></td>
										<td><input title="Value" type="text" class="wpdf-input" name="field[][value_keys][]"></td>
										<td><input title="Default?" type="checkbox" name="field[][value_default][]"></td>
										<td>
											<a href="#" class="wpdf-add-row button">+</a>
											<a href="#" class="wpdf-del-row button">-</a>
										</td>
									</tr>
									</tbody>
								</table>

							</div>
						</div>
						<?php
						break;
				endswitch;
				// add-on fields
				?>
				<div class="wpdf-clear"></div>
			</div>
		</div>
		<?php
	}
}

new WPDF_Admin();