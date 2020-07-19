<?php
/**
 * The misc settings.
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Settings
 */

use Classic_SEO\Helper;

$dep = [ [ 'cpseo_disable_date_archives', 'off' ] ];

$cmb->add_field([
	'id'      => 'cpseo_disable_date_archives',
	'type'    => 'switch',
	'name'    => esc_html__( 'Date Archives', 'cpseo' ),
	'desc'    => wp_kses_post( __( 'If this option is set to <strong>Enabled</strong>, you will be able to access your posts\' date archive pages. For example, if you have written three posts, one on 2019/11/02, the second on 2019/11/17 and the third on 2019/11/28, you\'ll be able to view all three posts by going to <code>mydomain.com/2019/11/</code>. If this option is set to <strong>Disabled</strong>, the date archive is, instead, redirected to the homepage.', 'cpseo' ) ),
	'options' => [
		'on'  => esc_html__( 'Disabled', 'cpseo' ),
		'off' => esc_html__( 'Enabled', 'cpseo' ),
	],
	'default' => 'off',
]);

$cmb->add_field([
	'id'              => 'cpseo_date_archive_title',
	'type'            => 'text',
	'name'            => esc_html__( 'Date Archive Title', 'cpseo' ),
	'desc'            => esc_html__( 'Title tag on day/month/year based archives.', 'cpseo' ),
	'classes'         => 'cpseo-supports-variables cpseo-title',
	'default'         => '%date% %page% %sep% %sitename%',
	'dep'             => $dep,
	'sanitization_cb' => false,
	'attributes'      => [ 'data-exclude-variables' => 'seo_title,seo_description' ],
]);

$cmb->add_field([
	'id'              => 'cpseo_date_archive_description',
	'type'            => 'textarea_small',
	'name'            => esc_html__( 'Date Archive Description', 'cpseo' ),
	'desc'            => esc_html__( 'Date archive description.', 'cpseo' ),
	'classes'         => 'cpseo-supports-variables cpseo-description',
	'dep'             => $dep,
	'sanitization_cb' => false,
	'attributes'      => [
		'class'             => 'cmb2-textarea-small wp-exclude-emoji',
		'data-gramm_editor' => 'false',
		'data-exclude-variables'  => 'seo_title,seo_description',
	],
]);

$cmb->add_field([
	'id'              => 'cpseo_search_title',
	'type'            => 'text',
	'name'            => esc_html__( 'Search Results Title', 'cpseo' ),
	'desc'            => esc_html__( 'Title tag on search results page.', 'cpseo' ),
	'classes'         => 'cpseo-supports-variables cpseo-title',
	'default'         => '%search_query% %page% %sep% %sitename%',
	'sanitization_cb' => false,
	'attributes'      => [ 'data-exclude-variables' => 'seo_title,seo_description' ],
]);

$cmb->add_field([
	'id'              => 'cpseo_404_title',
	'type'            => 'text',
	'name'            => esc_html__( '404 Title', 'cpseo' ),
	'desc'            => esc_html__( 'Title tag on 404 Not Found error page.', 'cpseo' ),
	'classes'         => 'cpseo-supports-variables cpseo-title',
	'default'         => 'Page Not Found %sep% %sitename%',
	'sanitization_cb' => false,
	'attributes'      => [ 'data-exclude-variables' => 'seo_title,seo_description' ],
]);

$cmb->add_field([
	'id'                => 'cpseo_date_archive_robots',
	'type'              => 'multicheck',
	/* translators: post type name */
	'name'              => esc_html__( 'Date Robots Meta', 'cpseo' ),
	'desc'              => esc_html__( 'Custom values for robots meta tag on date page.', 'cpseo' ),
	'options'           => Helper::choices_robots(),
	'select_all_button' => false,
	'dep'               => $dep,
]);

$cmb->add_field([
	'id'              => 'cpseo_date_advanced_robots',
	'type'            => 'advanced_robots',
	'name'            => esc_html__( 'Date Advanced Robots', 'cpseo' ),
	'sanitization_cb' => [ '\Classic_SEO\CMB2', 'sanitize_advanced_robots' ],
	'dep'             => $dep,
]);

$cmb->add_field([
	'id'      => 'cpseo_noindex_search',
	'type'    => 'switch',
	'name'    => esc_html__( 'Noindex Search Results', 'cpseo' ),
	'desc'    => esc_html__( 'Prevent search results pages from getting indexed by search engines. Search results could be considered to be thin content and prone to duplicate content issues.', 'cpseo' ),
	'default' => 'on',
]);

$cmb->add_field([
	'id'      => 'cpseo_noindex_paginated_pages',
	'type'    => 'switch',
	'name'    => esc_html__( 'Noindex Paginated Pages', 'cpseo' ),
	'desc'    => wp_kses_post( __( 'Set this to on to prevent /page/2 and further of any archive to show up in the search results.', 'cpseo' ) ),
	'default' => 'off',
]);

$cmb->add_field([
	'id'      => 'cpseo_noindex_archive_subpages',
	'type'    => 'switch',
	'name'    => esc_html__( 'Noindex Archive Subpages', 'cpseo' ),
	'desc'    => esc_html__( 'Prevent paginated archive pages from getting indexed by search engines.', 'cpseo' ),
	'default' => 'off',
]);

$cmb->add_field([
	'id'      => 'cpseo_noindex_password_protected',
	'type'    => 'switch',
	'name'    => esc_html__( 'Noindex Password Protected Pages', 'cpseo' ),
	'desc'    => esc_html__( 'Prevent password protected pages & posts from getting indexed by search engines.', 'cpseo' ),
	'default' => 'off',
]);
