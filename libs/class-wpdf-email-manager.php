<?php
/**
 * Form Theme
 *
 * @package WPDF
 * @author James Collings
 * @created 06/08/2016
 */

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
	 * WPDF_EmailManager constructor.
	 *
	 * @param array $notifications List of form notifications.
	 */
	public function __construct( $notifications ) {
		$this->_notifications = $notifications;
	}


	/**
	 * Send email notification
	 *
	 * @param WPDF_FormData $data Form data.
	 *
	 * @return bool
	 */
	public function send( $data ) {

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
		$fields = $data->to_array();
		foreach ( $fields as $field_id => $value ) {

			// dont show empty fields.
			if ( '' === $value ) {
				continue;
			}

			$field = $data->get_field( $field_id );
			if ( $field->is_type( 'file' ) ) {

				$link = trailingslashit( $data->get_upload_folder() ) . $value;

				if ( 'html' === $this->_email_type ) {
					$value = '<a href="' . wpdf_get_uploads_url() . $link . '">' . $value . '</a>';
				} else {
					$value = wpdf_get_uploads_url() . $link;
				}
			}

			$tag                       = $this->setup_merge_tag( 'field_' . $field_id );
			$raw_template_tags[ $tag ] = $value;
			$template_tags[ $tag ]     = $this->parse_merge_tags( $this->_template[ 'field_' . $this->_email_type ], array(
				'{{field_name}}'  => $field->get_label(),
				'{{field_value}}' => $value,
			) );

			if ( ! $field->is_type( 'password' ) ) {
				// dont add password fields to {{fields}} tag.
				$all .= $tag;
			}
		}

		// add admin_email merge tag to both raw and html tags.
		$raw_template_tags[ $this->setup_merge_tag( 'admin_email' ) ] = $template_tags[ $this->setup_merge_tag( 'admin_email' ) ] = get_option( 'admin_email' );

		$template_tags[ $this->setup_merge_tag( 'fields' ) ] = $all;
		$template_tags                                       = array_reverse( $template_tags, true );
		$this->_tags                                         = array_keys( $template_tags );


		// loop through notifications, setup, and send.
		foreach ( $this->_notifications as $notification ) {

			if ( ! $notification->is_valid( $data ) ) {
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
