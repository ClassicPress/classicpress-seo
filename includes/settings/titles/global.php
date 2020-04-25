<?php
/**
 * The general settings.
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Settings
 */

use Classic_SEO\Helper;

$cmb->add_field([
	'id'				=> 'cpseo_metabox_priority',
	'type'				=> 'radio_inline',
	'name'				=> esc_html__( 'Position of metabox', 'cpseo' ),
	'desc'				=> esc_html__( 'The position of the Classic SEO metabox on the admin post edit page', 'cpseo' ),
	'options'	=> [
		'high'			=> esc_html__( 'High', 'cpseo' ),
		'core'			=> esc_html__( 'Core', 'cpseo' ),
		'default'		=> esc_html__( 'Default', 'cpseo' ),
		'low'			=> esc_html__( 'Low', 'cpseo' ),
	],
	'default'			=> 'default',
]);

$cmb->add_field([
	'id'				=> 'cpseo_robots_global',
	'type'				=> 'multicheck',
	'name'				=> esc_html__( 'Robots Meta', 'cpseo' ),
	'desc'				=> esc_html__( 'Default values for robots meta tag. These can be changed for individual posts, taxonomies, etc.', 'cpseo' ),
	'options'			=> Helper::choices_robots(),
	'select_all_button'	=> false,
]);

$cmb->add_field([
	'id'				=> 'cpseo_advanced_robots_global',
	'type'				=> 'advanced_robots',
	'name'				=> esc_html__( 'Advanced Robots Meta', 'cpseo' ),
	'sanitization_cb'	=> [ '\Classic_SEO\CMB2', 'sanitize_advanced_robots' ],
]);

$cmb->add_field([
	'id'				=> 'cpseo_noindex_empty_taxonomies',
	'type'				=> 'switch',
	'name'				=> esc_html__( 'Noindex Empty Category and Tag Archives', 'cpseo' ),
	'desc'				=> wp_kses_post( __( 'Setting empty archives to <code>noindex</code> is useful for avoiding indexation of thin content pages and dilution of page rank. As soon as a post is added, the page is updated to <code>index</code>.', 'cpseo' ) ),
	'default'			=> Helper::get_settings( 'titles.cpseo_noindex_empty_taxonomies' ) ? 'on' : 'off',
]);

$cmb->add_field([
	'id'				=> 'cpseo_title_separator',
	'type'				=> 'radio_inline',
	'name'				=> esc_html__( 'Separator Character', 'cpseo' ),
	'desc'				=> wp_kses_post( __( 'You can use the separator character in titles by inserting <code>%separator%</code> or <code>%sep%</code> in the title fields.', 'cpseo' ) ), // phpcs:ignore
	'options'			=> Helper::choices_separator( Helper::get_settings( 'titles.cpseo_title_separator' ) ),
	'default'			=> '|',
	'attributes'		=> [ 'data-preview' => 'title' ],
	'sanitization_cb'	=> [ '\Classic_SEO\CMB2', 'sanitize_htmlentities' ],
]);

if ( ! current_theme_supports( 'title-tag' ) ) {
	$cmb->add_field([
		'id'			=> 'cpseo_rewrite_title',
		'type'			=> 'switch',
		'name'			=> esc_html__( 'Rewrite Titles', 'cpseo' ),
		'desc'			=> esc_html__( 'Your current theme doesn\'t support title-tag. Enable this option to rewrite page, post, category, search and archive page titles.', 'cpseo' ),
		'default'		=> 'off',
	]);
}

$cmb->add_field([
	'id'				=> 'cpseo_capitalize_titles',
	'type'				=> 'switch',
	'name'				=> esc_html__( 'Capitalize Titles', 'cpseo' ),
	'desc'				=> esc_html__( 'Automatically capitalize the first character of all title tags.', 'cpseo' ),
	'default'			=> 'off',
]);

$cmb->add_field([
	'id'				=> 'cpseo_open_graph_image',
	'type'				=> 'file',
	'name'				=> esc_html__( 'OpenGraph Thumbnail', 'cpseo' ),
	'desc'				=> esc_html__( 'When a featured image is not set, this image will be used as a thumbnail when your post is shared on Facebook. Recommended image size 1200 x 630 pixels.', 'cpseo' ),
	'options'			=> [ 'url' => false ],
	'class'				=> 'button-primary',
]);

$cmb->add_field([
	'id'				=> 'cpseo_twitter_card_type',
	'type'				=> 'select',
	'name'				=> esc_html__( 'Twitter Card Type', 'cpseo' ),
	'desc'				=> esc_html__( 'Card type selected when creating a new post. This will also be applied for posts without a card type selected.', 'cpseo' ),
	'options'	=> [
		'summary_large_image'	=> esc_html__( 'Summary Card with Large Image', 'cpseo' ),
		'summary_card'			=> esc_html__( 'Summary Card', 'cpseo' ),
	],
	'default'			=> 'summary_large_image',
]);
