<?php
/**
 * The Post_Type helpers.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Helpers
 */

namespace Classic_SEO\Helpers;

use Classic_SEO\Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Post_Type class.
 */
trait Post_Type {

	/**
	 * Check if post is indexable.
	 *
	 * @param  int $post_id Post ID to check.
	 *
	 * @return boolean
	 */
	public static function is_post_indexable( $post_id ) {
		if ( true === self::is_post_excluded( $post_id ) ) {
			return false;
		}

		$robots = self::is_post_meta_indexable( $post_id );
		if ( is_bool( $robots ) ) {
			return $robots;
		}

		$post_type = get_post_type( $post_id );
		$robots    = Helper::get_settings( 'titles.cpseo_pt_' . $post_type . '_custom_robots' );
		$robots    = false === $robots ? Helper::get_settings( 'titles.cpseo_robots_global' ) : Helper::get_settings( 'titles.cpseo_pt_' . $post_type . '_robots' );

		return in_array( 'noindex', (array) $robots, true ) ? false : true;
	}
	
	/**
	 * Check if post is indexable by meta.
	 *
	 * @param int $post_id Post ID to check.
	 *
	 * @return boolean
	 */
	private static function is_post_meta_indexable( $post_id ) {
		$robots = Helper::get_post_meta( 'robots', $post_id );
		if ( empty( $robots ) || ! is_array( $robots ) ) {
			return '';
		}

		if ( in_array( 'index', $robots, true ) ) {
			return true;
		}

		return in_array( 'noindex', $robots, true ) ? false : '';
	}

	/**
	 * Check if post is explicitly excluded.
	 *
	 * @param  int $post_id Post ID to check.
	 * @return bool
	 */
	public static function is_post_excluded( $post_id ) {
		static $posts_to_exclude;

		if ( ! isset( $posts_to_exclude ) ) {
			$posts_to_exclude = wp_parse_id_list( Helper::get_settings( 'sitemap.cpseo_exclude_posts' ) );
			$posts_to_exclude = apply_filters( 'cpseo/sitemap/posts_to_exclude', $posts_to_exclude );
		}

		return in_array( $post_id, $posts_to_exclude, true );
	}

	/**
	 * Check if post type is indexable.
	 *
	 * @param  string $post_type Post type to check.
	 * @return bool
	 */
	public static function is_post_type_indexable( $post_type ) {
		if ( Helper::get_settings( 'titles.cpseo_pt_' . $post_type . '_custom_robots' ) ) {
			if ( in_array( 'noindex', (array) Helper::get_settings( 'titles.cpseo_pt_' . $post_type . '_robots' ), true ) ) {
				return false;
			}
		}

		return Helper::get_settings( 'sitemap.cpseo_pt_' . $post_type . '_sitemap' );
	}

	/**
	 * Check if post type is accessible.
	 *
	 * @param  string $post_type Post type to check.
	 * @return bool
	 */
	public static function is_post_type_accessible( $post_type ) {
		return in_array( $post_type, self::get_allowed_post_types(), true );
	}

	/**
	 * Get the post type label.
	 *
	 * @param  string $post_type Post type name.
	 * @param  bool   $singular  Get singular label.
	 * @return string|false
	 */
	public static function get_post_type_label( $post_type, $singular = false ) {
		$object = get_post_type_object( $post_type );
		if ( ! $object ) {
			return false;
		}
		return ! $singular ? $object->labels->name : $object->labels->singular_name;
	}

	/**
	 * Get post types that are public and not set to noindex.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return array All the accessible post types.
	 */
	public static function get_accessible_post_types() {
		static $accessible_post_types;

		if ( isset( $accessible_post_types ) ) {
			return $accessible_post_types;
		}

		$accessible_post_types = get_post_types( [ 'public' => true ] );
		$accessible_post_types = array_filter( $accessible_post_types, 'is_post_type_viewable' );

		/**
		 * Changing the list of accessible post types.
		 *
		 * @api array $accessible_post_types The post types.
		 */
		$accessible_post_types = apply_filters( 'cpseo/sitemap/excluded_post_types', $accessible_post_types );

		if ( ! is_array( $accessible_post_types ) ) {
			$accessible_post_types = [];
		}

		return $accessible_post_types;
	}

	/**
	 * Get accessible post types.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return array
	 */
	public static function get_allowed_post_types() {
		static $cpseo_allowed_post_types;

		if ( isset( $cpseo_allowed_post_types ) ) {
			return $cpseo_allowed_post_types;
		}

		$cpseo_allowed_post_types = [];
		foreach ( self::get_accessible_post_types() as $post_type ) {
			if ( false === apply_filters( 'cpseo/metabox/add_seo_metabox', Helper::get_settings( 'titles.cpseo_pt_' . $post_type . '_add_meta_box', true ) ) ) {
				continue;
			}

			$cpseo_allowed_post_types[] = $post_type;
		}

		return $cpseo_allowed_post_types;
	}
}
