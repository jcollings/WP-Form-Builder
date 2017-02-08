<?php
/**
 * Email Manager
 *
 * @package WPDF
 * @author James Collings
 * @created 06/08/2016
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class WPDF_EmailManager
 */
class WPDF_EmailManager {

	/**
	 * List of available tags
	 *
	 * @var array
	 */
	protected $_tags = array();

	/**
	 * Email Notifications
	 *
	 * @var WPDF_Notification[]
	 */
	protected $_notifications = array();

	/**
	 * Email Templates
	 *
	 * @var array
	 */
	protected $_template = array(
		'field_html' => '<p>{{field_name}}:<br />{{field_value}}</p>',
		'field_text' => "\n{{field_name}}:\n{{field_value}}",
	);

	/**
	 * Email output type
	 *
	 * @var string
	 */
	protected $_email_type = 'html';

	/**
	 * WPDF Form
	 *
	 * @var WPDF_Form
	 */
	protected $_form;

	/**
	 * WPDF_EmailManager constructor.
	 *
	 * @param array $notifications List of form notifications.
	 */
	public function __construct( $form, $notifications ) {
		$this->_form = $form;
		$this->_notifications = $notifications;
	}


	/**
	 * Send email notification
	 *
	 * @param WPDF_FormData $form_data Form data.
	 *
	 * @return bool
	 */
	public function send( $form_data ) {

		if ( empty( $this->_notifications ) ) {
			// no notifications were needing to be sent.
			return true;
		}

		/**
		 * List of template tags when displayed in message
		 */
		$template_tags = array();

		/**
		 * List of template tags when displayed in to,cc,bcc, subject
		 */
		$raw_template_tags = array();

		if ( 'html' === $this->_email_type ) {
			add_filter( 'wp_mail_content_type', array( $this, 'set_mail_content_type' ) );
		}

		$all    = '';
		$fields = $form_data->to_array();
		foreach ( $fields as $field_id => $value ) {

			// dont show empty fields.
			if ( '' === $value ) {
				continue;
			}

			$field = $form_data->get_field( $field_id );
			$value = apply_filters( 'wpdf/display_field_value', $value, $field_id, $form_data );

			if ( 'html' === $this->_email_type ) {
				// todo: Convert url to link
			}

			$tag                       = $this->setup_merge_tag( 'field_' . $field_id );
			$raw_template_tags[ $tag ] = $value;
			$template_tags[ $tag ]     = $this->parse_merge_tags( $this->_template[ 'field_' . $this->_email_type ], array(
				'{{field_name}}'  => $field->get_label(),
				'{{field_value}}' => $value,
			) );

			if ( ! $field->is_type( 'password' ) && ! $field->is_type( 'virtual' ) ) {
				// dont add password fields to {{fields}} tag.
				$all .= $tag;
			}
		}

		// add admin_email merge tag to both raw and html tags.
		$raw_template_tags[ $this->setup_merge_tag( 'admin_email' ) ] = $template_tags[ $this->setup_merge_tag( 'admin_email' ) ] = get_option( 'admin_email' );
		$raw_template_tags[ $this->setup_merge_tag( 'site_name' ) ] = $template_tags[ $this->setup_merge_tag( 'site_name' ) ] = get_option( 'blogname' );
		$raw_template_tags[ $this->setup_merge_tag( 'site_url' ) ] = $template_tags[ $this->setup_merge_tag( 'site_url' ) ] = get_option( 'siteurl' );
		$raw_template_tags[ $this->setup_merge_tag( 'form_name' ) ] = $template_tags[ $this->setup_merge_tag( 'form_name' ) ] = $this->_form->get_label();


		$template_tags[ $this->setup_merge_tag( 'fields' ) ] = $all;
		$template_tags                                       = array_reverse( $template_tags, true );
		$this->_tags                                         = array_keys( $template_tags );


		// loop through notifications, setup, and send.
		foreach ( $this->_notifications as $notification ) {

			if ( ! $notification->is_valid( $form_data ) ) {
				continue;
			}

			$headers = array();

			$to = $this->parse_merge_tags( $notification->get_to(), $raw_template_tags );
			$cc = $this->parse_merge_tags( $notification->get_cc(), $raw_template_tags );
			if ( ! empty( $cc ) ) {
				$headers[] = 'Cc: ' . $cc;
			}

			$bcc = $this->parse_merge_tags( $notification->get_bcc(), $raw_template_tags );
			if ( ! empty( $bcc ) ) {
				$headers[] = 'Bcc: ' . $bcc;
			}

			$from = $this->parse_merge_tags( $notification->get_from(), $raw_template_tags );
			if ( ! empty( $from ) ) {
				$headers[] = 'From: ' . $from;
			}

			$subject = $this->parse_merge_tags( $notification->get_subject(), $raw_template_tags );
			$message = $this->parse_merge_tags( $notification->get_message(), $template_tags );

			wp_mail( $to, $subject, $message, $headers );
		}

		if ( 'html' === $this->_email_type ) {
			remove_filter( 'wp_mail_content_type', array( $this, 'set_mail_content_type' ) );
		}

		return true;
	}

	/**
	 * Set mail content type
	 *
	 * @return string
	 */
	public function set_mail_content_type() {
		return 'text/html';
	}

	/**
	 * Setup merge tag
	 *
	 * @param string $tag Merge tag.
	 *
	 * @return string
	 */
	protected function setup_merge_tag( $tag ) {
		return '{{' . $tag . '}}';
	}

	/**
	 * Parse merge tags
	 *
	 * @param string $content Content to be parsed.
	 * @param array  $tags List of merge tags.
	 *
	 * @return mixed
	 */
	protected function parse_merge_tags( $content, $tags ) {

		foreach ( $tags as $tag => $tag_content ) {

			$replacement = $tag_content;
			if ( is_array( $replacement ) ) {
				$replacement = implode( ',', $replacement );
			}

			$content = preg_replace( "/{$tag}/im", $replacement, $content );
		}

		return $content;
	}
}
