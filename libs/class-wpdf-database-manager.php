<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 06/08/2016
 * Time: 14:36
 */

class WPDF_DatabaseManager{

	protected $_submission_table = null;
	protected $_submission_data_table = null;

	public function __construct() {

		global $wpdb;
		$this->_submission_table = "{$wpdb->prefix}wpdf_submissions";
		$this->_submission_data_table = "{$wpdb->prefix}wpdf_submission_data";
	}

	/**
	 * Save form submission in database
	 *
	 * @param $name string
	 * @param $data WPDF_FormData
	 *
	 * @return bool
	 */
	public function save_entry($name, $data){

		global $wpdb;

		$values = $data->toArray();

		$this->save_submission($name, $data->getIp());
		$submission_id = $wpdb->insert_id;
		foreach($values as $field_id => $value){

			$field = $data->getField($field_id);
			if($field->isType("password")){
				// dont store password data in entry table
				continue;
			}

			$temp_val = $value;
			if($field->isType("file")){
				// if file, add url to file instead of just filename
				$temp_val = wpdf_get_uploads_url() . $value;
			}
			$this->save_submission_data($submission_id, $field, $temp_val);
		}

		// save virtual fields, hidden data the user doesn't see
		$virtual_fields = apply_filters('wpdf/save_virtual_fields', array(), $name);
		if(is_array($virtual_fields) && !empty($virtual_fields)){
			foreach($virtual_fields as $field => $value){
				$virtual_field = new WPDF_FormField($field, 'virtual');
				$this->save_submission_data($submission_id, $virtual_field, $value);
			}
		}

		return true;
	}

	/**
	 * Flag submission as deleted
	 *
	 * @param $entry_id
	 * @param string $active
	 *
	 * @return false|int
	 */
	public function delete_entry($entry_id, $active = 'N'){

		global $wpdb;
		return $wpdb->update(
			$this->_submission_table,
			array(
				'active' => $active
			),
			array(
				'id' => $entry_id
			),
			array('%d'),
			array('%d')
		);
	}

	/**
	 * Save submission details
	 *
	 * @param $form
	 * @param $ip
	 *
	 * @return false|int
	 */
	protected function save_submission($form, $ip){

		$created = date('Y-m-d H:i:s');
		$user_id = null;
		if(is_user_logged_in()){
			$user_id = get_current_user_id();
		}

		global $wpdb;
		$query = $wpdb->prepare("INSERT INTO {$this->_submission_table}(`form`,`ip`,`created`, `user_id`) VALUES(%s, %s, %s, %d)", array($form, $ip, $created, $user_id));
		return $wpdb->query($query);
	}

	/**
	 * Save submission field data
	 *
	 * @param $submission_id int
	 * @param $field WPDF_FormField
	 * @param $content mixed
	 *
	 * @return false|int
	 */
	protected function save_submission_data($submission_id, $field, $content){

		$created = date('Y-m-d H:i:s');

		$content = is_array($content) ? implode(",", $content) : $content;

		// dont store data if empty content
		if($content === ''){
			return false;
		}

		$field_label = $field->getLabel();
		$field_type = $field->getType();
		$field_name = $field->getName();

		global $wpdb;
		$query = $wpdb->prepare("INSERT INTO {$this->_submission_data_table}(`submission_id`, `field`, `content`, `created`, `field_type`, `field_label`) VALUES(%d, %s, %s, %s, %s, %s)", array($submission_id, $field_name, $content, $created, $field_type, $field_label) );
		return $wpdb->query($query);
	}

	/**
	 * Get total amount of form submissions
	 *
	 * @param $form_id
	 * @param string $search
	 *
	 * @return array
	 */
	public function get_form_count($form_id, $search = ''){

		global $wpdb;

		$search_join = '';
		$search_where = '';
		if(!empty($search)){

			$search_join = " INNER JOIN {$this->_submission_data_table} sdt1 ON st.id = sdt1.submission_id";
			$search_where = sprintf(" AND sdt1.content LIKE '%%%%%s%%%%'", $search);
		}

		$query = $wpdb->prepare("SELECT COUNT(*) FROM {$this->_submission_table} as st {$search_join} WHERE form=%s AND active='Y' {$search_where}", $form_id);
		return $wpdb->get_col($query);
	}

	/**
	 * Get form submissions
	 *
	 * @param $form_id
	 * @param $paged
	 * @param $per_page
	 * @param $orderby
	 * @param $order
	 * @param string $search
	 *
	 * @return array|null|object
	 */
	public function get_form_submissions($form_id, $paged, $per_page, $orderby, $order, $search = ''){
		global $wpdb;

		$order_str = '';
		if(!empty($orderby) && !empty($order)){
			$order_str =' ORDER BY st.'.$orderby.' '.$order;
		}

		$search_join = '';
		$search_where = '';
		if(!empty($search)){

			$search_join = " INNER JOIN {$this->_submission_data_table} sdt1 ON st.id = sdt1.submission_id";
			$search_where = sprintf(" AND sdt1.content LIKE '%%%%%s%%%%'", $search);
		}

		$offset = ($paged-1)*$per_page;
		$query = $wpdb->prepare("SELECT st.* FROM {$this->_submission_table} as st {$search_join} WHERE form=%s AND active='Y'".$search_where .$order_str." LIMIT %d, %d", $form_id, $offset, $per_page);
		return $wpdb->get_results($query);
	}

	/**
	 * Get last submitted form data
	 *
	 * @param $form_id
	 *
	 * @return array|null|object|void
	 */
	public function get_form_last_entry($form_id){

		global $wpdb;
		$query = $wpdb->prepare("SELECT * FROM {$this->_submission_table} WHERE form=%s AND active='Y' ORDER BY created DESC LIMIT 1", $form_id);
		return $wpdb->get_row($query);
	}

	/**
	 * Get submission by id
	 *
	 * @param $submission_id
	 *
	 * @return array|null|object
	 */
	public function get_submission($submission_id){

		global $wpdb;

		// submission
		$query = $wpdb->prepare("SELECT * FROM {$this->_submission_table} st LEFT JOIN {$this->_submission_data_table} sdt ON st.id = sdt.submission_id WHERE st.id=%d", $submission_id);
		return $wpdb->get_results($query);
	}

	/**
	 * Mark submission as read
	 *
	 * @param $submission_id
	 * @param int $state
	 *
	 * @return false|int
	 */
	public function mark_as_read($submission_id, $state = 1){

		global $wpdb;
		return $wpdb->update($this->_submission_table, array('is_read' => $state), array('id' => $submission_id), array('%d'));
	}

	/**
	 * Get total amount of unread submissions
	 *
	 * @param $form_id string Name of form
	 *
	 * @return array
	 */
	public function get_form_unread_count($form_id){

		global $wpdb;
		$query = $wpdb->prepare( "SELECT COUNT(*) FROM {$this->_submission_table} WHERE form=%s AND active='Y' AND is_read=%d" , $form_id, 0);
		return $wpdb->get_col($query);
	}

	/**
	 * Create and Upgrade plugin database
	 */
	public function install(){

		$db_version = intval(get_option('wpdf_db', 0));

		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		if($db_version < 1) {

			$sql = "CREATE TABLE {$this->_submission_table}(
				id INT NOT NULL AUTO_INCREMENT,
			    form VARCHAR(50),
			    unread INT(1) DEFAULT 1,
			    ip VARCHAR(15),
			    user_id INT,
			    active CHAR(1) DEFAULT 'Y',
			    created DATETIME,
			    UNIQUE KEY id (id)
			) $charset_collate;";
			dbDelta( $sql );

			$sql = "CREATE TABLE {$this->_submission_data_table}(
				id INT NOT NULL AUTO_INCREMENT,
				submission_id INT,
			    field VARCHAR(50),
			    content TEXT,
			    field_type VARCHAR(50),
			    field_label VARCHAR(50),
			    created DATETIME,
			    UNIQUE KEY id (id)
			) $charset_collate;";
			dbDelta( $sql );
			update_option('wpdf_db', 1);
		}

		if($db_version < 2){
			if($wpdb->query("ALTER TABLE {$this->_submission_table} ADD `is_read` INT(1) NOT NULL DEFAULT '0' AFTER `form`")){
				update_option('wpdf_db', 2);
			}
		}
	}
}