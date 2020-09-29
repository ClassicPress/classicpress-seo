<?php
/**
 * The Updates routine for version 2.0.0
 *
 * @since      2.0.0
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Updates
 */


use Classic_SEO\Helper;
use Classic_SEO\Helpers\DB;
use Classic_SEO\Admin\Admin_Helper;


/**
 * Remove table cpseo_sc_analytics as it is no longer used
 * Google search console feature removed in 2.0.0
 */
function cpseo_2_0_0_remove_gsc_table() {
	global $wpdb;
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$table_name = $wpdb->prefix . 'cpseo_sc_analytics';
	$sql = "DROP TABLE IF EXISTS $table_name";
	$wpdb->query( $sql );

	delete_option( 'cpseo_search_console_data' );
}
cpseo_2_0_0_remove_gsc_table();


/*
 * 	Clear cpseo_search_console_get_analytics cron job.
 */
function cpseo_2_0_0_remove_gsc_scheduled_hook() {	
	wp_clear_scheduled_hook( 'cpseo_search_console_get_analytics' );
}
cpseo_2_0_0_remove_gsc_scheduled_hook();
