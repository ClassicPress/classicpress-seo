<?php
/**
 * Metabox - Advance Tab
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Metaboxes
 */

use Classic_SEO\Helper;
use Classic_SEO\Admin\Admin_Helper;

$robot_index = [
	'index' => esc_html__( 'Index', 'cpseo' ) . Admin_Helper::get_tooltip( esc_html__( 'Instructs search engines to index and show these pages in the search results.', 'cpseo' ) ),
];

$cmb->add_field( array(
	'id'                => 'cpseo_robots',
	'type'              => 'multicheck',
	'name'              => esc_html__( 'Robots Meta', 'cpseo' ),
	'desc'              => esc_html__( 'Custom values for robots meta tag.', 'cpseo' ),
	'options'           => $robot_index + Helper::choices_robots(),
	'default_cb'        => '\\Classic_SEO\\Helper::get_robots_defaults',
	'select_all_button' => false,
) );

$cmb->add_field( array(
	'id'              => 'cpseo_advanced_robots',
	'type'            => 'advanced_robots',
	'name'            => esc_html__( 'Advanced Robots Meta', 'cpseo' ),
	'sanitization_cb' => [ '\Classic_SEO\CMB2', 'sanitize_advanced_robots' ],
) );

$cmb->add_field( array(
	'id'   => 'cpseo_canonical_url',
	'type' => 'text',
	'name' => esc_html__( 'Canonical URL', 'cpseo' ),
	'desc' => esc_html__( 'The canonical URL informs search crawlers which page is the main page if you have double content.', 'cpseo' ),
) );

if ( Helper::get_settings( 'general.cpseo_breadcrumbs' ) ) {
	$cmb->add_field( array(
		'id'   => 'cpseo_breadcrumb_title',
		'type' => 'text',
		'name' => esc_html__( 'Breadcrumb Title', 'cpseo' ),
		'desc' => esc_html__( 'Breadcrumb Title to use for this post', 'cpseo' ),
	) );
}
