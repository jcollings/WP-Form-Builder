<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 06/08/2016
 * Time: 20:35
 */

class WPDF_Admin{

	public function __construct() {
		add_action( 'admin_menu', array($this, 'wpdf_register_pages'));
	}

	function wpdf_register_pages(){
		add_menu_page("WP Form", "Forms", "manage_options", "wpdf-forms", array( $this, 'wpdf_form_page') );
	}

	function wpdf_form_page(){

		echo '<div class="wrap">';

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
					echo "<p><strong>{$form->getFieldLabel($submission->field, $submission->field_label)}</strong>:<br />{$submission->content}</p>";
				}

			}else{

				echo '<h1>Form: ' . $form->getName() . '</h1>';
				require 'class-wpdf-submissions-list-table.php';
				$wpdf_submissions_table = new WPDF_Submissions_List_Table( $form );
				$wpdf_submissions_table->prepare_items();

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