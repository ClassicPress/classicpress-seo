<?php
/**
 * The Conditional helpers.
 *
 * @since      0.1.8
 * @package    ClassicPress_SEO
 * @subpackage ClassicPress_SEO\Helpers
 */

namespace ClassicPress_SEO\Helpers;

use ClassicPress_SEO\Helper;
use ClassicPress_SEO\Admin\Admin_Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Conditional class.
 */
trait Conditional {
	
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
	 * @return bool
	 */
	public static function is_rest() {
		$prefix = rest_get_url_prefix();
		if (
			defined( 'REST_REQUEST' ) && REST_REQUEST || // (#1)
			isset( $_GET['rest_route'] ) && // (#2)
			0 === strpos( trim( $_GET['rest_route'], '\\/' ), $prefix, 0 )
		) {
			return true;
		}

		// (#3)
		$rest_url    = wp_parse_url( site_url( $prefix ) );
		$current_url = wp_parse_url( add_query_arg( [] ) );

		return 0 === strpos( $current_url['path'], $rest_url['path'], 0 );
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
	 * Check if the site is connected to the ClassicPress SEO API.
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
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		// @codeCoverageIgnoreEnd
		return is_plugin_active( 'woocommerce/woocommerce.php' );
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
