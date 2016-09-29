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

	/**
	 * @var String form id
	 */
	protected $form_id = null;

	/**
	 * @var WPDF_Form
	 */
	protected $form = null;

	/**
	 * WPDF_Submissions_List_Table constructor.
	 *
	 * @param WPDF_Form $form
	 */
	public function __construct($form) {

		$this->form_id = $form->getName();
		$this->form = $form;

		parent::__construct( array(
			'singular' => 'wpdf_submissions_list',
			'plural' => 'wpdf_submissions_lists',
			'ajax' => false,
			'screen' => 'wpdf_submissions'
		) );
	}

	public function prepare_items() {

		global $_wp_column_headers;

		$orderby = !empty($_GET["orderby"]) ? sanitize_sql_orderby($_GET["orderby"]) : 'created';
		$order = !empty($_GET["order"]) && strtoupper($_GET['order']) == 'ASC' ? 'ASC' : 'DESC';
		$s = !empty($_GET["s"]) ? $_GET['s'] : '';

		$screen = get_current_screen();
		$db = new WPDF_DatabaseManager();
		$count = $db->get_form_count($this->form_id, $s);

		$totalitems = intval($count[0]);
		$perpage = 10;
		$totalpages = ceil($totalitems/$perpage);

		$paged = isset($_GET['paged']) ? intval($_GET['paged']) : 1;

		$submissions = $db->get_form_submissions($this->form_id, $paged, $perpage, $orderby, $order, $s);

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

		$columns = array(
			'col_entry_id' => __('ID', 'wpdf'),
		);

		$cols = $this->form->get_setting('admin_columns');
		if(!empty($cols)){
			foreach($cols as $field_id => $label){
				if(!isset($columns['col_' . $field_id])) {
					$columns[ 'col_' . $field_id ] = $label;
				}
			}
		}

		$columns['col_submitted_date'] =  __('Submitted', 'wpdf');
		return $columns;
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

						// visualize unread state
						$entry_str = sprintf('<strong><a href="%s">Entry: %s</a> - unread</strong>', $link, $item->id);
						if(1 == intval($item->is_read)){
							$entry_str = sprintf('<a href="%s">Entry: %s</a>', $link, $item->id);
						}

						echo '<td>
							'. $entry_str .'
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
						
						$col_key = substr($column_name, 4);
						$val = '';

						// load entry data from database if not already loaded
						if(!isset($item->data)){
							$db = new WPDF_DatabaseManager();
							$submission_data = $db->get_submission($item->id);
							foreach($submission_data as $v){

								if(!isset($item->data)){
									$item->data = new stdClass();
								}
								$item->data->{$v->field} = $v->content;
							}
						}


						if(isset($item->data->{$col_key})){
							$val = $item->data->{$col_key};
						}

						echo '<td>'.$val.'</td>';
						break;
				}
			}

			echo '</tr>';
		}
	}
}
