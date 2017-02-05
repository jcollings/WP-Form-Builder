<?php
/**
 * Database manager
 *
 * @package WPDF
 * @author James Collings
 * @created 06/08/2016
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class WPDF_DatabaseManager
 */
class WPDF_DatabaseManager {

	/**
	 * Submission table
	 *
	 * @var string
	 */
	protected $_submission_table = null;

	/**
	 * Submission data table
	 *
	 * @var string
	 */
	protected $_submission_data_table = null;

	/**
	 * WPDF_DatabaseManager constructor.
	 */
	public function __construct() {

		global $wpdb;
		$this->_submission_table      = "{$wpdb->prefix}wpdf_submissions";
		$this->_submission_data_table = "{$wpdb->prefix}wpdf_submission_data";
	}

	/**
	 * Save form submission in database
	 *
	 * @param string        $name Form name.
	 * @param WPDF_FormData $data Form data.
	 *
	 * @return bool
	 */
	public function save_entry( $name, $data ) {

		global $wpdb;

		$values = $data->to_array();

		$this->save_submission( $name, $data->get_ip() );
		$submission_id = $wpdb->insert_id;
		foreach ( $values as $field_id => $value ) {

			$field = $data->get_field( $field_id );
			if ( $field->is_type( 'password' ) ) {
				// dont store password data in entry table.
				continue;
			}

			$temp_val = $value;
			if ( $field->is_type( 'file' ) ) {

				// Get root upload directory, append on upload session dir and file.
				$upload_folder = $data->get_upload_folder();
				$temp_val = trailingslashit( $upload_folder ) . $value;
			}
			$this->save_submission_data( $submission_id, $field, $temp_val );
		}

		// save virtual fields, hidden data the user doesn't see.
		$virtual_fields = apply_filters( 'wpdf/save_virtual_fields', array(), $name );
		if ( is_array( $virtual_fields ) && ! empty( $virtual_fields ) ) {
			foreach ( $virtual_fields as $field => $value ) {
				$virtual_field = new WPDF_FormField( $field, 'virtual' );
				$this->save_submission_data( $submission_id, $virtual_field, $value );
			}
		}

		return true;
	}

	/**
	 * Flag submission as deleted
	 *
	 * @param int    $entry_id Submission id.
	 * @param string $active Database active flag.
	 *
	 * @return false|int
	 */
	public function delete_entry( $entry_id, $active = 'N' ) {

		global $wpdb;

		return $wpdb->update(
			$this->_submission_table,
			array(
				'active' => $active,
			),
			array(
				'id' => $entry_id,
			),
			array( '%d' ),
			array( '%d' )
		);
	}

	/**
	 * Save submission details
	 *
	 * @param string $form Form name.
	 * @param string $ip User ip.
	 *
	 * @return false|int
	 */
	protected function save_submission( $form, $ip ) {

		$created = date( 'Y-m-d H:i:s' );
		$user_id = null;
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
		}

		global $wpdb;
		$query = $wpdb->prepare( "INSERT INTO {$this->_submission_table}(`form`,`ip`,`created`, `user_id`) VALUES(%s, %s, %s, %d)", array(
			$form,
			$ip,
			$created,
			$user_id,
		) );

		return $wpdb->query( $query );
	}

	/**
	 * Save submission field data
	 *
	 * @param int            $submission_id Submission id.
	 * @param WPDF_FormField $field Form field.
	 * @param string         $content Submitted data.
	 *
	 * @return false|int
	 */
	protected function save_submission_data( $submission_id, $field, $content ) {

		$created = date( 'Y-m-d H:i:s' );

		$content = is_array( $content ) ? implode( ',', $content ) : $content;

		// don't store data if empty content.
		if ( '' === $content ) {
			return false;
		}

		$field_label = $field->get_label();
		$field_type  = $field->get_type();
		$field_name  = $field->get_name();

		global $wpdb;
		$query = $wpdb->prepare( "INSERT INTO {$this->_submission_data_table}(`submission_id`, `field`, `content`, `created`, `field_type`, `field_label`) VALUES(%d, %s, %s, %s, %s, %s)", array(
			$submission_id,
			$field_name,
			$content,
			$created,
			$field_type,
			$field_label,
		) );

		return $wpdb->query( $query );
	}

	/**
	 * Get total amount of form submissions
	 *
	 * @param string $form_id Form id.
	 * @param string $search Search string.
	 *
	 * @return array
	 */
	public function get_form_count( $form_id, $search = '' ) {

		global $wpdb;

		$search_join  = '';
		$search_where = '';
		if ( ! empty( $search ) ) {

			$search_join  = " INNER JOIN {$this->_submission_data_table} sdt1 ON st.id = sdt1.submission_id";
			$search_where = sprintf( " AND sdt1.content LIKE '%%%%%s%%%%'", $search );
		}

		$query = $wpdb->prepare( "SELECT COUNT(DISTINCT st.id) FROM {$this->_submission_table} as st {$search_join} WHERE form=%s AND active='Y' {$search_where}", $form_id );

		return $wpdb->get_col( $query );
	}

	/**
	 * Get form submissions
	 *
	 * @param string $form_id Form id.
	 * @param int    $paged Current page.
	 * @param int    $per_page Posts per page.
	 * @param string $order_by Field to order by.
	 * @param string $order Order by DESC or ASC.
	 * @param string $search Search string.
	 *
	 * @return array|null|object
	 */
	public function get_form_submissions( $form_id, $paged, $per_page, $order_by, $order, $search = '' ) {
		global $wpdb;

		$order_str = '';
		if ( ! empty( $order_by ) && ! empty( $order ) ) {
			$order_str = ' ORDER BY st.' . $order_by . ' ' . $order;
		}

		$search_join  = '';
		$search_where = '';
		if ( ! empty( $search ) ) {

			$search_join  = " INNER JOIN {$this->_submission_data_table} sdt1 ON st.id = sdt1.submission_id";
			$search_where = sprintf( " AND sdt1.content LIKE '%%%%%s%%%%'", $search );
		}

		$offset = ( $paged - 1 ) * $per_page;
		$query  = $wpdb->prepare( "SELECT DISTINCT st.id, st.* FROM {$this->_submission_table} as st {$search_join} WHERE form=%s AND active='Y'" . $search_where . $order_str . " LIMIT %d, %d", $form_id, $offset, $per_page );

		return $wpdb->get_results( $query );
	}

	/**
	 * Get last submitted form data
	 *
	 * @param string $form_id Form id.
	 *
	 * @return bool
	 */
	public function get_form_last_entry( $form_id ) {

		global $wpdb;
		$query = $wpdb->prepare( "SELECT * FROM {$this->_submission_table} WHERE form=%s AND active='Y' ORDER BY created DESC LIMIT 1", $form_id );

		return $wpdb->get_row( $query );
	}

	/**
	 * Get submission by id
	 *
	 * @param int $submission_id Submission id.
	 *
	 * @return array|null|object
	 */
	public function get_submission( $submission_id ) {

		global $wpdb;
		$query = $wpdb->prepare( "SELECT * FROM {$this->_submission_table} st LEFT JOIN {$this->_submission_data_table} sdt ON st.id = sdt.submission_id WHERE st.id=%d", $submission_id );
		return $wpdb->get_results( $query );
	}

	/**
	 * Get submission details by id
	 *
	 * @param int $submission_id Submission id.
	 *
	 * @return array|null|object
	 */
	public function get_submission_detail( $submission_id ) {

		global $wpdb;
		$query = $wpdb->prepare( "SELECT * FROM {$this->_submission_table} st WHERE st.id=%d", $submission_id );
		return $wpdb->get_row( $query );
	}

	/**
	 * Get submission data by id
	 *
	 * @param int $submission_id Submission id.
	 *
	 * @return array|null|object
	 */
	public function get_submission_data( $submission_id ) {

		global $wpdb;
		$query = $wpdb->prepare( "SELECT * FROM {$this->_submission_data_table} sdt WHERE sdt.submission_id=%d", $submission_id );
		return $wpdb->get_results( $query );
	}

	/**
	 * Mark submission as read
	 *
	 * @param int $submission_id Submission id.
	 * @param int $state Mark as read.
	 *
	 * @return false|int
	 */
	public function mark_as_read( $submission_id, $state = 1 ) {

		global $wpdb;
		return $wpdb->update( $this->_submission_table, array( 'is_read' => $state ), array( 'id' => $submission_id ), array( '%d' ) );
	}

	/**
	 * Get total amount of unread submissions
	 *
	 * @param string $form_id  Name of form.
	 *
	 * @return array
	 */
	public function get_form_unread_count( $form_id ) {

		global $wpdb;
		$query = $wpdb->prepare( "SELECT COUNT(*) FROM {$this->_submission_table} WHERE form=%s AND active='Y' AND is_read=%d", $form_id, 0 );
		return $wpdb->get_col( $query );
	}

	/**
	 * Check if field data is unique
	 *
	 * @param string $form_id  Name of form.
	 * @param string $field  Field Name.
	 * @param string $value  Value to compare.
	 *
	 * @return bool
	 */
	public function is_data_unique( $form_id, $field, $value ) {

		global $wpdb;
		$query = $wpdb->prepare( "SELECT COUNT(*) FROM {$this->_submission_table} as st INNER JOIN {$this->_submission_data_table} as sdt ON st.id = sdt.submission_id WHERE st.form=%s AND sdt.field=%s AND sdt.content=%s AND active='Y'", $form_id, $field, $value );
		$count = $wpdb->get_var( $query );
		if ( intval( $count ) > 0 ) {
			return false;
		}
		return true;
	}

	/**
	 * Create and Upgrade plugin database
	 */
	public function install() {

		$db_version = intval( get_option( 'wpdf_db', 0 ) );

		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		if ( $db_version < 1 ) {

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
			update_option( 'wpdf_db', 1 );
		}

		if ( $db_version < 2 ) {
			if ( $wpdb->query( "ALTER TABLE {$this->_submission_table} ADD `is_read` INT(1) NOT NULL DEFAULT '0' AFTER `form`" ) ) {
				update_option( 'wpdf_db', 2 );
			}
		}

		if ( $db_version < 3 ) {

			$query = new WP_Query( array(
				'post_type' => 'wpdf_form',
				'posts_per_page' => -1,
			) );

			if ( $query->have_posts() ) {
				foreach ( $query->posts as $post ) {

					$content = maybe_unserialize( $post->post_content ) ? unserialize( $post->post_content ) : array();
					update_post_meta( $post->ID, '_form_data', $content );
				}
			}

			update_option( 'wpdf_db', 3 );
		}
	}
}
