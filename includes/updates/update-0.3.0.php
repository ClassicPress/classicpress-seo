<?php
/**
 * The Updates routine for version 0.3.0.
 *
 * @since      0.3.0
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Updates
 */

use Classic_SEO\Helper;
use Classic_SEO\Helpers\DB;
use Classic_SEO\Redirections\DB as Redirections_DB;

/**
 * Create and update table schema
 *
 * @since 0.3.0
 */
function cpseo_0_3_0_update_tables() {
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	
	$tables = [ '404_logs', 'redirections', 'redirections_cache', 'internal_links', 'internal_meta' ];
	$meta_tables = [ 'options', 'postmeta', 'usermeta' ];
	
	// Rename old tables.
	foreach ( $tables as $table ) {
		cpseo_0_3_0_rename_tables( $table );
	}
	
	// Rename values in options and meta tables.
	foreach ( $meta_tables as $meta_table ) {
		cpseo_0_3_0_rename_table_settings_data( $meta_table );
	}

}

/**
 * Replace old namespace with new namespace in database tables
 *
 * @since 0.3.0
 */
function cpseo_0_3_0_rename_tables( $table_name ) {
	global $wpdb;
	$old_table_name = 'cpseo_' . esc_sql( $table_name );
	$new_table_name = 'cpseo_' . esc_sql( $table_name );
	
	if ( DB::check_table_exists( $old_table_name ) ) {
		$wpdb->query( "ALTER TABLE {$wpdb->prefix}{$old_table_name} RENAME TO {$wpdb->prefix}{$new_table_name} " ); // phpcs:ignore
	}
}

/**
 * Replace cpseo with cpseo table column values
 *
 * @since 0.3.0
 */
function cpseo_0_3_0_rename_table_settings_data( $table_name ) {
	global $wpdb;
	$old = 'cpseo';
	$new = 'cpseo';
	$update_sql = array();
	$where_sql	= array();
	$upd 		= false;


	if ( DB::check_table_exists( $table_name ) && $table_name == 'options' ) {
		// Update option_name
		$wpdb->query( "UPDATE {$wpdb->prefix}{$table_name} SET option_name = REPLACE(option_name, 'cpseo', 'cpseo')" ); // phpcs:ignore
		
		// Update option_value which contains serialized data
		$sql = "SELECT option_id, option_value FROM {$wpdb->prefix}{$table_name} WHERE option_value LIKE '%$old%' AND option_name NOT LIKE 'rank%'";
		$data = $wpdb->get_results( $sql, ARRAY_A );

		foreach ( $data as $col ) {
			$upd = false;
			$update_sql = [];
			$data_to_fix = $col['option_value'];
			$opt_id = $col['option_id'];
			$edited_data = cpseo_0_3_0_recursive_unserialize_replace_meta( $data_to_fix, false );

			if ( $edited_data != $data_to_fix ) {
				$update_sql[] = 'option_value = "' . cpseo_0_3_0_mysql_escape_mimic( $edited_data ) . '"';
				$upd = true;
			}
			
			if ( $upd ) {
				$sql 	= 'UPDATE ' . $wpdb->prefix.$table_name . ' SET ' . implode( ', ', $update_sql ) . ' WHERE option_id = ' . $opt_id;
				$result = $wpdb->query( $sql );
			}
		}

		$wpdb->flush();
	}
	
	
	if ( DB::check_table_exists( $table_name ) && $table_name == 'postmeta' ) {
		$wpdb->query( "UPDATE {$wpdb->prefix}{$table_name} SET meta_key = REPLACE(meta_key, 'cpseo', 'cpseo') " ); // phpcs:ignore
	}
	
	
	if ( DB::check_table_exists( $table_name ) && $table_name == 'usermeta' ) {
		// Update meta_key
		$wpdb->query( "UPDATE {$wpdb->prefix}{$table_name} SET meta_key = REPLACE(meta_key, 'cpseo', 'cpseo') " ); // phpcs:ignore
		
		// Update meta_value which contains serialized data
		$sql = "SELECT umeta_id, meta_value FROM {$wpdb->prefix}{$table_name} WHERE meta_value LIKE '%$old%' AND meta_key NOT LIKE 'rank%'";
		$data = $wpdb->get_results( $sql, ARRAY_A );

		foreach ( $data as $col ) {
			$upd = false;
			$update_sql = [];
			$data_to_fix = $col['meta_value'];
			$meta_id = $col['umeta_id'];
			$edited_data = cpseo_0_3_0_recursive_unserialize_replace_meta( $data_to_fix, false );

			if ( $edited_data != $data_to_fix ) {
				$update_sql[] = 'meta_value = "' . cpseo_0_3_0_mysql_escape_mimic( $edited_data ) . '"';
				$upd = true;
			}
			
			if ( $upd ) {
				$sql 	= 'UPDATE ' . $wpdb->prefix.$table_name . ' SET ' . implode( ', ', $update_sql ) . ' WHERE umeta_id = ' . $meta_id;
				$result = $wpdb->query( $sql );
			}
		}

		$wpdb->flush();
	}

}


/**
 * Adapated from interconnect/it's search/replace script.
 *
 * @link https://interconnectit.com/products/search-and-replace-for-wordpress-databases/
 *
 * Take a serialised array and unserialise it replacing elements as needed and
 * unserialising any subordinate arrays and performing the replace on those too.
 *
 * @access private
 * @param  array  			$data       		Used to pass any subordinate arrays back to in.
 * @param  boolean 			$serialised 		Does the array passed via $data need serialising.
 *
 * @return string|array	The original array with all elements replaced as needed.
 */
function cpseo_0_3_0_recursive_unserialize_replace_meta( $data, $serialised = false ) {
	$old = 'cpseo';
	$new = 'cpseo';
	
	try {

		if ( is_string( $data ) && ! is_serialized_string( $data ) && ( $unserialized = cpseo_0_3_0_unserialize( $data ) ) !== false ) {
			$data = cpseo_0_3_0_recursive_unserialize_replace_meta( $unserialized, true );
		} elseif ( is_array( $data ) ) {
			$_tmp = array( );
			foreach ( $data as $key => $value ) {
				$_tmp[ $key ] = cpseo_0_3_0_recursive_unserialize_replace_meta( $value, false );
			}
			$data = $_tmp;
			unset( $_tmp );
		} elseif ( is_object( $data ) ) {
			$_tmp = $data;
			$props = get_object_vars( $data );
			foreach ( $props as $key => $value ) {
				$_tmp->$key = cpseo_0_3_0_recursive_unserialize_replace_meta( $value, false );
			}
			$data = $_tmp;
			unset( $_tmp );
		} elseif ( is_serialized_string( $data ) ) {
			if ( $data = cpseo_0_3_0_unserialize( $data ) !== false ) {
				$data = str_replace( $old, $new, $data );
				$data = serialize( $data );
			}
		} else {
			if ( is_string( $data ) ) {
				$data = str_replace( $old, $new, $data );
			}
		}

		if ( $serialised ) {
			return serialize( $data );
		}

	} catch( Exception $error ) {

	}

	return $data;
}


/**
 * Return unserialized object or array
 *
 * @param string $serialized_string Serialized string.
 * @param string $method            The name of the caller method.
 *
 * @return mixed, false on failure
 */
function cpseo_0_3_0_unserialize( $serialized_string ) {
	if ( ! is_serialized( $serialized_string ) ) {
		return false;
	}

	$serialized_string   = trim( $serialized_string );
	$unserialized_string = @unserialize( $serialized_string );

	return $unserialized_string;
}



/**
 * Mimics the mysql_real_escape_string function but doesn't need an active mysql connection.
 * @link   https://www.php.net/manual/en/function.mysql-real-escape-string.php#101248
 * @access public
 * @param  string $input The string to escape.
 * @return string
 */
function cpseo_0_3_0_mysql_escape_mimic( $input ) {
	if ( is_array( $input ) ) {
		return array_map( __METHOD__, $input );
	}
	if ( ! empty( $input ) && is_string( $input ) ) {
		return str_replace( array( '\\', "\0", "\n", "\r", "'", '"', "\x1a" ), array( '\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z' ), $input );
	}

	return $input;
}



/**
 * De-authorized search console
 */
function cpseo_0_3_0_clear_search_console() {
	Helper::search_console_data( false );
}

/**
 * Clear SEO Analysis result.
 */
function cpseo_0_3_0_reset_options() {
	delete_option( 'cpseo_seo_analysis_results' );
}


cpseo_0_3_0_reset_options();
cpseo_0_3_0_update_tables();
cpseo_0_3_0_clear_search_console();
