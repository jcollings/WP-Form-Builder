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

		if(!empty($forms)) {
			foreach ( $forms as $form_id => $form ) {

				$row            = new stdClass();
				$row->form_name = $form->getName();

				$row->ID = $form->getId();

				$count             = $db->get_form_count( $form_id );
				$row->form_entries = $count[0];

				$unread_count = $db->get_form_unread_count($form_id);
				$row->unread_count = $unread_count[0];

				$last_entry_data = $db->get_form_last_entry( $form_id );
				$row->last_entry = 'N/A';
				if ( $last_entry_data ) {
					$row->last_entry = $last_entry_data->created;
				}

				$this->items[] = $row;
			}
		}
	}

	public function get_columns(){

		return $columns= array(
			'col_form_name'=>__('Name', 'wpdf'),
			'col_form_entries' => __('Entries', 'wpdf'),
			'col_form_last' => __('Last Entry', 'wpdf'),
		);

	}

	public function display_rows() {

		list( $columns, $hidden ) = $this->get_column_info();

		foreach($this->items as $item){

			$linkParam = $formParam = '&form=' . $item->form_name;
			if(isset($item->ID)){
				$formParam = '&form_id=' . $item->ID;
				$linkParam = '&form=WPDF_FORM_' . $item->ID;
			}

			echo '<tr>';

			foreach($columns as $column_name => $column_display_name){

				switch($column_name){
					case 'col_form_name':


						$link = admin_url('admin.php?page=wpdf-forms&action=entries' . $linkParam );
						$entry_str = '<strong><a href="'. $link .'">' . $item->form_name . '</a></strong>';
						$del_link = admin_url('admin.php?page=wpdf-forms&action=delete' . $formParam );

						$links = array();

						if(isset($item->ID)){
							$links[] = '<span class="edit"><a href="'.admin_url('admin.php?page=wpdf-forms&action=manage&form_id=' . $item->ID).'" aria-label="View">Edit</a></span>';
							$links[] = '<span class="edit"><a href="'.admin_url('admin.php?page=wpdf-forms&action=settings&form_id=' . $item->ID).'" aria-label="Settings">Settings</a></span>';
							$links[] = '<span class="edit"><a href="'.admin_url('admin.php?page=wpdf-forms&action=notifications&form_id=' . $item->ID).'" aria-label="Notifications">Notifications</a></span>';
							$links[] = '<span class="edit"><a href="'.$link.'" aria-label="View">View</a></span>';
							$links[] = '<span class="delete"><a href="'.$del_link.'" aria-label="Delete">Delete</a></span>';
						}

						echo '<td>';
						echo $entry_str;
						if(!empty($links)){
							echo '<div class="row-actions">' . implode( ' | ', $links ) .'</div>';
						}
						echo '</td>';
						break;
					case 'col_form_entries':
						echo sprintf('<td>%d <strong>(%d unread)</strong></td>', $item->form_entries, $item->unread_count);
						break;
					case 'col_form_last':
						echo '<td>' . date( 'M j, Y @ H:i', strtotime($item->last_entry)) .'</td>';
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
