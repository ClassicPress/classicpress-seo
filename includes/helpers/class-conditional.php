<?php
/**
 * The Conditional helpers.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Helpers
 */


namespace Classic_SEO\Helpers;

use Classic_SEO\Helper;
use Classic_SEO\Admin\Admin_Helper;
use Classic_SEO\Helpers\WordPress;

defined( 'ABSPATH' ) || exit;

/**
 * Conditional class.
 */
trait Conditional {

	use WordPress;

	/**
	 * Check if whitelabel filter is active.
	 *
	 * @return boolean
	 */
	public static function is_whitelabel() {
		/**
		 * Enable whitelabel.
		 *
		 * @param bool $whitelabel Enable whitelabel.
		 */
		return apply_filters( 'cpseo/whitelabel', false );
	}

	/**
	 * Is AJAX request
	 *
	 * @return bool Returns true when the page is loaded via ajax.
	 */
	public static function is_ajax() {
		return function_exists( 'wp_doing_ajax' ) ? wp_doing_ajax() : defined( 'DOING_AJAX' ) && DOING_AJAX;
	}

	/**
	 * Is CRON request
	 *
	 * @return bool Returns true when the page is loaded via cron.
	 */
	public static function is_cron() {
		return function_exists( 'wp_doing_cron' ) ? wp_doing_cron() : defined( 'DOING_CRON' ) && DOING_CRON;
	}

	/**
	 * Is auto-saving
	 *
	 * @return bool Returns true when the page is loaded for auto-saving.
	 */
	public static function is_autosave() {
		return defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE;
	}

	/**
	 * Is REST request
	 *
	 * @link https://wordpress.stackexchange.com/questions/221202/does-something-like-is-rest-exist/221289
	 *
	 * Case #1: After WP_REST_Request initialisation
	 * Case #2: Support "plain" permalink settings
	 * Case #3: It can happen that WP_Rewrite is not yet initialized,
	 *          so do this (wp-settings.php)
	 * Case #4: URL Path begins with wp-json/ (your REST prefix)
	 *          Also supports WP installations in subfolders
	 *
	 * @return bool
	 */
	public static function is_rest() {
		global $wp_rewrite;

		$prefix = rest_get_url_prefix();
		if (
			defined( 'REST_REQUEST' ) && REST_REQUEST || // (#1)
			isset( $_GET['rest_route'] ) && // (#2)
			0 === strpos( trim( $_GET['rest_route'], '\\/' ), $prefix, 0 )
		) {
			return true;
		}

		// (#3)
		if ( null === $wp_rewrite ) {
			$wp_rewrite = new \WP_Rewrite;
		}

		// (#4)
		$rest_url    = wp_parse_url( trailingslashit( rest_url() ) );
		$current_url = wp_parse_url( add_query_arg( [] ) );

		return (
			isset( $current_url['path'] ) &&
			isset( $rest_url['path'] ) &&
			0 === strpos( $current_url['path'], $rest_url['path'], 0 )
		);
	}


	/**
	 * Check if module is active.
	 *
	 * @param  string $id Module ID.
	 * @return boolean
	 */
	public static function is_module_active( $id ) {
		$active_modules = get_option( 'cpseo_modules', [] );
		if ( ! is_array( $active_modules ) || ! isset( cpseo()->manager ) || is_null( cpseo()->manager ) ) {
			return false;
		}

		return in_array( $id, $active_modules, true ) && array_key_exists( $id, cpseo()->manager->modules );
	}

	/**
	 * Checks if the plugin is configured.
	 *
	 * @param bool $value If this param is set, the option will be updated.
	 * @return bool Return the option value if param is not set.
	 */
	public static function is_configured( $value = null ) {
		$key = 'cpseo_is_configured';
		if ( is_null( $value ) ) {
			$value = get_option( $key );
			return ! empty( $value );
		}
		Helper::schedule_flush_rewrite();
		update_option( $key, $value );
	}

	/**
	 * Check if the site is connected to the Classic SEO API.
	 *
	 * @return bool
	 */
	public static function is_site_connected() {
		return true;			// KLUDGE To be removed
	}

	/**
	 * Check if author archives are indexable.
	 *
	 * @return bool
	 */
	public static function is_author_archive_indexable() {
		if ( true === Helper::get_settings( 'titles.cpseo_disable_author_archives' ) ) {
			return false;
		}

		if ( Helper::get_settings( 'titles.cpseo_author_custom_robots' ) && in_array( 'noindex', (array) Helper::get_settings( 'titles.cpseo_author_robots' ), true ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if editing the file is allowed.
	 *
	 * @return bool
	 *
	 * @since 1.0.32
	 */
	public static function is_edit_allowed() {
		return ( ! defined( 'DISALLOW_FILE_EDIT' ) || ! DISALLOW_FILE_EDIT ) && ( ! defined( 'DISALLOW_FILE_MODS' ) || ! DISALLOW_FILE_MODS );
	}

	/**
	 * Check whether to show SEO score.
	 *
	 * @return boolean
	 * @since 1.0.32
	 */
	public static function is_score_enabled() {
		/**
		 * Enable SEO Score.
		 *
		 * @param bool Enable SEO Score.
		 */
		return apply_filters( 'cpseo/show_score', true );
	}

	/**
	 * Check if the request is heartbeat.
	 *
	 * @return bool
	 */
	public static function is_heartbeat() {
		return 'heartbeat' === Param::post( 'action' );
	}

	/**
	 * Check if the request is from frontend.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return bool
	 */
	public function is_frontend() {
		return ! is_admin();
	}

	/**
	 * Is WooCommerce Installed
	 *
	 * @return bool
	 */
	public static function is_woocommerce_active() {
		// @codeCoverageIgnoreStart
		$wp_filesystem = self::get_filesystem();

		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		/**
		 * Check for additional proof that the real WC is installed and not the Classic Commerce compatibility plugin
		 */
		if( $wp_filesystem->exists( WP_PLUGIN_DIR . "/woocommerce/includes/class-woocommerce.php" ) && $wp_filesystem->exists( WP_PLUGIN_DIR . "/woocommerce/includes/admin/class-wc-admin.php" ) ) {
			// @codeCoverageIgnoreEnd
			return in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
		}
		return false;
	}

	/**
	 * Is Classic Commerce Installed
	 *
	 * @return bool
	 * @since 0.5.3
	 */
	public static function is_classic_commerce_active() {
		// @codeCoverageIgnoreStart
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		// @codeCoverageIgnoreEnd
		return in_array( 'classic-commerce/classic-commerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
	}

	/**
	 * Is EDD Installed
	 *
	 * @return bool
	 */
	public static function is_edd_active() {
		return class_exists( 'Easy_Digital_Downloads' );
	}

}
