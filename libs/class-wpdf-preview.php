<?php
/**
 * Form Preview Class
 *
 * @package WPDF
 * @author James Collings
 * @created 25/11/2016
 */

/**
 * Class WPDF_Preview
 *
 * Create dummy form preview page, verified by nonce
 *
 * @since 0.3
 */
class WPDF_Preview {

	/**
	 * Preview slug
	 *
	 * @var string
	 */
	protected $slug = '';

	/**
	 * Preview arguments
	 *
	 * @var array
	 */
	protected $args = array();

	/**
	 * Form Id being previewed
	 *
	 * @var int
	 */
	protected $form_id = 0;

	/**
	 * WPDF_Preview constructor.
	 *
	 * @param bool|string $preview_id Preview token.
	 */
	public function __construct( $preview_id = false ) {

		if ( ! $preview_id ) {
			return;
		}

		$transient_key = sprintf( 'wpdf_preview_%s', $preview_id );
		$transient     = get_transient( $transient_key );
		if ( $transient ) {

			// mark as preview.
			if ( ! defined( 'WPDF_PREVIEW' ) ) {
				define( 'WPDF_PREVIEW', true );
			}

			add_filter( 'the_posts', array( $this, 'generate_preview' ) );
			$this->slug   = $transient_key;
			$this->form_id = $transient['form_id'];
		}
	}

	/**
	 * Generate preview
	 *
	 * @param array $posts Found posts.
	 *
	 * @return array|null
	 */
	public function generate_preview( $posts ) {

		global $wp, $wp_query;
		$page_slug = $this->slug;

		if ( 0 !== $this->form_id ) {

			$post                 = new stdClass;
			$post->post_author    = 1;
			$post->post_name      = $page_slug;
			$post->guid           = get_bloginfo( 'wpurl' ) . '/' . $page_slug;
			$post->post_title     = 'WP Form Builder Preview';
			$post->post_content   = sprintf( '[wp_form form_id="%d"]', $this->form_id );
			$post->ID             = - 1;
			$post->post_type      = 'page';
			$post->post_status    = 'static';
			$post->comment_status = 'closed';
			$post->ping_status    = 'closed';
			$post->comment_count  = 0;
			$post->post_date      = current_time( 'mysql' );
			$post->post_date_gmt  = current_time( 'mysql', 1 );

			$posts   = null;
			$posts[] = $post;

			$wp_query->is_page     = true;
			$wp_query->is_singular = true;
			$wp_query->is_home     = false;
			$wp_query->is_archive  = false;
			$wp_query->is_category = false;
			unset( $wp_query->query['error'] );
			$wp_query->query_vars['error'] = '';
			$wp_query->is_404              = false;
		}

		return $posts;
	}
}
