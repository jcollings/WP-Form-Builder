<?php
/**
 * Core Admin Class
 *
 * @package WPDF/Admin
 * @author James Collings
 * @created 06/08/2016
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class WPDF_Admin
 */
class WPDF_Admin {

	/**
	 * Messages
	 *
	 * @var array
	 */
	protected $_messages = array();

	/**
	 * Error messages
	 *
	 * @var array
	 */
	protected $_errors = array();

	/**
	 * If form has been saved
	 *
	 * @var int
	 */
	protected $_success = 0;

	/**
	 * If upgrade is available
	 *
	 * @var bool
	 */
	protected $_upgrade = false;

	/**
	 * WPDF_Admin constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'wpdf_register_pages' ) );
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'init', array( $this, 'register_form_post_type' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// mce editor.
		add_filter( 'mce_external_plugins', array( $this, 'enqueue_mce_scripts' ) );
		add_filter( 'mce_buttons', array( $this, 'register_mce_buttons' ) );
		add_action( 'wp_ajax_wpdf_insert_dialog', array( $this, 'mce_insert_dialog' ) );
	}

	/**
	 * Enqueue Js and Css files
	 */
	function enqueue_scripts() {
		$version = WPDF()->get_version();
		$ext     = '.min';
		if ( ( defined( 'WP_DEBUG' ) && WP_DEBUG ) || ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ) {
			$version = time();
			$ext     = '';
		}

		wp_enqueue_script( 'tiptip', WPDF()->get_plugin_url() . 'assets/admin/js/jquery-tipTip' . $ext . '.js', array(), '1.3' );
		wp_enqueue_script( 'wpdf-admin', WPDF()->get_plugin_url() . 'assets/admin/js/wpdf' . $ext . '.js', array(
			'jquery-ui-draggable',
			'jquery-ui-sortable',
			'tiptip',
			'iris',
		), $version );
		wp_enqueue_style( 'wpdf-admin', WPDF()->get_plugin_url() . 'assets/admin/css/wpdf' . $ext . '.css', array(), $version );
	}

	/**
	 * Load tiny mce insert form dialog
	 */
	function mce_insert_dialog() {

		require WPDF()->get_plugin_dir() . 'templates/admin/mce-insert-dialog.php';
		die();
	}

	/**
	 * Register tiny mce button
	 *
	 * @param array $buttons List of existing tiny mce buttons.
	 *
	 * @return array
	 */
	function register_mce_buttons( $buttons ) {
		$buttons[] = 'wpdf';

		return $buttons;
	}

	/**
	 * Enqueue Js files for tiny mce
	 *
	 * @param array $plugins Loaded tiny mce plugins array.
	 *
	 * @return mixed
	 */
	function enqueue_mce_scripts( $plugins ) {

		$ext = '.min';
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$ext = '';
		}

		$plugins['wpdf_form_btn'] = WPDF()->get_plugin_url() . 'assets/admin/js/editor-shortcode' . $ext . '.js';

		return $plugins;
	}

	/**
	 * Register WPDF Admin pages.
	 */
	function wpdf_register_pages() {

		$admin_slug   = 'wpdf-forms';
		$capabilities = apply_filters( 'wpdf/admin_capabilities', 'manage_options' );
		add_menu_page( 'WP Form Builder', 'Forms', $capabilities, $admin_slug, array(
			$this,
			'wpdf_form_page',
		), 'dashicons-feedback', 30 );
		add_submenu_page( $admin_slug, 'New Form', 'Add Form', $capabilities, 'admin.php?page=wpdf-forms&action=new' );
		add_submenu_page( $admin_slug, 'Settings', 'Settings', $capabilities, 'admin.php?page=wpdf-settings', array( $this, 'display_plugin_settings' ) );
	}

	/**
	 * On Admin init, process admin actions before view is loaded.
	 */
	function init() {

		// Check for database update.
		$current_db_version = get_option( 'wpdf_db', 0 );
		if ( $current_db_version < WPDF()->get_db_version() ) {
			$this->_upgrade = true;
		}

		$action = isset( $_GET['action'] ) ? sanitize_title( $_GET['action'] ) : false;
		if ( 'upgrade' === $action ) {
			$db = new WPDF_DatabaseManager();
			$db->install();
			wp_redirect( admin_url( '/admin.php?page=wpdf-forms' ) );
			exit();
		}

		$delete_submission = isset( $_GET['delete_submission'] ) ? $_GET['delete_submission'] : false;
		if ( $delete_submission ) {

			$db = new WPDF_DatabaseManager();
			$db->delete_entry( $delete_submission );

			$url = remove_query_arg( 'delete_submission' );
			wp_redirect( $url );
			exit();
		}

		// preview form.
		$preview = isset( $_GET['action'] ) && $_GET['action'] == 'preview-form' && isset( $_GET['form_id'] ) ? intval( $_GET['form_id'] ) : 0;
		if ( $preview > 0 ) {
			$post = get_post( $preview );
			if ( 'wpdf_form' === $post->post_type ) {
				$preview_id    = wp_generate_password( 12, false );
				$transient_key = sprintf( 'wpdf_preview_%s', $preview_id );
				set_transient( $transient_key, array(
					'form_id' => $preview,
				), HOUR_IN_SECONDS );
				wp_redirect( site_url( '/?wpdf_preview=' . $preview_id ) );
				exit();
			}
		}

		// delete form.
		$delete_form = isset( $_GET['action'] ) && $_GET['action'] == 'delete-form' && isset( $_GET['form_id'] ) ? intval( $_GET['form_id'] ) : 0;
		if ( $delete_form > 0 ) {

			// todo: Alert user if form was not deleted.
			$deleted = wp_delete_post( $delete_form );

			wp_redirect( admin_url( 'admin.php?page=wpdf-forms' ) );
			exit();
		}

		$this->_success = isset($_GET['success']) && intval($_GET['success']) >= 1 ? intval($_GET['success']) : false;

		if ( isset( $_POST['wpdf-action'] ) ) {

			switch ( $_POST['wpdf-action'] ) {
				case 'create-form':

					$this->create_form();
					break;
				case 'edit-form-fields':

					$this->save_form_fields();
					break;
				case 'edit-form-notifications':

					$this->save_form_notifications();
					break;
				case 'edit-form-settings':

					$this->save_form_settings();
					break;
				case 'edit-form-style':
					$this->save_form_style();
					break;
			}
		}
	}

	/**
	 * Register WPDF post type
	 */
	function register_form_post_type() {
		$args = array(
			'public'   => false,
			'supports' => array(
				'title',
				'editor',
				'author',
				'revisions',
			),
		);
		register_post_type( 'wpdf_form', $args );
	}

	/**
	 * Display admin content, loads individual views
	 */
	function wpdf_form_page() {

		if($this->_upgrade){
			?>
			<div class="update-nag">
				WP Form Builder Database upgrade is required! <a href="<?php echo esc_url( admin_url( '/admin.php?page=wpdf-forms&action=upgrade' ) ); ?>" aria-label="Please update WPDF database now">Please upgrade now</a>.
			</div>
			<?php
			return;
		}

		echo '<div class="wrap wpdf">';

		$form    = isset( $_GET['form'] ) ? wpdf_get_form( $_GET['form'] ) : false;
		$form_id = isset( $_GET['form_id'] ) ? intval( $_GET['form_id'] ) : false;

		if ( ! $form && intval( $form_id ) > 0 ) {
			$form = new WPDF_DB_Form( $form_id );
		}

		// get submission id, if found load into form data
		$submission_id = isset( $_GET['submission'] ) ? $_GET['submission'] : false;
		if($submission_id){
			$form->load_submission( $submission_id );
		}

		$action        = isset( $_GET['action'] ) ? $_GET['action'] : false;

		if ( 'new' === $action ) {

			$this->display_create_form();

		} elseif ( 'manage' === $action && $form_id ) {

			$this->display_manage_form( $form );

		} elseif ( 'notifications' === $action && $form_id ) {

			$this->display_notifications_form( $form );

		} elseif ( 'settings' === $action  && $form_id ) {

			$this->display_settings_form( $form );

		} elseif ( 'style' === $action && $form_id ) {

			$this->display_style_form( $form );

		} elseif ( $form ) {

			if ( $submission_id ) {

				$this->display_submission_page( $form, $submission_id );
			} else {

				$this->display_submissions_archive( $form );
			}
		} else {

			$this->display_forms_archive();
		}

		echo '</div>';

	}

	/**
	 * Add error message
	 *
	 * @param string $msg Error Message.
	 */
	public function add_error( $msg ) {
		$this->_errors[] = $msg;
	}

	/**
	 * Check if there are errors
	 *
	 * @return bool
	 */
	public function has_errors() {
		if ( ! empty( $this->_errors ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get error messages array
	 *
	 * @return array
	 */
	public function get_errors() {
		return $this->_errors;
	}

	/**
	 * Get success flag
	 *
	 * @return int
	 */
	public function get_success() {
		return $this->_success;
	}

	/**
	 * Display view submission page
	 *
	 * @param WPDF_Form $form Form object.
	 * @param int       $submission_id Submission id.
	 */
	private function display_submission_page( $form, $submission_id ) {

		?>

		<div class="wpdf-form-manager">

			<?php $this->display_form_header( 'submissions', $form ); ?>

			<div class="wpdf-cols">

				<div class="wpdf-left">
					<div class="wpdf-left__inside">
						<?php

						$db          = new WPDF_DatabaseManager();
						$submissions = $db->get_submission_detail( $submission_id );
						$db->mark_as_read( $submission_id );

						// get user name.
						$user_id = isset( $submissions->user_id ) ? $submissions->user_id : 'N/A';
						$user    = 'Guest';
						if ( intval( $user_id ) > 0 ) {
							$user = get_user_by( 'id', intval( $user_id ) );
							if ( $user ) {
								$user = $user->data->user_login;
							}
						}

						// get ip.
						$ip = isset( $submissions->ip ) ? $submissions->ip : 'N/A';

						// date.
						$date = isset( $submissions->created ) ? $submissions->created : false;
						if ( $date ) {
							$date = date( 'M j, Y @ H:i', strtotime( $date ) );
						}

						$form_data = $form->get_data();
						$submissions = $form_data->to_array();
						if ( ! empty( $submissions ) ) {
							foreach ( $submissions as $field_id => $value ) {

								$field = $form_data->get_field($field_id);
								$label = $field->get_label();

								$value = apply_filters( 'wpdf/display_field_value', $value, $field_id, $form_data );
								if ( ! empty( $value ) ) {
									echo "<p><strong>{$label}</strong>:<br />{$value}</p>";
								}
							}
						}

						$url = 'form=' . $form->get_name();
						if ( $form->get_id() ) {
							$url = 'form_id=' . $form->get_id();
						}

						echo '<a href="' . admin_url( 'admin.php?page=wpdf-forms&' . $url ) . '" class="button">Back</a>';
						?>
					</div>
				</div>
				<div class="wpdf-right">

					<div class="wpdf-right__inside">
						<div class="wpdf-panel wpdf-panel--active">
							<div class="wpdf-panel__header">
								<p class="wpdf-panel__title">Information</p>
							</div>
							<div class="wpdf-panel__content">
								<?php if ( $date ) : ?>
									<div class="misc-pub-section curtime misc-pub-curtime">
										<span id="timestamp">Submitted on: <b><?php echo esc_html( $date ); ?></b></span>
									</div>
								<?php endif; ?>

								<div class="misc-pub-section misc-pub-visibility" id="visibility">
									From: <span id="post-visibility-display"><?php echo esc_html( $user ); ?> (<?php echo esc_html( $ip ); ?>
										)</span>
								</div>
							</div>
						</div>
					</div>

				</div>

			</div>

			<div class="wpdf-clear"></div>
		</div>
		<?php
	}

	/**
	 * Display submissions archive
	 *
	 * @param WPDF_Form $form Form object.
	 */
	private function display_submissions_archive( $form ) {

		echo '<form method="GET">';

		echo '<input type="hidden" name="page" value="wpdf-forms" />';
		echo '<input type="hidden" name="form" value="' . esc_attr( $form->get_name() ) . '" />';

		?>

		<div class="wpdf-form-manager">

			<?php $this->display_form_header( 'submissions', $form ); ?>

			<div class="wpdf-cols">

				<div class="wpdf-full">

					<div class="wpdf-table-archive">

						<?php
						require 'class-wpdf-submissions-list-table.php';
						$wpdf_submissions_table = new WPDF_Submissions_List_Table( $form );
						$wpdf_submissions_table->prepare_items();

						echo $wpdf_submissions_table->search_box( 'Search Entries', 'wpdf-search' );

						echo '<div style="clear:both"></div>';

						$wpdf_submissions_table->display();
						?>
					</div>

				</div>

			</div>

			<div class="wpdf-clear"></div>
		</div>
		<?php

		echo '</form>';
	}

	/**
	 * Display forms archive
	 */
	private function display_forms_archive() {
		$url = admin_url( 'admin.php?page=wpdf-forms&action=new' );
		echo '<h1>Forms <a href="' . esc_attr( $url ) . '" class="button">Add New</a></h1>';

		require 'class-wpdf-forms-list-table.php';
		$wpdf_forms_table = new WPDF_Forms_List_Table();
		$wpdf_forms_table->prepare_items();

		$wpdf_forms_table->display();
	}

	/**
	 * Display forms edit header
	 *
	 * @param string    $active Active header item.
	 * @param WPDF_Form $form Form object.
	 */
	private function display_form_header( $active = 'fields', $form ) {

		require WPDF()->get_plugin_dir() . 'templates/admin/settings-header.php';
	}

	/**
	 * Display form notifications page
	 *
	 * @param WPDF_DB_Form|bool $form Form object.
	 */
	private function display_notifications_form( $form = false ) {

		require WPDF()->get_plugin_dir() . 'templates/admin/page-form-notifications.php';
	}

	/**
	 * Display form settings page
	 *
	 * @param WPDF_DB_Form|bool $form Form object.
	 */
	private function display_settings_form( $form = false ) {

		require WPDF()->get_plugin_dir() . 'templates/admin/page-form-settings.php';

	}

	/**
	 * Display form styling page
	 *
	 * @param WPDF_DB_Form|bool $form Form object.
	 */
	private function display_style_form( $form = false ) {
		require WPDF()->get_plugin_dir() . 'templates/admin/page-form-style.php';
	}

	/**
	 * Display form fields page
	 *
	 * @param WPDF_DB_Form|bool $form Form object.
	 */
	private function display_manage_form( $form = false ) {

		require WPDF()->get_plugin_dir() . 'templates/admin/page-form-fields.php';
	}

	/**
	 * Display create form screen
	 */
	private function display_create_form() {

		require WPDF()->get_plugin_dir() . 'templates/admin/page-form-create.php';
	}

	/**
	 * Display plugin settings screen
	 */
	public function display_plugin_settings() {

		require WPDF()->get_plugin_dir() . 'templates/admin/page-settings.php';
	}

	/**
	 * Display form field panel
	 *
	 * @param WPDF_FormField|string $field Form field object.
	 * @param WPDF_Form             $form Form object.
	 * @param bool                  $active open or close field panel.
	 */
	private function display_field_panel( $field, $form = null, $active = false ) {

		if ( is_string( $field ) ) {

			switch ( $field ) {
				case 'text':
					$field = new WPDF_TextField( '', $field );
					break;
				case 'textarea':
					$field = new WPDF_TextareaField( '', $field );
					break;
				case 'select':
					$field = new WPDF_SelectField( '', $field );
					break;
				case 'radio':
					$field = new WPDF_RadioField( '', $field );
					break;
				case 'checkbox':
					$field = new WPDF_CheckboxField( '', $field );
					break;
				case 'file':
					$field = new WPDF_FileField( '', $field );
					break;
				case 'number':
					$field = new WPDF_NumberField( '', $field );
					break;
				default:
					$field = new WPDF_FormField( '', $field );
					break;
			}
		}

		require WPDF()->get_plugin_dir() . 'templates/admin/field-panel.php';
	}

	/**
	 * Load field class
	 *
	 * @param string $field_type field type.
	 * @param array  $args field arguments.
	 *
	 * @return WPDF_FormField
	 */
	private function load_field( $field_type, $args = array() ) {

		$field_name = isset( $args['name'] ) ? $args['name'] : '';

		switch ( $field_type ) {
			case 'text':
				return new WPDF_TextField( $field_name, $field_type, $args );
			case 'textarea':
				return new WPDF_TextareaField( $field_name, $field_type, $args );
			case 'select':
				return new WPDF_SelectField( $field_name, $field_type, $args );
			case 'radio':
				return new WPDF_RadioField( $field_name, $field_type, $args );
			case 'checkbox':
				return new WPDF_CheckboxField( $field_name, $field_type, $args );
			case 'file':
				return new WPDF_FileField( $field_name, $field_type, $args );
			case 'number':
				return new WPDF_NumberField( $field_name, $field_type, $args );
			default:
				return new WPDF_FormField( $field_name, $field_type, $args );
		}
	}

	/**
	 * Save form settings
	 */
	private function save_form_settings() {

		$form_id   = intval( $_POST['wpdf-form'] );
		$form_data = get_post_meta( $form_id, '_form_data', true );

		$section = isset($_GET['setting']) ? $_GET['setting'] : '';
		$modules = WPDF()->get_modules();
		$url_prepend = '';

		if ( ! empty( $modules ) && isset( $modules[ $section ] ) ){

			$module = $modules[ $section ];
			if ( method_exists( $module, 'save_settings' ) ) {
				$form_data['settings'][ $module->get_setting_key() ] = $module->save_settings();
			}

			$url_prepend = '&setting=' . sanitize_title( $section );

		} else {
			$settings = $_POST['wpdf_settings'];

			// save form settings array.
			if ( ! isset( $form_data['settings'] ) ) {
				$form_data['settings'] = array();
			}

			$form_data['content'] = isset( $settings['form_content'] ) ? $settings['form_content'] : '';

			$form_data['settings'] = array(
				'labels' => array(
					'submit' => $settings['submit_label'],
				),
			);

			// save form confirmations array.
			if ( ! isset( $form_data['confirmations'] ) ) {
				$form_data['confirmations'] = array();
			}

			$form_data['confirmations'] = array(
				array(
					'type'         => $settings['confirmation_type'],
					'redirect_url' => $settings['confirmation_redirect'],
					'message'      => $settings['confirmation_message'],
				),
			);
			// save how confirmation should display.
			$form_data['confirmation_location'] = isset( $settings['confirmation_location'] ) && in_array( $settings['confirmation_location'], array(
				'replace',
				'after',
			), true ) ? $settings['confirmation_location'] : 'after';

			if ( ! empty( $settings['form_label'] ) ) {
				$form_data['form_label'] = $settings['form_label'];
			}

			if ( isset( $settings['enable_style'] ) ) {
				$form_data['settings']['enable_style'] = $settings['enable_style'];
			} else {
				$form_data['settings']['enable_style'] = 'enabled';
			}

			if ( isset( $settings['enable_layout_css'] ) ) {
				$form_data['settings']['enable_layout_css'] = $settings['enable_layout_css'];
			} else {
				$form_data['settings']['enable_layout_css'] = 'enabled';
			}

			// settings error.
			if ( isset( $settings['error'] ) ) {

				$form_data['settings']['error'] = array();

				if ( isset( $settings['error']['general_message'] ) ) {
					$form_data['settings']['error']['general_message'] = $settings['error']['general_message'];
				}

				if ( isset( $settings['error']['show_fields'] ) ) {
					$form_data['settings']['error']['show_fields'] = $settings['error']['show_fields'];
				}
			}

			// save recaptcha settings.
			if ( isset( $settings['recaptcha_public'] ) ) {
				$form_data['settings']['recaptcha_public'] = $settings['recaptcha_public'];
			}
			if ( isset( $settings['recaptcha_private'] ) ) {
				$form_data['settings']['recaptcha_private'] = $settings['recaptcha_private'];
			}
		}

		update_post_meta( $form_id , '_form_data', $form_data );
		wp_redirect( admin_url( 'admin.php?page=wpdf-forms&action=settings&form_id=' . $form_id . '&success=1' . $url_prepend ) );
		exit();
	}

	/**
	 * Save form notifications
	 */
	private function save_form_notifications() {

		$form_id   = intval( $_POST['wpdf-form'] );
		$form_data = $form_data = get_post_meta( $form_id, '_form_data', true );

		$form_data['notifications'] = array();
		foreach ( $_POST['notification'] as $i => $notification ) {
			$form_data['notifications'][ $i ] = array(
				'to'      => $notification['to'],
				'subject' => $notification['subject'],
				'message' => $notification['message'],
				'from'    => $notification['from'],
				'cc'      => $notification['cc'],
				'bcc'     => $notification['bcc'],
			);
		}

		update_post_meta( $form_id , '_form_data', $form_data );
		wp_redirect( admin_url( 'admin.php?page=wpdf-forms&action=notifications&form_id=' . $form_id . '&success=1' ) );
		exit();
	}

	/**
	 * Save form style
	 */
	private function save_form_style() {

		$form_id   = intval( $_POST['wpdf-form'] );
		$form_data = get_post_meta( $form_id, '_form_data', true );

		$style    = isset( $_POST['wpdf_style'] ) ? $_POST['wpdf_style'] : array();
		$disabled = isset( $_POST['wpdf_style_disable'] ) ? $_POST['wpdf_style_disable'] : array();

		// todo: load defaults from form->theme.
		$default = array(
			'form_bg_colour'            => '',
			'form_text_colour'          => '',
			'form_bg_error_colour'      => '#c63838',
			'form_text_error_colour'    => '#ffffff',
			'form_bg_success_colour'    => '#80cd5f',
			'form_text_success_colour'  => '#ffffff',
			'field_label_bg_colour'     => '',
			'field_label_text_colour'   => '',
			'field_input_bg_colour'     => '',
			'field_input_text_colour'   => '',
			'field_error_text_colour'   => '#c63838',
			'field_border_colour'       => '',
			'field_error_border_colour' => '#c63838',
			'button_bg_colour'          => '#000000',
			'button_text_colour'        => '#ffffff',
			'button_hover_bg_colour'    => '#333333',
			'button_hover_text_colour'  => '#ffffff',
			'checkbox_text_colour'      => '',
		);

		if ( ! isset( $form_data['theme'] ) ) {
			$form_data['theme'] = array();
		}
		if ( ! isset( $form_data['theme_disabled'] ) ) {
			$form_data['theme_disabled'] = array();
		}

		foreach ( $default as $key => $val ) {
			$form_data['theme'][ $key ]          = isset( $style[ $key ] ) ? $style[ $key ] : $default[ $key ];
			$form_data['theme_disabled'][ $key ] = in_array( $key, $disabled, true ) ? true : false;
		}

		update_post_meta( $form_id , '_form_data', $form_data );
		wp_redirect( admin_url( 'admin.php?page=wpdf-forms&action=style&form_id=' . $form_id . '&success=1' ) );
		exit();
	}

	/**
	 * Create form with given name, then redirect to manage fields screen
	 *
	 * @return void
	 */
	private function create_form() {

		$name = sanitize_text_field( $_POST['form-name'] );
		if ( empty( $name ) ) {
			$this->add_error( 'Please enter a name before creating the form.' );
			return;
		}

		// add default form data.
		$form_data = array(
			'form_label'    => $name,
			'fields'        => array(),
			'settings'      => array(
				'labels' => array(
					'submit' => 'Submit',
				),
			),
			'confirmations' => array(
				array(
					'type'    => 'message',
					'message' => 'The form has been submitted successfully',
				),
			),
			'notifications' => array(
				array(
					'to'      => '{{admin_email}}',
					'subject' => 'New Submission from {{form_name}}',
					'message' => '{{fields}}',
				),
			),
		);

		$postarr = array(
			'post_title'   => $name,
			'post_type'    => 'wpdf_form',
			'post_status'  => 'publish',
			'post_content' => '',
		);

		$post = wp_insert_post( $postarr, true );
		if ( ! is_wp_error( $post ) && update_post_meta( $post , '_form_data', $form_data ) ) {
			wp_redirect( admin_url( 'admin.php?page=wpdf-forms&action=manage&form_id=' . $post ) );
			exit();
		} else {
			$this->add_error( 'An error occured when creating the form.' );
		}
	}

	/**
	 * Save form fields
	 */
	private function save_form_fields() {

		$form_id  = null;
		$old_form = null;

		if ( intval( $_POST['wpdf-form'] ) > 0 ) {
			$form_id = intval( $_POST['wpdf-form'] );
			$type    = get_post_type( $form_id );
			if ( 'wpdf_form' !== $type ) {
				wp_redirect( admin_url( 'admin.php?page=wpdf-forms&action=new' ) );
				exit();
			}
		}

		$fields = array();

		if ( isset( $_POST['field'] ) && ! empty( $_POST['field'] ) ) {

			// fetch list of existing field ids.
			$field_ids = array();
			foreach ( $_POST['field'] as $arr ) {
				if ( isset( $arr['id'] ) && ! empty( $arr['id'] ) ) {
					$field_ids[] = $arr['id'];
				}
			}

			foreach ( $_POST['field'] as $field ) {

				// get or randomly generate field id, should only generate on creating field.
				$field_id = isset( $field['id'] ) && ! empty( $field['id'] ) ? $field['id'] : false;
				if ( ! $field_id ) {

					// generate field id from label.
					$field_id = $base_name = sanitize_title( $field['label'] );
					$c        = 1;
					while ( empty( $field_id ) || in_array( $field_id, $field_ids, true ) ) {
						$field_id = sprintf( '%s-%d', $base_name, $c );
						$c ++;
					}
					$field_ids[] = $field_id;
				}

				// load field.
				$field_class         = $this->load_field( $field['type'], $field );
				$fields[ $field_id ] = $field_class->save( $field );
			}
		}

		$postarr = array(
			'post_type'   => 'wpdf_form',
			'post_status' => 'publish',
		);

		// update existing post.
		$form_data = get_post_meta( $form_id, '_form_data', true );

		if ( ! is_array( $form_data ) ) {
			$form_data = array();
		}

		$form_data['fields'] = $fields;

		update_post_meta( $form_id , '_form_data', $form_data );
		wp_redirect( admin_url( 'admin.php?page=wpdf-forms&action=manage&form_id=' . $form_id . '&success=1' ) );
		exit();
	}
}

new WPDF_Admin();
