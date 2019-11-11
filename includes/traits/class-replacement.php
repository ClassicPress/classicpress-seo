<?php
/**
 * The Replacement Helpers.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Traits
 */

namespace Classic_SEO\Traits;

use Classic_SEO\Helper;
use Classic_SEO\Helpers\Str;

defined( 'ABSPATH' ) || exit;

/**
 * Replacement class.
 */
trait Replacement {

	/**
	 * Remove the '%' delimiters from a variable string.
	 *
	 * @param  string $string Variable string to be cleaned.
	 * @return string
	 */
	private static function remove_var_delimiter( $string ) {
		return trim( $string, '%' );
	}

	/**
	 * Add the '%' delimiters to a variable string.
	 *
	 * @param  string $string Variable string to be delimited.
	 * @return string
	 */
	private static function add_var_delimiter( $string ) {
		return '%' . $string . '%';
	}

	/**
	 * Determine the post type names for the current post/page/cpt.
	 *
	 * @param  string $request Either 'single'|'plural' - whether to return the single or plural form.
	 * @return string|null
	 */
	private function determine_pt_names( $request = 'single' ) {
		$post_type = $this->get_post_type();
		if ( empty( $post_type ) ) {
			return null;
		}

		$object = get_post_type_object( $post_type );

		if ( 'single' === $request && isset( $object->labels->singular_name ) ) {
			return $object->labels->singular_name;
		}

		if ( 'plural' === $request && isset( $object->labels->name ) ) {
			$pt_plural = $object->labels->name;
		}

		return $object->name;
	}

	/**
	 * Determine the page number of the current post/page/cpt.
	 *
	 * @return int|null
	 */
	private function determine_page_number() {
		$page_number = is_singular() ? get_query_var( 'page' ) : get_query_var( 'paged' );
		if ( 0 === $page_number || '' === $page_number ) {
			return 1;
		}

		return $page_number;
	}

	/**
	 * Determine the max num of pages of the current post/page/cpt.
	 *
	 * @return int|null
	 */
	private function determine_max_pages() {
		global $wp_query, $post;
		if ( is_singular() && isset( $post->post_content ) ) {
			return ( substr_count( $post->post_content, '<!--nextpage-->' ) + 1 );
		}

		if ( isset( $wp_query->max_num_pages ) && ( '' !== $wp_query->max_num_pages && 0 !== $wp_query->max_num_pages ) ) {
			return $wp_query->max_num_pages;
		}

		return 1;
	}

	/**
	 * Get post type for current quried object.
	 *
	 * @return string
	 */
	private function get_post_type() {
		global $wp_query;

		if ( isset( $wp_query->query_vars['post_type'] ) && ( Str::is_non_empty( $wp_query->query_vars['post_type'] ) || ( is_array( $wp_query->query_vars['post_type'] ) && [] !== $wp_query->query_vars['post_type'] ) ) ) {
			$post_type = $wp_query->query_vars['post_type'];
		} elseif ( isset( $this->args->post_type ) && Str::is_non_empty( $this->args->post_type ) ) {
			$post_type = $this->args->post_type;
		} else {
			// Make it work in preview mode.
			$post_type = $wp_query->get_queried_object()->post_type;
		}

		if ( is_array( $post_type ) ) {
			$post_type = reset( $post_type );
		}

		return $post_type;
	}
}
