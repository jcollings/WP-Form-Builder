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

class WPDF_Forms_List_Table extends WP_List_Table{

	public function __construct() {
		parent::__construct( array(
			'singular' => 'wpdf_form_list',
			'plural' => 'wpdf_form_lists',
			'ajax' => false,
			'screen' => 'wpdf_forms'
		) );
	}

	public function prepare_items() {

		global $_wp_column_headers;

		$screen = get_current_screen();
		$forms = WPDF()->get_forms();

		$totalitems = $perpage = count($forms);
		$totalpages = 1;

		$this->set_pagination_args( array(
			"total_items" => $totalitems,
			"total_pages" => $totalpages,
			"per_page" => $perpage,
		) );

		$columns = $this->get_columns();
		$_wp_column_headers[$screen->id]=$columns;

		$this->items = array();

		$db = new WPDF_DatabaseManager();

		foreach($forms as $form_id => $form){

			$row = new stdClass();
			$row->form_name = $form_id;

			$count = $db->get_form_count($form_id);
			$row->form_entries = $count[0];

			$this->items[] = $row;
		}
	}

	public function get_columns(){

		return $columns= array(
			'col_form_name'=>__('Name'),
			'col_form_entries' => __('Entries')
		);

	}

	public function display_rows() {

		list( $columns, $hidden ) = $this->get_column_info();

		foreach($this->items as $item){

			echo '<tr>';

			foreach($columns as $column_name => $column_display_name){

				switch($column_name){
					case 'col_form_name':
						echo '<td><a href="'.admin_url('admin.php?page=wpdf-forms&form=' . $item->form_name).'">' . $item->form_name . '</a></td>';
						break;
					case 'col_form_entries':
						echo '<td>' . $item->form_entries . '</td>';
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
