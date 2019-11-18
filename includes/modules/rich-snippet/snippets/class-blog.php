<?php
/**
 * The Blog Class.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\RichSnippet

 */

namespace Classic_SEO\RichSnippet;

defined( 'ABSPATH' ) || exit;

/**
 * Blog class.
 */
class Blog implements Snippet {

	/**
	 * Sets the Schema structured data for the Blog.
	 *
	 * @link https://schema.org/Blog
	 *
	 * @param array  $data   Array of JSON-LD data.
	 * @param JsonLD $jsonld JsonLD Instance.
	 *
	 * @return array
	 */
	public function process( $data, $jsonld ) {
		$is_front       = is_front_page();
		$data['schema'] = 'BlogPosting';
		$data['Blog']   = [
			'@context'    => 'https://schema.org/',
			'@type'       => 'Blog',
			'url'         => $is_front ? home_url() : get_permalink( get_option( 'page_for_posts' ) ),
			'headline'    => $is_front ? $jsonld->get_website_name() : get_the_title( get_option( 'page_for_posts' ) ),
			'description' => get_bloginfo( 'description' ),
			'blogPost'    => $jsonld->get_post_collection( $data ),
		];
		unset( $data['schema'] );

		return $data;
	}
}
