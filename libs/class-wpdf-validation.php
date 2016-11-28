<?php
/**
 * Form Validation
 *
 * @package WPDF
 * @author James Collings
 * @created 04/08/2016
 */

/**
 * Class WPDF_Validation
 */
class WPDF_Validation {

	/**
	 * Error message
	 *
	 * @var string
	 */
	protected $_error = null;

	/**
	 * Field rules
	 *
	 * @var array
	 */
	protected $_rules = array();

	/**
	 * WPDF_Validation constructor.
	 *
	 * @param array $rules List of rules.
	 */
	public function __construct( $rules = array() ) {
		$this->_rules = $rules;
	}

	/**
	 * Validate field data
	 *
	 * @param WPDF_FormField $field Form field.
	 * @param array          $post_data Form post data.
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function validate( $field, $post_data ) {

		// hook into field validation.
		$error = apply_filters( 'wpdf/validate_field', null, $field->get_name(), $post_data );
		if ( ! empty( $error ) ) {
			$this->_error = $error;

			return false;
		}

		if ( isset( $this->_rules[ $field->get_name() ] ) ) {
			if ( ! empty( $this->_rules[ $field->get_name() ] ) ) {
				foreach ( $this->_rules[ $field->get_name() ] as $rule ) {
					$type = isset( $rule['type'] ) ? $rule['type'] : false;
					$args = array();

					if ( is_array( $type ) && count( $type ) > 0 ) {

						// validation type is first element in array, args follow after.
						$t    = array_shift( $type );
						$args = $type;
						$type = $t;
					}

					switch ( $type ) {
						case 'required':
							if ( ! $this->is_required( $field->get_name(), $post_data ) ) {
								$this->set_error( $rule );

								return false;
							}
							break;
						case 'email':
							if ( ! $this->is_valid_email( $field->get_name(), $post_data ) ) {
								$this->set_error( $rule );

								return false;
							}
							break;
						case 'min_length':

							// throw error if no argumments passed.
							if ( 0 === count( $args ) ) {
								throw new Exception( __( 'No argument for minimum length validation', 'wpdf' ) );
							}

							if ( ! $this->is_min_length( $field->get_name(), $args[0], $post_data ) ) {
								$this->set_error( $rule );

								return false;
							}
							break;
						case 'max_length':

							// throw error if no argumments passed.
							if ( 0 === count( $args ) ) {
								throw new Exception( __( 'No argument for maximum length validation', 'wpdf' ) );
							}

							if ( ! $this->is_max_length( $field->get_name(), $args[0], $post_data ) ) {
								$this->set_error( $rule );

								return false;
							}
							break;
						case 'unique':

							$form = WPDF()->get_current_form();
							if ( ! $this->is_unique( $form->get_name(), $field->get_name(), $post_data ) ) {
								$this->set_error( $rule );

								return false;
							}
							break;
					}
				}
			}
		}

		return true;
	}

	/**
	 * Get validation error message
	 *
	 * @return string
	 */
	public function get_error() {
		return $this->_error;
	}

	/**
	 * Get upload error
	 *
	 * @param int $code Upload error code.
	 * @param int $max_upload_size Max upload size.
	 *
	 * @return mixed|string
	 */
	public function get_upload_error( $code, $max_upload_size = - 1 ) {

		if ( $max_upload_size < 0 ) {
			$max_upload_size = intval( ini_get( 'upload_max_filesize' ) );
		}

		if ( WP_DEBUG ) {

			switch ( $code ) {
				case UPLOAD_ERR_INI_SIZE:
					$message = WPDF()->text->get( 'ini_size', 'upload' );
					break;
				case UPLOAD_ERR_FORM_SIZE:
					$message = WPDF()->text->get( 'form_size', 'upload' );
					break;
				case UPLOAD_ERR_PARTIAL:
					$message = WPDF()->text->get( 'partial', 'upload' );
					break;
				case UPLOAD_ERR_NO_FILE:
					$message = WPDF()->text->get( 'no_file', 'upload' );
					break;
				case UPLOAD_ERR_NO_TMP_DIR:
					$message = WPDF()->text->get( 'no_tmp_dir', 'upload' );
					break;
				case UPLOAD_ERR_CANT_WRITE:
					$message = WPDF()->text->get( 'cant_write', 'upload' );
					break;
				case UPLOAD_ERR_EXTENSION:
					$message = WPDF()->text->get( 'extension', 'upload' );
					break;
				default:
					$message = WPDF()->text->get( 'unknown', 'upload' );
					break;
			}

			return $message;

		} else {

			if ( UPLOAD_ERR_INI_SIZE === $code ) {
				$message = sprintf( WPDF()->text->get( 'max_size', 'upload' ), $max_upload_size );
			} else {
				$message = WPDF()->text->get( 'general', 'upload' );
			}

			return $message;
		}
	}

	/**
	 * Set error
	 *
	 * @param array $rule Validation rule.
	 */
	protected function set_error( $rule ) {

		// default error message.
		$error = '';

		$type = $rule['type'];
		$args = array();

		if ( is_array( $type ) ) {
			$temp = array_shift( $type );
			$args = $type;
			$type = $temp;
		}

		// check for custom error message in rule
		// todo: vsprintf need to check amount of arguments with string, so no errors will be thrown.
		if ( isset( $rule['msg'] ) ) {
			$error = vsprintf( $rule['msg'], $args );
		} elseif ( WPDF()->text->get( $type, 'validation' ) ) {
			$error = vsprintf( WPDF()->text->get( $type, 'validation' ), $args );
		}

		// save error for later.
		$this->_error = $error;
	}

	/**
	 * Required Field
	 *
	 * @param string $field Field id.
	 * @param array  $post_data Form post data.
	 *
	 * @return bool
	 */
	protected function is_required( $field, $post_data ) {

		if ( isset( $post_data[ $field ] ) && ! empty( $post_data[ $field ] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Valid email address
	 *
	 * @param string $field Field id.
	 * @param array  $post_data Form post data.
	 *
	 * @return bool
	 */
	protected function is_valid_email( $field, $post_data ) {

		// escape early as data is not found.
		if ( ! isset( $post_data[ $field ] ) || empty( $post_data[ $field ] ) ) {
			return true;
		}

		if ( is_email( $post_data[ $field ] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Minimum character requirement
	 *
	 * @param string $field Field id.
	 * @param int    $length Minimim length.
	 * @param array  $post_data Form post data.
	 *
	 * @return bool
	 */
	public function is_min_length( $field, $length, $post_data ) {

		// escape early as data is not found.
		if ( ! isset( $post_data[ $field ] ) || empty( $post_data[ $field ] ) ) {
			return true;
		}

		if ( strlen( $post_data[ $field ] ) >= $length ) {
			return true;
		}

		return false;
	}

	/**
	 * Maximum character requirement
	 *
	 * @param string $field Field id.
	 * @param int    $length Maximum length.
	 * @param array  $post_data Form post data.
	 *
	 * @return bool
	 */
	public function is_max_length( $field, $length, $post_data ) {

		// escape early as data is not found.
		if ( ! isset( $post_data[ $field ] ) || empty( $post_data[ $field ] ) ) {
			return true;
		}

		if ( strlen( $post_data[ $field ] ) <= $length ) {
			return true;
		}

		return false;
	}

	/**
	 * Is unique entry
	 *
	 * @param string $form_id Field id.
	 * @param string $field Field to search.
	 * @param array  $post_data Form post data.
	 *
	 * @return bool
	 */
	public function is_unique( $form_id, $field, $post_data ) {

		$db = new WPDF_DatabaseManager();
		if ( $db->is_data_unique( $form_id, $field, $post_data[ $field ] ) ) {
			return true;
		}

		return false;
	}
}
