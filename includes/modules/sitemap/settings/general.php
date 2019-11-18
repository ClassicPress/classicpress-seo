<?php
/**
 * Sitemap - General
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Sitemap
 */

$cmb->add_field( array(
	'id'         => 'cpseo_items_per_page',
	'type'       => 'text',
	'name'       => esc_html__( 'Links Per Sitemap', 'cpseo' ),
	'desc'       => esc_html__( 'Max number of links on each sitemap page.', 'cpseo' ),
	'default'    => '200',
	'attributes' => array( 'type' => 'number' ),
) );

$cmb->add_field( array(
	'id'      => 'cpseo_include_images',
	'type'    => 'switch',
	'name'    => esc_html__( 'Include Post Images in Sitemaps', 'cpseo' ),
	'desc'    => wp_kses_post( __( 'Include images in sitemaps. Helps search engines to index the important images on your pages. <strong>Note that this is NOT the same as indexing the image attachment page URL.</strong>', 'cpseo' ) ),
	'default' => 'on',
) );

$cmb->add_field( array(
	'id'      => 'cpseo_include_featured_image',
	'type'    => 'switch',
	'name'    => esc_html__( 'Include Featured Images', 'cpseo' ),
	'desc'    => esc_html__( 'Include the Featured Image too, even if it does not appear directly in the post content.', 'cpseo' ),
	'default' => 'on',
	'dep'     => array( array( 'cpseo_include_images', 'on' ) ),
) );

$cmb->add_field( array(
	'id'   => 'cpseo_exclude_posts',
	'type' => 'text',
	'name' => esc_html__( 'Exclude Posts', 'cpseo' ),
	'desc' => esc_html__( 'Enter post IDs of posts you want to exclude from the sitemap, separated by commas. This option **applies** to all posts types including posts, pages, and custom post types.', 'cpseo' ),
) );

$cmb->add_field( array(
	'id'   => 'cpseo_exclude_terms',
	'type' => 'text',
	'name' => esc_html__( 'Exclude Terms', 'cpseo' ),
	'desc' => esc_html__( 'Add term IDs, separated by comma. This option is applied for all taxonomies.', 'cpseo' ),
) );

$cmb->add_field( array(
	'id'      => 'cpseo_ping_search_engines',
	'type'    => 'switch',
	'name'    => esc_html__( 'Ping Search Engines', 'cpseo' ),
	'desc'    => esc_html__( 'Automatically notify Google &amp; Bing when a sitemap gets updated.', 'cpseo' ),
	'default' => 'on',
) );
