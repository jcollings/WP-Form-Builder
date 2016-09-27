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
	}

	function wpdf_register_pages(){

		$forms = WPDF()->get_forms();

		$admin_slug = "wpdf-forms";
		add_menu_page("WP Form", "Forms", "manage_options", $admin_slug, array( $this, 'wpdf_form_page'), 'dashicons-feedback', 30 );

		foreach($forms as $form_id => $form){
			add_submenu_page( $admin_slug , $form->getName(), $form->getName(), "manage_options", 'admin.php?page=wpdf-forms&form='.$form_id);
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
			'slug' => 'wpdf/wpdf.php', // this is the slug of your plugin
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
		$view = isset($_GET['view']) ? $_GET['view'] : false;
		$submission_id = isset($_GET['submission']) ? $_GET['submission'] : false;
		if($form) {

			if($submission_id){

				echo '<h1>Submission</h1>';
				echo '<a href="'. admin_url('admin.php?page=wpdf-forms&form='.$form->getName()).'">Back</a>';
				$db = new WPDF_DatabaseManager();
				$submissions = $db->get_submission($submission_id);

				foreach($submissions as $submission ){
					$content = esc_html($submission->content);
					echo "<p><strong>{$form->getFieldLabel($submission->field, $submission->field_label)}</strong>:<br />{$content}</p>";
				}

			}else{

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

		}else{
			echo '<h1>Forms</h1>';

			require 'class-wpdf-forms-list-table.php';
			$wpdf_forms_table = new WPDF_Forms_List_Table();
			$wpdf_forms_table->prepare_items();

			$wpdf_forms_table->display();
		}

		echo '</div>';

	}
}

new WPDF_Admin();