<?php
/**
 * The breadcrumb settings.
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Settings
 */

use Classic_SEO\Helper;

$cmb->add_field([
	'id'      => 'cpseo_breadcrumbs',
	'type'    => 'switch',
	'name'    => esc_html__( 'Enable breadcrumbs function', 'cpseo' ),
	'desc'    => esc_html__( 'Turning off breadcrumbs will hide breadcrumbs inserted in template files too.', 'cpseo' ),
	'default' => 'off',
]);

$dependency = [ [ 'cpseo_breadcrumbs', 'on' ] ];
$cmb->add_field([
	'id'              => 'cpseo_breadcrumbs_separator',
	'type'            => 'radio_inline',
	'name'            => esc_html__( 'Separator Character', 'cpseo' ),
	'desc'            => esc_html__( 'Separator character or string that appears between breadcrumb items.', 'cpseo' ),
	'options'         => Helper::choices_separator( Helper::get_settings( 'general.cpseo_breadcrumbs_separator' ) ),
	'default'         => '-',
	'dep'             => $dependency,
	'sanitization_cb' => [ '\Classic_SEO\CMB2', 'sanitize_htmlentities' ],
]);

$cmb->add_field([
	'id'      => 'cpseo_breadcrumbs_home',
	'type'    => 'switch',
	'name'    => esc_html__( 'Show Homepage Link', 'cpseo' ),
	'desc'    => wp_kses_post( __( 'Display homepage breadcrumb in trail.', 'cpseo' ) ),
	'default' => 'on',
	'dep'     => $dependency,
]);

$dependency_home   = [ 'relation' => 'and' ] + $dependency;
$dependency_home[] = [ 'cpseo_breadcrumbs_home', 'on' ];
$cmb->add_field([
	'id'      => 'cpseo_breadcrumbs_home_label',
	'type'    => 'text',
	'name'    => esc_html__( 'Homepage label', 'cpseo' ),
	'desc'    => esc_html__( 'Label used for homepage link (first item) in breadcrumbs.', 'cpseo' ),
	'default' => esc_html__( 'Home', 'cpseo' ),
	'dep'     => $dependency_home,
]);

$cmb->add_field([
	'id'      => 'cpseo_breadcrumbs_home_link',
	'type'    => 'text',
	'name'    => esc_html__( 'Homepage Link', 'cpseo' ),
	'desc'    => esc_html__( 'Link to use for homepage (first item) in breadcrumbs.', 'cpseo' ),
	'default' => get_home_url(),
	'dep'     => $dependency_home,
]);

$cmb->add_field([
	'id'   => 'cpseo_breadcrumbs_prefix',
	'type' => 'text',
	'name' => esc_html__( 'Prefix Breadcrumb', 'cpseo' ),
	'desc' => esc_html__( 'Prefix for the breadcrumb path.', 'cpseo' ),
	'dep'  => $dependency,
]);

$cmb->add_field([
	'id'      => 'cpseo_breadcrumbs_archive_format',
	'type'    => 'text',
	'name'    => esc_html__( 'Archive Format', 'cpseo' ),
	'desc'    => esc_html__( 'Format the label used for archive pages.', 'cpseo' ),
	/* translators: placeholder */
	'default' => esc_html__( 'Archives for %s', 'cpseo' ),
	'dep'     => $dependency,
]);

$cmb->add_field([
	'id'      => 'cpseo_breadcrumbs_search_format',
	'type'    => 'text',
	'name'    => esc_html__( 'Search Results Format', 'cpseo' ),
	'desc'    => esc_html__( 'Format the label used for search results pages.', 'cpseo' ),
	/* translators: placeholder */
	'default' => esc_html__( 'Results for %s', 'cpseo' ),
	'dep'     => $dependency,
]);

$cmb->add_field([
	'id'      => 'cpseo_breadcrumbs_404_label',
	'type'    => 'text',
	'name'    => esc_html__( '404 label', 'cpseo' ),
	'desc'    => esc_html__( 'Label used for 404 error item in breadcrumbs.', 'cpseo' ),
	'default' => esc_html__( '404 Error: page not found', 'cpseo' ),
	'dep'     => $dependency,
]);

$cmb->add_field([
	'id'      => 'cpseo_breadcrumbs_remove_post_title',
	'type'    => 'switch',
	'name'    => esc_html__( 'Hide Post Title', 'cpseo' ),
	'desc'    => wp_kses_post( __( 'Hide Post title from Breadcrumb.', 'cpseo' ) ),
	'default' => 'off',
	'dep'     => $dependency,
]);

$cmb->add_field([
	'id'      => 'cpseo_breadcrumbs_ancestor_categories',
	'type'    => 'switch',
	'name'    => esc_html__( 'Show Category(s)', 'cpseo' ),
	'desc'    => esc_html__( 'If category is a child category, show all ancestor categories.', 'cpseo' ),
	'default' => 'off',
	'dep'     => $dependency,
]);

$cmb->add_field([
	'id'      => 'cpseo_breadcrumbs_hide_taxonomy_name',
	'type'    => 'switch',
	'name'    => esc_html__( 'Hide Taxonomy Name', 'cpseo' ),
	'desc'    => wp_kses_post( __( 'Hide Taxonomy Name from Breadcrumb.', 'cpseo' ) ),
	'default' => 'off',
	'dep'     => $dependency,
]);

if ( 'page' === get_option( 'show_on_front' ) && 0 < get_option( 'page_for_posts' ) ) {
	$cmb->add_field([
		'id'      => 'cpseo_breadcrumbs_blog_page',
		'type'    => 'switch',
		'name'    => esc_html__( 'Show Blog Page', 'cpseo' ),
		'desc'    => esc_html__( 'Show Blog Page in Breadcrumb.', 'cpseo' ),
		'default' => 'off',
		'dep'     => $dependency,
	]);
}
