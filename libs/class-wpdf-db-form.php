<?php
/**
 * DB Form
 *
 * @package WPDF
 * @author James Collings
 * @created 11/10/2016
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class WPDF_DB_Form
 */
class WPDF_DB_Form extends WPDF_Form {

	/**
	 * Form Label
	 *
	 * @var string
	 */
	private $_label = null;

	/**
	 * Form Theme Styles
	 *
	 * @var WPDF_FormTheme
	 */
	protected $_theme = null;

	/**
	 * WPDF_DB_Form constructor.
	 *
	 * @param int $form_id Form id.
	 */
	public function __construct( $form_id = null ) {

		$form_id = intval( $form_id );
		$post    = get_post( $form_id );
		if ( $post && 'wpdf_form' === $post->post_type ) :
			$this->ID = $form_id;
			$form = get_post_meta( $form_id , '_form_data', true );

			$fields = isset( $form['fields'] ) && ! empty( $form['fields'] ) ? $form['fields'] : array();
			$settings = isset($form['settings']) ? $form['settings'] : array();
			parent::__construct( 'Form ' . $form_id, $fields, $settings );

			// load settings.
//			if ( isset( $form['settings'] ) ) {
//				$this->settings( $form['settings'] );
//			}

			// load style.
			$style          = isset( $form['theme'] ) ? $form['theme'] : array();
			$style_disabled = isset( $form['theme_disabled'] ) ? $form['theme_disabled'] : array();
			$this->_theme   = new WPDF_FormTheme( $style, $style_disabled );

			// load form content.
			$this->_content               = isset( $form['content'] ) ? $form['content'] : '';
			$this->_confirmation_location = isset( $form['confirmation_location'] ) ? $form['confirmation_location'] : 'after';

			if ( isset( $form['form_label'] ) ) {
				$this->_label = $form['form_label'];
			} else {
				$this->_label = sprintf( 'WPDF_FORM_%d', $this->ID );
			}

			// load confirmations.
			if ( isset( $form['confirmations'] ) ) {

				foreach ( $form['confirmations'] as $confirmation ) {

					if ( 'message' === $confirmation['type'] ) {
						$this->add_confirmation( 'message', $confirmation['message'] );
					} elseif ( 'redirect' === $confirmation['type'] ) {
						$this->add_confirmation( 'redirect', $confirmation['redirect_url'] );
					}
				}
			}

			// load notifications.
			if ( isset( $form['notifications'] ) && ! empty( $form['notifications'] ) ) {

				foreach ( $form['notifications'] as $notification ) {

					if ( empty( $notification['to'] ) ) {
						continue;
					}

					$args = array();
					if ( isset( $notification['from'] ) && ! empty( $notification['from'] ) ) {
						$args['from'] = $notification['from'];
					}
					if ( isset( $notification['cc'] ) && ! empty( $notification['cc'] ) ) {
						$args['cc'] = $notification['cc'];
					}
					if ( isset( $notification['bcc'] ) && ! empty( $notification['bcc'] ) ) {
						$args['bcc'] = $notification['bcc'];
					}

					$this->add_notification( $notification['to'], $notification['subject'], $notification['message'], $args );
				}
			}

		endif;
	}

	/**
	 * Get form id
	 *
	 * @return int
	 */
	public function get_db_id() {
		return $this->get_id();
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'WPDF_FORM_' . $this->ID;
	}

	/**
	 * Get label
	 *
	 * @return string
	 */
	public function get_label() {
		return $this->_label;
	}

	/**
	 * Get Form Styling
	 *
	 * @param string $key Style key.
	 * @param bool   $force Get style even if its disabled.
	 *
	 * @return bool|string
	 */
	public function get_style( $key, $force = false ) {
		return $this->_theme->get_style( $key, $force );
	}

	/**
	 * Has Style
	 *
	 * @param string $key Style key.
	 *
	 * @return bool|mixed
	 */
	public function has_style( $key ) {
		return $this->_theme->get_style( $key );
	}

	/**
	 * Check to see if style is disabled
	 *
	 * @param string $key Style key.
	 *
	 * @return bool
	 */
	public function is_style_disabled( $key ) {
		return $this->_theme->is_style_disabled( $key );
	}

	/**
	 * Export DB Form
	 *
	 * @return bool|mixed
	 */
	public function export() {

		if ( is_admin() ) {
			$post = get_post( $this->ID );
			if ( $post && 'wpdf_form' === $post->post_type ) {
				return get_post_meta( $this->ID , '_form_data', true );
			}
		}
		return false;
	}
}
