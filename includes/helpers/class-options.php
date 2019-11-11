<?php
/**
 * The Options helpers.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Helpers
 */

namespace Classic_SEO\Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * Options class.
 */
trait Options {

	/**
	 * Option handler.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param  string $key   Option to perform action.
	 * @param  mixed  $value Pass null to get option,
	 *                       Pass false to delete option,
	 *                       Pass value to update option.
	 * @return mixed
	 */
	public static function option( $key, $value = null ) {
		$key = 'cpseo_' . $key;

		if ( false === $value ) {
			return delete_option( $key );
		}

		if ( is_null( $value ) ) {
			return get_option( $key, [] );
		}

		return update_option( $key, $value );
	}

	/**
	 * Normalize option value.
	 *
	 * @param mixed $value Value to normalize.
	 * @return mixed
	 */
	public static function normalize_data( $value ) {

		if ( 'true' === $value || 'on' === $value ) {
			$value = true;
		} elseif ( 'false' === $value || 'off' === $value ) {
			$value = false;
		} elseif ( '0' === $value || '1' === $value ) {
			$value = intval( $value );
		}

		return $value;
	}

	/**
	 * Update settings.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param array|null $general Array to update settings.
	 * @param array|null $titles  Array to update settings.
	 * @param array|null $sitemap Array to update settings.
	 */
	public static function update_all_settings( $general, $titles, $sitemap ) {

		if ( ! is_null( $general ) ) {
			update_option( 'cpseo-options-general', $general );
		}

		if ( ! is_null( $titles ) ) {
			update_option( 'cpseo-options-titles', $titles );
		}

		if ( ! is_null( $sitemap ) ) {
			update_option( 'cpseo-options-sitemap', $sitemap );
		}
	}
}
