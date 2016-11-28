<?php
/**
 * Class WPDF_FileField
 *
 * Add file field.
 */
class WPDF_FileField extends WPDF_FormField {

	/**
	 * Post max size
	 *
	 * @var null|string
	 */
	protected $_post_max_size = null;

	/**
	 * Upload max filesize
	 *
	 * @var null|string
	 */
	protected $_upload_max_filesize = null;

	/**
	 * Field upload limit
	 *
	 * @var int|null|string
	 */
	protected $_limit = 0;

	/**
	 * User set limit from field panel
	 *
	 * @var int $_max_file_size
	 */
	protected $_max_file_size = 0;

	/**
	 * Cvs of allowed file extensions
	 *
	 * @var string $_allowed_ext
	 */
	protected $_allowed_ext = '';

	/**
	 * WPDF_FileField constructor.
	 *
	 * @param string $name Field name.
	 * @param string $type Field type.
	 * @param array  $args Field arguments.
	 */
	public function __construct( $name, $type = '', $args = array() ) {
		parent::__construct( $name, $type, $args );

		// find upload limits.
		$this->_post_max_size       = ini_get( 'post_max_size' );
		$this->_upload_max_filesize = ini_get( 'upload_max_filesize' );
		$this->_limit               = $this->_post_max_size;
		if ( $this->_limit > $this->_upload_max_filesize ) {
			$this->_limit = $this->_upload_max_filesize;
		}

		$this->_max_file_size = isset( $args['max_file_size'] ) ? $args['max_file_size'] : $this->_limit;
		$this->_allowed_ext  = isset( $args['allowed_ext'] ) ? $args['allowed_ext'] : 'jpg,jpeg,png';

	}

	/**
	 * Get server limit
	 *
	 * @return int|null|string
	 */
	public function get_server_limit() {
		return $this->_limit;
	}

	/**
	 * Get post max size
	 *
	 * @return null|string
	 */
	public function get_post_max_size() {
		return $this->_post_max_size;
	}

	/**
	 * Get upload max filesize
	 *
	 * @return null|string
	 */
	public function get_upload_max_filesize() {
		return $this->_upload_max_filesize;
	}

	/**
	 * Get max filesize
	 *
	 * @return int|mixed|null|string
	 */
	public function get_max_filesize() {
		return $this->_max_file_size;
	}

	/**
	 * Get allowed ext
	 *
	 * @return mixed|string
	 */
	public function get_allowed_ext() {
		return $this->_allowed_ext;
	}

	/**
	 * Is valid extension
	 *
	 * @param array $filedata submitted filedata.
	 *
	 * @return bool
	 */
	public function is_valid_ext( $filedata ) {

		if ( empty( $this->get_allowed_ext() ) ) {
			return true;
		}

		$name       = $filedata['name'];
		$extensions = explode( ',', $this->get_allowed_ext() );
		$last_pos    = strrpos( $name, '.' );
		$ext        = substr( $name, $last_pos + 1 );
		if ( in_array( $ext, $extensions, true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Check to see if file is withing filesize limit
	 *
	 * @param array $filedata Submitted file data.
	 *
	 * @return bool
	 */
	public function is_allowed_size( $filedata ) {

		if ( isset( $filedata['size'] ) && intval( $filedata['size'] ) < $this->get_max_filesize() * 1024 * 1024 ) {
			return true;
		}

		return false;
	}

	/**
	 * Display field output on public form
	 *
	 * @param WPDF_FormData $form_data Form data to be output.
	 */
	public function output( $form_data ) {

		$value = $form_data->get( $this->_name );

		// display name of previously uploaded file and show the file uploader to allow users to overwrite upload.
		echo '<input type="' . $this->get_type() . '" name="' . $this->get_input_name() . '" />';
		if ( ! empty( $value ) ) {
			echo '<input type="hidden" name="' . $this->get_input_name() . '_uploaded" value="' . $value . '" />';
			echo sprintf( '<p class="wpdf-upload">Uploaded File: <span class="wpdf-upload__name">%s</span></p>', $value );
		}
	}

	/**
	 * Format field data to store in fields array
	 *
	 * @param array $field Field data.
	 *
	 * @return array
	 */
	public function save( $field = array() ) {

		$data = parent::save( $field );

		if ( isset( $field['max_file_size'] ) ) {

			// find upload limits.
			$post_max_size       = ini_get( 'post_max_size' );
			$upload_max_filesize = ini_get( 'upload_max_filesize' );
			$limit               = $post_max_size;
			if ( $limit > $upload_max_filesize ) {
				$limit = $upload_max_filesize;
			}

			if ( intval( $field['max_file_size'] ) > $limit || intval( $field['max_file_size'] ) < 0 ) {
				$data['max_file_size'] = $limit;
			} else {
				$data['max_file_size'] = intval( $field['max_file_size'] );
			}
		}
		if ( isset( $field['allowed_ext'] ) ) {
			$data['allowed_ext'] = $field['allowed_ext'];
		}

		return $data;
	}
}