<?php
/**
 * The WordPress helpers.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Helpers
 */


namespace Classic_SEO\Helpers;

use Classic_SEO\Post;
use Classic_SEO\Term;
use Classic_SEO\User;
use Classic_SEO\Helper;
use Classic_SEO\Helpers\Param;
use Classic_SEO\Helpers\Str;
use Classic_SEO\Helpers\WordPress as WP_Helper;

defined( 'ABSPATH' ) || exit;

/**
 * WordPress class.
 */
trait WordPress {

	/**
	 * Get roles.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param string $output How to return roles.
	 *
	 * @return array
	 */
	public static function get_roles( $output = 'names' ) {
		$wp_roles = wp_roles();

		if ( 'names' !== $output ) {
			return $wp_roles->roles;
		}

		return $wp_roles->get_names();
	}

	/**
	 * Get current post type.
	 *
	 * This function has some fallback strategies to get the current screen post type.
	 *
	 * @return string|bool
	 */
	public static function get_post_type() {
		global $pagenow;

		$post_type = self::post_type_from_globals();
		if ( false !== $post_type ) {
			return $post_type;
		}

		$post_type = self::post_type_from_request();
		if ( false !== $post_type ) {
			return $post_type;
		}

		return 'post-new.php' === $pagenow ? 'post' : false;
	}

	/**
	 * Retrieves the sitename.
	 *
	 * @return string
	 */
	public static function get_site_name() {
		return wp_strip_all_tags( get_bloginfo( 'name' ), true );
	}

	/**
	 * Instantiates the WordPress filesystem for use.
	 *
	 * @return object
	 */
	public static function get_filesystem() {
		global $wp_filesystem;

		if ( ! defined( 'FS_METHOD' ) ) {
			define( 'FS_METHOD', 'direct' );
		}

		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		return $wp_filesystem;
	}

	/**
	 * Get action from request.
	 *
	 * @return bool|string
	 */
	public static function get_request_action() {
		if ( empty( $_REQUEST['action'] ) ) {
			return false;
		}

		if ( '-1' === $_REQUEST['action'] && ! empty( $_REQUEST['action2'] ) ) {
			$_REQUEST['action'] = $_REQUEST['action2'];
		}

		return sanitize_key( $_REQUEST['action'] );
	}

	/**
	 * Get post type from global variables
	 *
	 * @return string|bool
	 */
	private static function post_type_from_globals() {
		global $post, $typenow, $current_screen;

		if ( $post && $post->post_type ) {
			return $post->post_type;
		}

		if ( $typenow ) {
			return $typenow;
		}

		if ( $current_screen && $current_screen->post_type ) {
			return $current_screen->post_type;
		}

		return false;
	}

	/**
	 * Get post type from request variables
	 *
	 * @return string|bool
	 */
	private static function post_type_from_request() {

		if ( $post_type = Param::request( 'post_type' ) ) { // phpcs:ignore
			return sanitize_key( $post_type );
		}

		if ( $post_id = Param::request( 'post_ID' ) ) { // phpcs:ignore
			return get_post_type( $post_id );
		}

		// @codeCoverageIgnoreStart
		if ( $post = Param::get( 'post' ) ) { // phpcs:ignore
			return get_post_type( $post );
		}
		// @codeCoverageIgnoreEnd

		return false;
	}

	/**
	 * Strip all shortcodes active or orphan.
	 *
	 * @param string $content Content to remove shortcodes from.
	 *
	 * @return string
	 */
	public static function strip_shortcodes( $content ) {
		if ( ! Str::contains( '[', $content ) ) {
			return $content;
		}

		return preg_replace( '~\[\/?.*?\]~s', '', $content );
	}

	/**
	 * Wraps wp_safe_redirect to add header.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param string $location The path to redirect to.
	 * @param int    $status   Status code to use.
	 */
	public static function redirect( $location, $status = 302 ) {
		header( 'X-Redirect-By: Classic SEO' );
		wp_safe_redirect( $location, $status );
		exit;
	}

	/**
	 * Whether the current user has a specific capability.
	 *
	 * @codeCoverageIgnore
	 * @see current_user_can()
	 *
	 * @param  string $capability Capability name.
	 * @return boolean Whether the current user has the given capability.
	 */
	public static function has_cap( $capability ) {
		return current_user_can( 'cpseo_' . str_replace( '-', '_', $capability ) );
	}

	/**
	 * Get post meta value.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param  string  $key     Internal key of the value to get (without prefix).
	 * @param  integer $post_id Post ID of the post to get the value for.
	 * @return mixed
	 */
	public static function get_post_meta( $key, $post_id = 0 ) {
		return Post::get_meta( $key, $post_id );
	}

	/**
	 * Get term meta value.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param  string $key      Internal key of the value to get (without prefix).
	 * @param  mixed  $term     Term to get the meta value for either (string) term name, (int) term id or (object) term.
	 * @param  string $taxonomy Name of the taxonomy to which the term is attached.
	 * @return mixed
	 */
	public static function get_term_meta( $key, $term = 0, $taxonomy = '' ) {
		return Term::get_meta( $key, $term, $taxonomy );
	}

	/**
	 * Get user meta value.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param  string $key  Internal key of the value to get (without prefix).
	 * @param  mixed  $user User to get the meta value for either (int) user id or (object) user.
	 * @return mixed
	 */
	public static function get_user_meta( $key, $user = 0 ) {
		return User::get_meta( $key, $user );
	}

	/**
	 * Get admin url.
	 *
	 * @param  string $page Page id.
	 * @param  array  $args Pass arguments to query string.
	 * @return string
	 */
	public static function get_admin_url( $page = '', $args = [] ) {
		$page = $page ? 'cpseo-' . $page : 'cpseo';
		$args = wp_parse_args( $args, [ 'page' => $page ] );

		return add_query_arg( $args, admin_url( 'admin.php' ) );
	}

	/**
	 * Get Classic SEO Dashboard url.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return string
	 */
	public static function get_dashboard_url() {
		$site_type     = get_transient( '_cpseo_site_type' );
		$business_type = [ 'news', 'business', 'webshop', 'otherbusiness' ];

		if ( in_array( $site_type, $business_type, true ) ) {
			return self::get_admin_url( 'options-titles#setting-panel-local' );
		}
		return admin_url( 'admin.php?page=cpseo&view=modules' );
	}

	/**
	 * Get default capabilities.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return array
	 */
	public static function get_capabilities() {
		$caps = [
			'cpseo_titles'          => esc_html__( 'Titles & Meta Settings', 'cpseo' ),
			'cpseo_general'         => esc_html__( 'General Settings', 'cpseo' ),
			'cpseo_sitemap'         => esc_html__( 'XML Sitemap Settings', 'cpseo' ),
			'cpseo_404_monitor'     => esc_html__( '404 Monitor Log', 'cpseo' ),
			'cpseo_link_builder'    => esc_html__( 'Link Builder', 'cpseo' ),
			'cpseo_redirections'    => esc_html__( 'Redirections', 'cpseo' ),
			'cpseo_role_manager'    => esc_html__( 'Role Manager', 'cpseo' ),
			'cpseo_onpage_analysis' => esc_html__( 'On-Page Analysis', 'cpseo' ),
			'cpseo_onpage_general'  => esc_html__( 'On-Page General Settings', 'cpseo' ),
			'cpseo_onpage_advanced' => esc_html__( 'On-Page Advanced Settings', 'cpseo' ),
			'cpseo_onpage_snippet'  => esc_html__( 'On-Page Rich Snippet Settings', 'cpseo' ),
			'cpseo_onpage_social'   => esc_html__( 'On-Page Social Settings', 'cpseo' ),
			'cpseo_admin_bar'       => esc_html__( 'Top Admin Bar', 'cpseo' ),
		];

		if ( ! function_exists( 'cpseo_load_premium' ) ) {
			unset( $caps['cpseo_link_builder'] );
		}

		return $caps;
	}

	/**
	 * Get active capabilities.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return array
	 */
	public static function get_roles_capabilities() {
		$data = [];
		$caps = array_keys( self::get_capabilities() );

		foreach ( self::get_roles() as $slug => $role ) {
			self::get_role_capabilities( $slug, $caps, $data );
		}

		return $data;
	}

	/**
	 * Get active capabilities for role.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param string $slug Role slug.
	 * @param array  $caps Array of capabilities.
	 * @param array  $data Data instance.
	 */
	private static function get_role_capabilities( $slug, $caps, &$data ) {
		$role = get_role( $slug );
		if ( ! $role ) {
			return;
		}

		$slug = esc_attr( $slug );
		foreach ( $caps as $cap ) {
			if ( $role->has_cap( $cap ) ) {
				$data[ $slug ][] = $cap;
			}
		}
	}

	/**
	 * Set capabilities to role.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param array $roles Data.
	 */
	public static function set_capabilities( $roles ) {
		$caps = array_keys( self::get_capabilities() );
		foreach ( WP_Helper::get_roles() as $slug => $role ) {
			self::set_role_capabilities( $slug, $caps, $roles );
		}
	}

	/**
	 * Set capabilities for role.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param string $slug  Role slug.
	 * @param array  $caps  Array of capabilities.
	 * @param array  $roles Data.
	 */
	private static function set_role_capabilities( $slug, $caps, $roles ) {
		$role = get_role( $slug );
		if ( ! $role ) {
			return;
		}

		$roles[ $slug ] = isset( $roles[ $slug ] ) && is_array( $roles[ $slug ] ) ? array_flip( $roles[ $slug ] ) : [];
		foreach ( $caps as $cap ) {
			$func = isset( $roles[ $slug ], $roles[ $slug ][ $cap ] ) ? 'add_cap' : 'remove_cap';
			$role->$func( $cap );
		}
	}

	/**
	 * Schedules a rewrite flush to happen.
	 *
	 * @codeCoverageIgnore
	 */
	public static function schedule_flush_rewrite() {
		update_option( 'cpseo_flush_rewrite', 1 );
	}

	/**
	 * Get post thumbnail with fallback as
	 *     1. Post thumbnail.
	 *     2. First image in content.
	 *     3. Facebook image if any
	 *     4. Twitter image if any.
	 *     5. Default open graph image set in option panel.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param  int|WP_Post  $post_id Post ID or WP_Post object.
	 * @param  string|array $size    Image size. Accepts any valid image size, or an array of width and height values in pixels.
	 * @return false|array Returns an array (url, width, height, is_intermediate), or false, if no image is available.
	 */
	public static function get_thumbnail_with_fallback( $post_id, $size = 'thumbnail' ) {
		if ( has_post_thumbnail( $post_id ) ) {
			return wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $size );
		}

		preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', get_the_content(), $matches );
		$matches = array_filter( $matches );
		if ( ! empty( $matches ) ) {
			return [ $matches[1][0], 200, 200 ];
		}

		$fb_image = Helper::get_post_meta( 'facebook_image_id', $post_id );
		$tw_image = Helper::get_post_meta( 'twitter_image_id', $post_id );
		$og_image = $fb_image ? $fb_image : $tw_image;

		if ( $og_image ) {
			return wp_get_attachment_image_src( $og_image, $size );
		}

		$default_og = Helper::get_settings( 'titles.cpseo_open_graph_image_id' );
		return $default_og ? wp_get_attachment_image_src( $default_og, $size ) : false;
	}

	/**
	 * Check if plugin is network active
	 *
	 * @codeCoverageIgnore
	 *
	 * @return boolean
	 */
	public static function is_plugin_active_for_network() {
		if ( ! is_multisite() ) {
			return false;
		}

		// Makes sure the plugin is defined before trying to use it.
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}

		if ( ! is_plugin_active_for_network( plugin_basename( CPSEO_FILE ) ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Helper function to validate & format ISO 8601 duration.
	 *
	 * @param  string $iso8601 Duration which need to be converted to seconds.
	 * @return string
	 *
	 * @since 1.0.21
	 */
	public static function get_formatted_duration( $iso8601 ) {
		$end = substr( $iso8601, -1 );
		if ( ! in_array( $end, [ 'D', 'H', 'M', 'S' ], true ) ) {
			return '';
		}

		// The format starts with the letter P, for "period".
		return ( ! Str::starts_with( 'P', $iso8601 ) ) ? 'PT' . $iso8601 : $iso8601;
	}

	/**
	 * Get robots default.
	 *
	 * @return array
	 */
	public static function get_robots_defaults() {
		$screen = get_current_screen();
		$robots = Helper::get_settings( 'titles.cpseo_robots_global', [] );

		if ( 'post' === $screen->base && Helper::get_settings( "titles.cpseo_pt_{$screen->post_type}_custom_robots" ) ) {
			$robots = Helper::get_settings( "titles.cpseo_pt_{$screen->post_type}_robots", [] );
		}

		if ( 'term' === $screen->base && Helper::get_settings( "titles.cpseo_tax_{$screen->taxonomy}_custom_robots" ) ) {
			$robots = Helper::get_settings( "titles.cpseo_tax_{$screen->taxonomy}_robots", [] );
		}

		if ( in_array( $screen->base, [ 'profile', 'user-edit' ], true ) && Helper::get_settings( 'titles.cpseo_author_custom_robots' ) ) {
			$robots = Helper::get_settings( 'titles.cpseo_author_robots', [] );
		}

		if ( is_array( $robots ) && ! in_array( 'noindex', $robots, true ) ) {
			$robots[] = 'index';
		}

		return $robots;
	}
}
