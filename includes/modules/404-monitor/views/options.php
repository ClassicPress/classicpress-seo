<?php
/**
 * 404 Monitor general settings.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Monitor

 */

use Classic_SEO\Helper;

$cmb->add_field( array(
	'id'      => '404_advanced_monitor',
	'type'    => 'notice',
	'what'    => 'error',
	'content' => esc_html__( 'If you have hundreds of 404 errors, your error log might increase quickly. Only choose this option if you have a very few 404s and are unable to replicate the 404 error on a particular URL from your end.', 'cpseo' ),
	'dep'     => array( array( 'cpseo_404_monitor_mode', 'advanced' ) ),
) );

$cmb->add_field( array(
	'id'      => 'cpseo_404_monitor_mode',
	'type'    => 'radio_inline',
	'name'    => esc_html__( 'Mode', 'cpseo' ),
	'desc'    => esc_html__( 'The Simple mode only logs URI and access time, while the Advanced mode creates detailed logs including additional information such as the Referer URL.', 'cpseo' ),
	'options' => array(
		'simple'   => esc_html__( 'Simple', 'cpseo' ),
		'advanced' => esc_html__( 'Advanced', 'cpseo' ),
	),
	'default' => 'simple',
) );

$cmb->add_field( array(
	'id'         => 'cpseo_404_monitor_limit',
	'type'       => 'text',
	'name'       => esc_html__( 'Log Limit', 'cpseo' ),
	'desc'       => esc_html__( 'Sets the max number of rows in a log. Set to 0 to disable the limit.', 'cpseo' ),
	'default'    => '100',
	'attributes' => array( 'type' => 'number' ),
) );

$monitor_exclude = $cmb->add_field( array(
	'id'      => 'cpseo_404_monitor_exclude',
	'type'    => 'group',
	'name'    => esc_html__( 'Exclude Paths', 'cpseo' ),
	'desc'    => esc_html__( 'Enter URIs or keywords you wish to prevent from getting logged by the 404 monitor.', 'cpseo' ),
	'options' => array(
		'add_button'    => esc_html__( 'Add another', 'cpseo' ),
		'remove_button' => esc_html__( 'Remove', 'cpseo' ),
	),
	'classes' => 'cmb-group-text-only',
) );

$cmb->add_group_field( $monitor_exclude, array(
	'id'   => 'exclude',
	'type' => 'text',
) );
$cmb->add_group_field( $monitor_exclude, array(
	'id'      => 'comparison',
	'type'    => 'select',
	'options' => Helper::choices_comparison_types(),
) );

$cmb->add_field( array(
	'id'      => 'cpseo_404_monitor_ignore_query_parameters',
	'type'    => 'switch',
	'name'    => esc_html__( 'Ignore Query Parameters', 'cpseo' ),
	'desc'    => esc_html__( 'Turn ON to ignore all query parameters (the part after a question mark in a URL) when logging 404 errors.', 'cpseo' ),
	'default' => 'off',
) );
