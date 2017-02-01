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
	 * Addon Name
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * Get Plugin Name
	 */
	public function get_name() {
		return $this->name;
	}
}