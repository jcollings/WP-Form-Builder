<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 06/08/2016
 * Time: 20:45
 */

if(!class_exists('WP_List_Table')){
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WPDF_Submissions_List_Table extends WP_List_Table{

	protected $form_id = null;

	public function __construct($form) {

		$this->form_id = $form->getName();

		parent::__construct( array(
			'singular' => 'wpdf_submissions_list',
			'plural' => 'wpdf_submissions_lists',
			'ajax' => false,
			'screen' => 'wpdf_submissions'
		) );
	}

	public function prepare_items() {

		global $_wp_column_headers;

		$screen = get_current_screen();
		$db = new WPDF_DatabaseManager();
		$count = $db->get_form_count($this->form_id);

		$totalitems = intval($count[0]);
		$perpage = 10;
		$totalpages = ceil($totalitems/$perpage);

		$paged = isset($_GET['paged']) ? intval($_GET['paged']) : 1;

		$orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'ASC';
		$order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : '';

		$submissions = $db->get_form_submissions($this->form_id, $paged, $perpage, $orderby, $order);

		$this->set_pagination_args( array(
			"total_items" => $totalitems,
			"total_pages" => $totalpages,
			"per_page" => $perpage,
		) );

		$columns = $this->get_columns();
		$_wp_column_headers[$screen->id]=$columns;

		$this->items = $submissions;
	}

	public function get_columns(){

		return $columns= array(
			'col_entry_id'=>__('ID'),
			'col_submitted_date' => __('Submitted')
		);

	}

	public function get_sortable_columns() {
		return $sortable = array(
			'col_submitted_date'=>'created',
		);
	}

	public function display_rows() {

		list( $columns, $hidden ) = $this->get_column_info();

		foreach($this->items as $item){

			echo '<tr>';

			foreach($columns as $column_name => $column_display_name){

				switch($column_name){
					case 'col_entry_id':
						$link = admin_url('admin.php?page=wpdf-forms&submission=' . $item->id . '&form='.$this->form_id);
						$del_link = admin_url('admin.php?page=wpdf-forms&delete_submission=' . $item->id . '&form='.$this->form_id);
						echo '<td>
							<a href="'.$link.'">Entry: ' . $item->id . '</a>
							<div class="row-actions">
								<span class="edit"><a href="'.$link.'" aria-label="View">View</a></span> | <span class="delete"><a href="'.$del_link.'" aria-label="View">Delete</a></span>
							</div>
							</td>';
						break;
					case 'col_user':
						echo '<td>' . $item->user_id . '</td>';
						break;
					case 'col_submitted_date':
						echo '<td>' . $item->created . '</td>';
						break;
					default:
						echo '<td></td>';
						break;
				}
			}

			echo '</tr>';
		}
	}
}
