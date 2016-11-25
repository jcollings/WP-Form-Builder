<?php

/*
Plugin Name: WP Form Builder
Plugin URI: https://www.wordpressdeveloperforms.com/
Description: Drag & Drop form builder plugin, custom notifications, custom confirmation message, manage form submissions online.
Version: 0.3
Author: James Collings
Author URI: https://www.jclabs.co.uk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * @package WP Form Builder
 * @author James Collings <james@jclabs.co.uk>
 * @link https://www.wordpressdeveloperforms.com/
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @copyright Copyright (c) 2016, James Collings
 *
 * GNU General Public License, Free Software Foundation
 * <http://creativecommons.org/licenses/GPL/2.0/>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
class WPDF_DeveloperForms {

	protected $version = '0.3';
	public $plugin_dir = false;
	public $plugin_url = false;
	protected $plugin_slug = false;
	protected $settings = false;

	/**
	 * List of all registered forms
	 * @var WPDF_Form[]
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
	 * @var WPDF_Text
	 */
	public $text = null;

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

		add_action( 'wp_loaded', array( $this, 'load_db_forms' ), 9 );
		add_action( 'wp_loaded', array( $this, 'process_form' ) );
		//add_action( 'init', array( $this, 'init' ) );

		register_activation_hook( __FILE__, array($this, 'on_activation') );

		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
	}

	public function includes(){

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

		// modules
		include_once 'libs/modules/class-wpdf-user-registration.php';

		if ( is_admin() ) {
			include_once 'libs/admin/wpdf-functions.php';
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

		// load text
		$this->text = new WPDF_Text();

		// load preview
		if(isset($_GET['wpdf_preview']) && !empty($_GET['wpdf_preview'])){
			include_once 'libs/class-wpdf-preview.php';
			new WPDF_Preview(sanitize_title($_GET['wpdf_preview']));
		}
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

	public function enqueue_scripts(){

		$ext = '.min';
		$version = $this->get_version();
		if((defined('WP_DEBUG') && WP_DEBUG) || (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG)){
			$version = time();
			$ext = '';
		}

		// todo: check if viewing form and that form has recaptcha enabled, then output scripts in head
		wp_enqueue_script('wpdf-recaptcha', '//www.google.com/recaptcha/api.js');

		wp_enqueue_script('wpdf-main', $this->get_plugin_url() . 'assets/public/js/main'.$ext.'.js', array('jquery'), $version, true);
		wp_enqueue_style('wpdf-main', $this->get_plugin_url() . 'assets/public/css/main'.$ext.'.css', array(), $version);
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

	/**
	 * Load form, and initialise it if required
	 * @param $name
	 *
	 * @return bool|WPDF_Form
	 */
	public function get_form($name){

		if(isset($this->_forms[$name])){

			if(is_array($this->_forms[$name])){

				if(isset($this->_forms[$name]['form_id'])){
					// load db form
					$this->_forms[$name] = new WPDF_DB_Form($this->_forms[$name]['form_id']);
				}
			}

			$form = $this->_forms[$name];

			return $form;
		}

		return false;
	}

	public function process_form(){

		// load and process current form
		if( $this->get_current_form() ){
			$this->_form->process();
		}
	}

	public function get_current_form(){

		if($this->_form != null){
			return $this->_form;
		}

		// find active form, maybe a hidden form action field was submitted?
		// if so load that form
		if(isset($_POST['wpdf_action'])){

			$form_id = $_POST['wpdf_action'];
			$this->_form = $this->get_form($form_id);
			return $this->_form;
		}

		return false;
	}

	#endregion

	public function get_forms(){
		return $this->_forms;
	}

	public function load_db_forms(){

		$query = new WP_Query(array(
			'post_type' => 'wpdf_form',
			'posts_per_page' => -1,
			'fields' => 'ids'
		));

		if($query->have_posts()){
			foreach($query->posts as $id){
				$this->_forms['WPDF_FORM_'.$id] = array('form_id' => $id); //new WPDF_DB_Form($id);
			}
		}

	}
}

function WPDF() {
	return WPDF_DeveloperForms::instance();
}

$GLOBALS['wpdf'] = WPDF();