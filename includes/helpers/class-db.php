<?php
/**
 * The database helpers.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Helpers
 */

namespace Classic_SEO\Helpers;

use Classic_SEO\Database\Database;

/**
 * DB class.
 */
class DB {

	/**
	 * Retrieve a Database instance by table name.
	 *
	 * @param string $table_name A Database instance id.
	 *
	 * @return Database Database object instance.
	 */
	public static function query_builder( $table_name ) {
		return Database::table( $table_name );
	}

	/**
	 * Check if table exists in db or not.
	 *
	 * @param string $table_name Table name to check for existance.
	 *
	 * @return bool
	 */
	public static function check_table_exists( $table_name ) {
		global $wpdb;

		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $wpdb->prefix . $table_name ) ) ) === $wpdb->prefix . $table_name ) {
			return true;
		}

		return false;
	}
}
