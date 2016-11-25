<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 25/11/2016
 * Time: 14:19
 */

/**
 * Class WPDF_Preview
 *
 * Create dummy form preview page, verified by nonce
 *
 * @since 0.3
 */
class WPDF_Preview{

	protected $slug = '';
	protected $args = array();
	protected $formId = 0;

	public function __construct($preview_id = false) {

		if(!$preview_id){
			return;
		}

		$transient_key = sprintf('wpdf_preview_%s', $preview_id);
		$transient = get_transient($transient_key);
		if($transient){

			// mark as preview
			if(!defined('WPDF_PREVIEW')){
				define('WPDF_PREVIEW', true);
			}

			add_filter('the_posts',array($this,'generate_preview'));
			$this->slug = $transient_key;
			$this->formId = $transient['form_id'];
		}
	}

	public function generate_preview( $posts ){

		global $wp,$wp_query;
		$page_slug = $this->slug;

		if($this->formId !== 0){

			$post = new stdClass;
			$post->post_author = 1;
			$post->post_name = $page_slug;
			$post->guid = get_bloginfo('wpurl' . '/' . $page_slug);
			$post->post_title = 'WP Form Builder Preview';
			$post->post_content = sprintf('[wp_form form_id="%d"]', $this->formId);
			$post->ID = -1;
			$post->post_status = 'static';
			$post->comment_status = 'closed';
			$post->ping_status = 'closed';
			$post->comment_count = 0;
			$post->post_date = current_time('mysql');
			$post->post_date_gmt = current_time('mysql',1);

			$posts = NULL;
			$posts[] = $post;

			$wp_query->is_page = true;
			$wp_query->is_singular = true;
			$wp_query->is_home = false;
			$wp_query->is_archive = false;
			$wp_query->is_category = false;
			unset($wp_query->query["error"]);
			$wp_query->query_vars["error"]="";
			$wp_query->is_404 = false;
		}

		return $posts;
	}
}