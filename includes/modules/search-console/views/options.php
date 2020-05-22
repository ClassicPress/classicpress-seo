<?php
/**
 * Search console options.
 *
 * @package    Classic_SEO
 */

use Classic_SEO\Helper;
use Classic_SEO\Helpers\Str;
use Classic_SEO\Search_Console\DB;
use Classic_SEO\Search_Console\Data_Fetcher;

$data           = Helper::search_console_data();
$db_info        = DB::info();
$is_empty       = DB::is_empty();
$is_queue_empty = Data_Fetcher::get()->is_empty();

$primary   = '<button type="button" class="button button-primary">' . ( $data['authorized'] ? esc_html__( 'De-authorize Account', 'cpseo' ) : esc_html__( 'Authorize', 'cpseo' ) ) . '</button>';
$secondary = '<a href="' . esc_url( Helper::get_console_auth_url() ) . '" class="button button-secondary"' . ( $data['authorized'] ? ' style="display:none;"' : '' ) . '>' . esc_html__( 'Get Authorization Code', 'cpseo' ) . '</a><br />';
$cmb->add_field([
	'id'          => 'console_authorization_code',
	'type'        => 'text',
	'name'        => esc_html__( 'Search Console', 'cpseo' ),
	'desc'        => esc_html__( 'Authorize the plugin to access data from Google Search Console.', 'cpseo' ),
	'attributes'  => [ 'data-authorized' => $data['authorized'] ? 'true' : 'false' ],
	'after_field' => $primary . $secondary,
]);

$profile       = Helper::get_settings( 'general.cpseo_console_profile' );
$profile_label = str_replace( 'sc-domain:', __( 'Domain Property: ', 'cpseo' ), $profile );
foreach ( $data['profiles'] as $key => $value ) {
	$data['profiles'][ $key ] = str_replace( 'sc-domain:', __( 'Domain Property: ', 'cpseo' ), $value );
}
if ( ! $data['authorized'] ) {
	$profile = false;
}

$cmb->add_field([
	'id'          => 'cpseo_console_profile',
	'type'        => 'select',
	'name'        => esc_html__( 'Search Console Profile', 'cpseo' ),
	'desc'        => esc_html__( 'After authenticating with Google Search Console, select the site from the dropdown list.', 'cpseo' ) .
		' <span id="gsc-dp-info" class="hidden">' . __( 'Please note that the Sitemaps overview in the Search Console module will not be available when using a Domain Property.', 'cpseo' ) . '</span>' .
		/* translators: setting url */
		'<br><br><span style="color: orange;">' . sprintf( __( 'Is your site not listed? <a href="%1$s" target="_blank">Click here</a> to get your website verified.', 'cpseo' ), Helper::get_admin_url( 'options-general#setting-panel-webmaster' ) ) . '</span>',
	'options'     => $profile ? [ $profile => $profile_label ] : $data['profiles'],
	'default'     => $profile,
	'after_field' => '<button class="button button-primary hidden" ' . ( $data['authorized'] ? '' : 'disabled="disabled"' ) . '>' . esc_html__( 'Refresh Sites', 'cpseo' ) . '</button>',
	'attributes'  => $data['authorized'] ? [ 'data-s2' => '' ] : [
		'disabled' => 'disabled',
		'data-s2'  => '',
	],
]);

if ( $is_empty ) {
	$cmb->add_field([
		'id'      => 'console_data_empty',
		'type'    => 'notice',
		'what'    => 'error',
		/* translators: date */
		'content' => sprintf( __( 'The data sets are empty in your cache. You can wait for the next cronjob (%s) or <strong>Update Manually</strong>.', 'cpseo' ), date_i18n( 'd/m/Y 00:00:00', strtotime( '+1 days' ) ) ) .
		'<p class="note">' . __( '<strong>Note:</strong> Please update your data by clicking on \'Update Manually\'. Google only stores data from the last 90 days - a dataset older than that can\'t be updated anymore.', 'cpseo' ) . '</p>',
	]);
}

$disable = ( ! $data['authorized'] || ! $is_queue_empty ) ? true : false;

$cmb->add_field([
	'id'          => 'console_caching_control',
	'type'        => 'text',
	'name'        => __( 'Cache Limit <br><small>Days to keep data rows in cache</small>', 'cpseo' ),
	'default'     => 90,
	'after_field' => '<br>' .
	'<button class="button console-cache-delete"  data-days="90">' . esc_html__( 'Delete Recent Cache (last 90 days)', 'cpseo' ) . '</button>' .
	'&nbsp;&nbsp;<button class="button console-cache-delete" data-days="-1">' . esc_html__( 'Delete Cache', 'cpseo' ) . '</button>' .
	'&nbsp;&nbsp;<button class="button console-cache-update-manually"' . ( $disable ? ' disabled="disabled"' : '' ) . '>' . ( $is_queue_empty ? esc_html__( 'Update Cache manually', 'cpseo' ) : esc_html__( 'Fetching in Progress', 'cpseo' ) ) . '</button><br>' .
	/* translators: number of days */
	'<div class="cpseo-console-db-info"><span class="dashicons dashicons-calendar-alt"></span> ' . sprintf( esc_html__( 'Cached Days: %s', 'cpseo' ), '<strong>' . $db_info['days'] . '</strong>' ) . '</div>' .
	/* translators: number of rows */
	'<div class="cpseo-console-db-info"><span class="dashicons dashicons-editor-ul"></span> ' . sprintf( esc_html__( 'Data Rows: %s', 'cpseo' ), '<strong>' . Str::human_number( $db_info['rows'] ) . '</strong>' ) . '</div>' .
	/* translators: database size */
	'<div class="cpseo-console-db-info"><span class="dashicons dashicons-editor-code"></span> ' . sprintf( esc_html__( 'Size: %s', 'cpseo' ), '<strong>' . size_format( $db_info['size'] ) . '</strong>' ) . '</div>',
]);
