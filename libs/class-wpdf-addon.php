<?php
/**
 * WPDF Base Addon
 *
 * @package WPDF/Pro
 * @author James Collings
 * @created 31/01/2017
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WPDF_Addon {

	/**
	 * Plugin Version
	 *
	 * @var string
	 */
	protected $version;

	/**
	 * Addon Name
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * DB Settings key
	 *
	 * @var string
	 */
	protected $setting_key;

	/**
	 * Is plugin setu
	 *
	 * @var bool
	 */
	protected $setup = false;

	/**
	 * Get Plugin Name
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Get DB Settings key
	 * @return string
	 */
	public function get_setting_key(){

		if( $this->setting_key ) {
			return $this->setting_key;
		}

		return $this->setting_key =  sanitize_title( $this->get_name() );
	}

	/**
	 * Check to see if plugin is enabled
	 *
	 * @param WPDF_Form $form
	 *
	 * @return bool
	 */
	public function is_enabled($form){

		if( $form->get_setting( 'enabled', $this->get_setting_key()) === 'yes'){
			return true;
		}

		return false;
	}

	public function is_setup(){
		return $this->setup;
	}

	/**
	 * Log class error
	 *
	 * @param $msg
	 */
	protected function log( $msg ) {
		file_put_contents( __DIR__ . '/debug.log', date( 'Y-m-d H:i:s - ' . $this->get_name() .': ' ) . json_encode( $msg ) . "\n", FILE_APPEND );
	}

	/**
	 * Get plugin version
	 *
	 * @return string
	 */
	public function get_version() {
		return $this->version;
	}
}