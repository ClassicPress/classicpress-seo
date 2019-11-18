<?php
/**
 * Sitemap - Taxonomies
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Sitemap
 */

$taxonomy   = $tab['taxonomy'];
$prefix     = "cpseo_tax_{$taxonomy}_";
$is_enabled = 'category' === $taxonomy ? 'on' : 'off';

$cmb->add_field( array(
	'id'      => $prefix . 'sitemap',
	'type'    => 'switch',
	'name'    => esc_html__( 'Include in Sitemap', 'cpseo' ),
	'desc'    => esc_html__( 'Include archive pages for terms of this taxonomy in the XML sitemap.', 'cpseo' ),
	'default' => $is_enabled,
) );

$cmb->add_field( array(
	'id'      => $prefix . 'include_empty',
	'type'    => 'switch',
	'name'    => esc_html__( 'Include Empty Terms', 'cpseo' ),
	'desc'    => esc_html__( 'Include archive pages of terms that have no posts associated.', 'cpseo' ),
	'default' => 'off',
	'dep'     => array( array( $prefix . 'sitemap', 'on' ) ),
) );
