<?php
/**
 * Sitemap - Post Types
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Sitemap
 */

use Classic_SEO\Helper;

$attributes = [];
$post_type  = $tab['post_type'];
$prefix     = "cpseo_pt_{$post_type}_";

if ( 'attachment' === $post_type && Helper::get_settings( 'general.cpseo_attachment_redirect_urls', true ) ) {
	$cmb->add_field( array(
		'id'      => 'cpseo_attachment_redirect_urls_notice',
		'type'    => 'notice',
		'what'    => 'warning',
		'content' => esc_html__( 'To generate attachment sitemap disable attachment redirection to parent.', 'cpseo' ),
	) );
	$attributes['disabled'] = 'disabled';
}

$cmb->add_field( array(
	'id'         => $prefix . 'sitemap',
	'type'       => 'switch',
	'name'       => esc_html__( 'Include in Sitemap', 'cpseo' ),
	'desc'       => esc_html__( 'Include this post type in the XML sitemap.', 'cpseo' ),
	'default'    => 'attachment' === $post_type ? 'off' : 'on',
	'attributes' => $attributes,
) );

if ( 'attachment' !== $post_type ) {
	$cmb->add_field( array(
		'id'   => $prefix . 'image_customfields',
		'type' => 'textarea_small',
		'name' => esc_html__( 'Image Custom Fields', 'cpseo' ),
		'desc' => esc_html__( 'Insert custom field (post meta) names which contain image URLs to include them in the sitemaps. Add one per line.', 'cpseo' ),
		'dep'  => array( array( $prefix . 'sitemap', 'on' ) ),
	) );
}
