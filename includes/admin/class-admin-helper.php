<?php
/**
 * Admin helper Functions.
 *
 * This file contains functions needed on the admin screens.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Admin
 */


namespace Classic_SEO\Admin;

use Classic_SEO\Helper;
use Classic_SEO\Helpers\Param;
use Classic_SEO\Helpers\WordPress;

defined( 'ABSPATH' ) || exit;

/**
 * Admin_Helper class.
 */
class Admin_Helper {

	/**
	 * Get tooltip HTML.
	 *
	 * @param  string $message Message to show in tooltip.
	 * @return string
	 */
	public static function get_tooltip( $message ) {
		return '<span class="cpseo-tooltip"><em class="dashicons-before dashicons-editor-help"></em><span>' . $message . '</span></span>';
	}
	
	/**
	 * Get Classic SEO icon.
	 *
	 * @param integer $width Width of the icon.
	 *
	 * @return string
	 */
	public static function get_icon() {
		$icon_svg = file_get_contents( CPSEO()->plugin_url() . '/assets/admin/img/classic-seo-dashicon-white-on-transparent.svg' );
		return $icon_svg;
	}

	/**
	 * Get admin view file.
	 *
	 * @param  string $view View filename.
	 * @return string Complete path to view
	 */
	public static function get_view( $view ) {
		return cpseo()->admin_dir() . "views/{$view}.php";
	}

	/**
	 * Get taxonomies as choices.
	 *
	 * @param array $args (Optional) Arguments passed to filter list.
	 * @return array|bool
	 */
	public static function get_taxonomies_options( $args = [] ) {
		global $wp_taxonomies;

		$args       = wp_parse_args( $args, [ 'public' => true ] );
		$taxonomies = wp_filter_object_list( $wp_taxonomies, $args, 'and', 'label' );

		return empty( $taxonomies ) ? false : [ 'off' => esc_html__( 'None', 'cpseo' ) ] + $taxonomies;
	}

	/**
	 * Compare values.
	 *
	 * @param  integer $value1     Old value.
	 * @param  integer $value2     New Value.
	 * @param  bool    $percentage Treat as percentage.
	 * @return float
	 */
	public static function compare_values( $value1, $value2, $percentage = false ) {
		$diff = round( ( $value2 - $value1 ), 2 );

		if ( ! $percentage ) {
			return (float) $diff;
		}

		if ( $value1 ) {
			$diff = round( ( ( $diff / $value1 ) * 100 ), 2 );
			if ( ! $value2 ) {
				$diff = -100;
			}
		} elseif ( $value2 ) {
			$diff = 100;
		}

		return (float) $diff;
	}

	/**
	 * Check if current page is post create/edit screen.
	 *
	 * @return bool
	 */
	public static function is_post_edit() {
		global $pagenow;

		return in_array( $pagenow, [ 'post.php', 'post-new.php' ], true );
	}

	/**
	 * Check if current page is term create/edit screen.
	 *
	 * @return bool
	 */
	public static function is_term_edit() {
		global $pagenow;
		return 'term.php' === $pagenow;
	}

	/**
	 * Check if current page is user create/edit screen.
	 *
	 * @return bool
	 */
	public static function is_user_edit() {
		global $pagenow;

		return in_array( $pagenow, [ 'profile.php', 'user-edit.php' ], true );
	}

	/**
	 * Check if current page is user or term create/edit screen.
	 *
	 * @return bool
	 */
	public static function is_term_profile_page() {
		global $pagenow;

		return in_array( $pagenow, [ 'term.php', 'profile.php', 'user-edit.php' ], true );
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

}
