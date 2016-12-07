<?php
/**
 * Plugin Name: WP Form Builder
 * Plugin URI: https://www.wpformbuilder.com/
 * Description: Drag & Drop form builder plugin, custom notifications, custom confirmation message, manage form submissions online.
 * Version: 0.3.2
 * Author: James Collings
 * Author URI: https://www.jclabs.co.uk
 *
 * @package WP Form Builder
 * @author James Collings <james@jclabs.co.uk>
 * @link https://www.wpformbuilder.com/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class WPDF_DeveloperForms
 */
class WPDF_DeveloperForms {

	/**
	 * Plugin Version
	 *
	 * @var string
	 */
	protected $version = '0.3.2';

	/**
	 * Database version
	 *
	 * @var int
	 */
	protected $db_version = 3;

	/**
	 * Plugin Directory
	 *
	 * @var bool|string
	 */
	public $plugin_dir = false;

	/**
	 * Plugin Url
	 *
	 * @var bool|string
	 */
	public $plugin_url = false;

	/**
	 * Plugin slug
	 *
	 * @var bool|string
	 */
	protected $plugin_slug = false;

	/**
	 * Plugin Settings array
	 *
	 * @var bool
	 */
	protected $settings = false;

	/**
	 * List of all registered forms
	 *
	 * @var WPDF_Form[]
	 */
	protected $_forms = null;

	/**
	 * Active Form
	 *
	 * @var WPDF_Form
	 */
	protected $_form = null;

	/**
	 * Default plugin settings
	 *
	 * @var array
	 */
	private $default_settings = null;

	/**
	 * Plugin text
	 *
	 * @var WPDF_Text
	 */
	public $text = null;

	/**
	 * Single instance of class
	 *
	 * @var null
	 */
	protected static $_instance = null;

	/**
	 * Return current instance of class
	 *
	 * @return WPDF_DeveloperForms
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * WPDF_DeveloperForms constructor.
	 */
	public function __construct() {

		$this->plugin_dir = plugin_dir_path( __FILE__ );
		$this->plugin_url = plugins_url( '/', __FILE__ );
		$this->plugin_slug = basename( dirname( __FILE__ ) );

		$this->init();

		add_action( 'wp_loaded', array( $this, 'load_db_forms' ), 9 );
		add_action( 'wp_loaded', array( $this, 'process_form' ) );

		register_activation_hook( __FILE__, array( $this, 'on_activation' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Load all necessary files
	 */
	public function includes() {

		include_once 'libs/class-wpdf-form.php';
		include_once 'libs/class-wpdf-db-form.php';
		include_once 'libs/class-wpdf-form-data.php';
		include_once 'libs/class-wpdf-form-theme.php';
		include_once 'libs/class-wpdf-text.php';

		include_once 'libs/class-wpdf-form-field.php';
		include_once 'libs/fields/class-wpdf-text-field.php';
		include_once 'libs/fields/class-wpdf-textarea-field.php';
		include_once 'libs/fields/class-wpdf-select-field.php';
		include_once 'libs/fields/class-wpdf-radio-field.php';
		include_once 'libs/fields/class-wpdf-checkbox-field.php';
		include_once 'libs/fields/class-wpdf-file-field.php';

		include_once 'libs/class-wpdf-validation.php';
		include_once 'libs/class-wpdf-notification.php';
		include_once 'libs/class-wpdf-email-manager.php';
		include_once 'libs/class-wpdf-database-manager.php';

		include_once 'libs/wpdf-functions.php';
		include_once 'libs/wpdf-shortcodes.php';

		include_once 'libs/modules/class-wpdf-user-registration.php';

		if ( is_admin() ) {
			include_once 'libs/admin/wpdf-functions.php';
			include_once 'libs/admin/wpdf-admin.php';
		}
	}

	/**
	 * Initiate plugin
	 *
	 * @return void
	 */
	public function init() {

		$this->load_settings();
		$this->includes();

		// load text.
		$this->text = new WPDF_Text();

		// load preview.
		$preview = isset( $_GET['wpdf_preview'] ) && !empty( $_GET['wpdf_preview'] ) ? esc_attr($_GET['wpdf_preview']) : false;
		if ( $preview ) {
			include_once 'libs/class-wpdf-preview.php';
			new WPDF_Preview( $preview );
		}
	}

	/**
	 * Plugin Default Settings
	 *
	 * @return void
	 */
	public function default_settings() {

		$this->default_settings = array();
	}

	/**
	 * Load plugin settings
	 *
	 * @return void
	 */
	public function load_settings() {

		$this->default_settings();
		$this->settings = array();

		// load settings from db.
		foreach ( $this->default_settings as $key => $default ) {
			$data = get_option( 'wpdf_' . $key, $default );
			$this->settings[ $key ] = is_serialized( $data ) ? unserialize( $data ) : $data;
		}
	}

	/**
	 * Get plugin setting
	 *
	 * @param string $key setting key.
	 * @param bool   $default load default value.
	 *
	 * @return bool|mixed
	 */
	public function get_settings( $key, $default = false ) {

		if ( $default ) {
			return isset( $this->default_settings[ $key ] ) ? $this->default_settings[ $key ] : false;
		}

		return isset( $this->settings[ $key ] ) ? $this->settings[ $key ] : false;
	}

	/**
	 * Get plugin version
	 *
	 * @return string
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Get plugin slug
	 *
	 * @return bool|string
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Get plugin Url
	 *
	 * @return bool|string
	 */
	public function get_plugin_url() {
		return $this->plugin_url;
	}

	/**
	 * Get plugin path to directory
	 *
	 * @return bool|string
	 */
	public function get_plugin_dir() {
		return $this->plugin_dir;
	}

	/**
	 * Load plugin scripts and styles
	 */
	public function enqueue_scripts() {

		$ext = '.min';
		$version = $this->get_version();
		if ( ( defined( 'WP_DEBUG' ) && WP_DEBUG) || ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ) {
			$version = time();
			$ext = '';
		}

		// todo: check if viewing form and that form has recaptcha enabled, then output scripts in head.
		wp_enqueue_script( 'wpdf-recaptcha' , '//www.google.com/recaptcha/api.js' );

		wp_enqueue_script( 'wpdf-main' , $this->get_plugin_url() . 'assets/public/js/main' . $ext . '.js', array( 'jquery' ), $version, true );
		wp_enqueue_style( 'wpdf-main' , $this->get_plugin_url() . 'assets/public/css/main' . $ext . '.css', array(), $version );
	}

	/**
	 * On Plugin Activation
	 */
	public function on_activation() {

		$db = new WPDF_DatabaseManager();
		$db->install();
	}

	/**
	 * Register new form
	 *
	 * @param string $name Name of form.
	 * @param array  $args Form arguments.
	 *
	 * @return WPDF_Form
	 */
	public function register_form( $name, $args = array() ) {

		// register form with system.
		$form = new WPDF_Form( $name, $args );
		$this->_forms[ $name ] = $form;
		return $form;
	}

	/**
	 * Load form, and initialise it if required
	 *
	 * @param string $name Name of plugin.
	 *
	 * @return bool|WPDF_Form
	 */
	public function get_form( $name ) {

		if ( isset( $this->_forms[ $name ] ) ) {

			if ( is_array( $this->_forms[ $name ] ) ) {

				if ( isset( $this->_forms[ $name ]['form_id'] ) ) {
					// load db form.
					$this->_forms[ $name ] = new WPDF_DB_Form( $this->_forms[ $name ]['form_id'] );
				}
			}

			$form = $this->_forms[ $name ];
			return $form;
		}

		return false;
	}

	/**
	 * Check and process form submission requests
	 */
	public function process_form() {

		// load and process current form.
		if ( $this->get_current_form() ) {
			$this->_form->process();
		}
	}

	/**
	 * Get currently loaded form
	 *
	 * @return bool|WPDF_Form
	 */
	public function get_current_form() {

		if ( null !== $this->_form ) {
			return $this->_form;
		}

		// find active form, maybe a hidden form action field was submitted?
		// if so load that form.
		$form_id = isset($_POST['wpdf_action']) ? esc_attr($_POST['wpdf_action']) : false;
		if ( $form_id ) {

			$this->_form = $this->get_form( $form_id );
			return $this->_form;
		}

		return false;
	}

	/**
	 * Get list of forms
	 *
	 * @return WPDF_Form[]
	 */
	public function get_forms() {
		return $this->_forms;
	}

	/**
	 * Get database version
	 *
	 * @return int
	 */
	public function get_db_version() {
		return $this->db_version;
	}

	/**
	 * Get forms from database
	 */
	public function load_db_forms() {

		$query = new WP_Query(array(
			'post_type' => 'wpdf_form',
			'posts_per_page' => -1,
			'fields' => 'ids',
		));

		if ( $query->have_posts() ) {
			foreach ( $query->posts as $id ) {
				$form_key = sprintf( 'WPDF_FORM_%d', $id );
				$this->_forms[ $form_key ] = array( 'form_id' => $id );
			}
		}
	}
}

/**
 * Globally access WPDF_DeveloperForms instance.
 *
 * @return WPDF_DeveloperForms
 */
function WPDF() {
	return WPDF_DeveloperForms::instance();
}

$GLOBALS['wpdf'] = WPDF();
