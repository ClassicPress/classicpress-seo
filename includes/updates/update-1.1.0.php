<?php
/**
 * The Updates routine for version 1.1.0
 *
 * @since      1.1.0
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Updates
 */


use Classic_SEO\Helper;
use Classic_SEO\Helpers\DB;
use Classic_SEO\Admin\Admin_Helper;


/**
 * Remove table cpseo_sc_analytics as it is no longer used
 */
function cpseo_1_1_0_remove_gsc_table() {
	global $wpdb;
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$table_name = $wpdb->prefix . 'cpseo_sc_analytics';
	$sql = "DROP TABLE IF EXISTS $table_name";
	$wpdb->query( $sql );

	delete_option( 'cpseo_search_console_data' );
}
cpseo_1_1_0_remove_gsc_table();

/*
 * 	Clear cpseo_search_console_get_analytics cron job.
 */
function cpseo_1_1_0_remove_gsc_scheduled_hook() {	
	wp_clear_scheduled_hook( 'cpseo_search_console_get_analytics' );
}
cpseo_1_1_0_remove_gsc_scheduled_hook()
