<?php
/**
 * The API helpers.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Helpers
 */


namespace Classic_SEO\Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * API class.
 */
trait Api {

	/**
	 * Add notification.
	 *
	 * @param string $message Message string.
	 * @param array  $options Set of options.
	 */
	public static function add_notification( $message, $options = [] ) {
		cpseo()->notification->add( $message, $options );
	}

	/**
	 * Remove notification.
	 *
	 * @param string $notification_id Notification id.
	 */
	public static function remove_notification( $notification_id ) {
		cpseo()->notification->remove_by_id( $notification_id );
	}

	/**
	 * Get Setting.
	 *
	 * @param  string $field_id The field id to get value for.
	 * @param  mixed  $default  The default value if no field found.
	 * @return mixed
	 */
	public static function get_settings( $field_id = '', $default = false ) {
		return cpseo()->settings->get( $field_id, $default );
	}

	/**
	 * Add something to the JSON object.
	 *
	 * @param string $key         Unique identifier.
	 * @param mixed  $value       The data itself can be either a single or an array.
	 * @param string $object_name Name for the JavaScript object. Passed directly, so it should be qualified JS variable.
	 */
	public static function add_json( $key, $value, $object_name = 'classicSEO' ) {
		cpseo()->json->add( $key, $value, $object_name );
	}

	/**
	 * Remove something from the JSON object.
	 *
	 * @param string $key         Unique identifier.
	 * @param string $object_name Name for the JavaScript object. Passed directly, so it should be qualified JS variable.
	 */
	public static function remove_json( $key, $object_name = 'classicSEO' ) {
		cpseo()->json->remove( $key, $object_name );
	}
}
