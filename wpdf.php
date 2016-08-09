<?php

/*
Plugin Name: WordPress Developer Forms
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: Simple WordPress forms library
Version: 0.0.1
Author: James Collings
Author URI: https://www.jclabs.co.uk
License: A "Slug" license name e.g. GPL2
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WPDF_DeveloperForms {

	protected $version = '0.0.1';
	public $plugin_dir = false;
	public $plugin_url = false;
	protected $plugin_slug = false;
	protected $settings = false;

	/**
	 * List of all registered forms
	 * @var null
	 */
	protected $_forms = null;

	/**
	 * Active Form
	 * @var WPDF_Form
	 */
	protected $_form = null;

	/**
	 * @var array
	 */
	private $default_settings = null;

	/**
	 * Single instance of class
	 */
	protected static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct(){

		$this->plugin_dir =  plugin_dir_path( __FILE__ );
		$this->plugin_url = plugins_url( '/', __FILE__ );
		$this->plugin_slug = basename(dirname(__FILE__));

		// not in init so function can be called from theme functions file
		$this->init();

		add_action( 'wp_loaded', array( $this, 'process_form' ) );
		//add_action( 'init', array( $this, 'init' ) );

		register_activation_hook( __FILE__, array($this, 'on_activation') );
	}

	public function includes(){

		include_once 'libs/class-wpdf-form.php';
		include_once 'libs/class-wpdf-form-data.php';
		include_once 'libs/class-wpdf-form-field.php';
		include_once 'libs/class-wpdf-validation.php';
		include_once 'libs/class-wpdf-notification.php';
		include_once 'libs/class-wpdf-email-manager.php';
		include_once 'libs/class-wpdf-database-manager.php';

		include_once 'libs/wpdf-functions.php';
		include_once 'libs/wpdf-shortcodes.php';

		// modules
		include_once 'libs/modules/class-wpdf-user-registration.php';

		if ( is_admin() ) {
			include_once 'libs/admin/wpdf-admin.php';
		}
	}

	/**
	 * Initite plugin
	 * @return void
	 */
	public function init(){

		$this->load_settings();
		$this->includes();
	}

	/**
	 * Plugin Default Settings
	 * @return void
	 */
	public function default_settings(){

		// set default settings
		$this->default_settings = array(

		);
	}

	/**
	 * Load plugin settings
	 * @return void
	 */
	public function load_settings(){

		$this->default_settings();
		$this->settings = array();

		// load settings from db
		foreach($this->default_settings as $key => $default){
			$data = get_option('wpdf_'.$key, $default);
			$this->settings[$key] = is_serialized( $data ) ? unserialize( $data ) : $data;
		}
	}

	public function get_settings($key, $default = false){

		if($default){
			return isset($this->default_settings[$key]) ? $this->default_settings[$key] : false;
		}

		return isset($this->settings[$key]) ? $this->settings[$key] : false;
	}

	public function get_version(){
		return $this->version;
	}
	public function get_plugin_slug(){
		return $this->plugin_slug;
	}
	public function get_plugin_url(){
		return $this->plugin_url;
	}
	public function get_plugin_dir(){
		return $this->plugin_dir;
	}

	public function on_activation(){
//		add_option( 'Activated_Plugin_WPDF', 'WPDF' );

		$db = new WPDF_DatabaseManager();
		if($db->install()){
			//delete_option( 'Activated_Plugin_WPDF' );
		}
	}

	#region Register Form Settings

	public function register_form($name, $args = array()){

		// register form with system
		$form = new WPDF_Form($name, $args);
		$this->_forms[$name] = $form;
		return $form;
	}

	public function get_form($name){

		if(isset($this->_forms[$name])){
			return $this->_forms[$name];
		}

		return false;
	}

	public function process_form(){

		// load and process current form
		if( $this->get_current_form() ){
			$this->_form->process();
		}
	}

	private function get_current_form(){

		// find active form, maybe a hidden form action field was submitted?
		// if so load that form
		if(isset($_GET['wpdf_action'])){

			$form_id = $_GET['wpdf_action'];
			$this->_form = wpdf_get_form($form_id);
			if($this->_form){
				return true;
			}
		}

		return false;
	}

	#endregion

	public function get_forms(){
		return $this->_forms;
	}
}

function WPDF() {
	return WPDF_DeveloperForms::instance();
}

$GLOBALS['wpdf'] = WPDF();