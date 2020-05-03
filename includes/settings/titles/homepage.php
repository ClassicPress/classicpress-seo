<?php
/**
 * The homepage/frontpage settings.
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Settings
 */

use Classic_SEO\Helper;

if ( 'page' === get_option( 'show_on_front' ) ) {
	return;
}

$cmb->add_field([
	'id'              => 'cpseo_homepage_title',
	'type'            => 'text',
	'name'            => esc_html__( 'Homepage Title', 'cpseo' ),
	'desc'            => esc_html__( 'Homepage title tag.', 'cpseo' ),
	'classes'         => 'cpseo-supports-variables cpseo-title',
	'default'         => '%sitename% %page% %sep% %sitedesc%',
	'sanitization_cb' => false,
	'attributes'      => [ 'data-exclude-variables' => 'seo_title,seo_description' ],
]);

$cmb->add_field([
	'id'              => 'cpseo_homepage_description',
	'type'            => 'textarea_small',
	'name'            => esc_html__( 'Homepage Meta Description', 'cpseo' ),
	'desc'            => esc_html__( 'Homepage meta description.', 'cpseo' ),
	'classes'         => 'cpseo-supports-variables cpseo-description',
	'sanitization_cb' => true,
	'attributes'      => [
		'class'             => 'cmb2_textarea wp-exclude-emoji',
		'data-gramm_editor' => 'false',
		'data-exclude-variables'  => 'seo_title,seo_description',
	],
]);

$cmb->add_field([
	'id'      => 'cpseo_homepage_custom_robots',
	'type'    => 'switch',
	'name'    => esc_html__( 'Homepage Robots Meta', 'cpseo' ),
	'desc'    => wp_kses_post( __( 'Select custom robots meta for homepage, such as <code>nofollow</code>, <code>noarchive</code>, etc. Otherwise the default meta will be used, as set in the Global Meta tab.', 'cpseo' ) ),
	'options' => [
		'off' => esc_html__( 'Default', 'cpseo' ),
		'on'  => esc_html__( 'Custom', 'cpseo' ),
	],
	'default' => 'off',
]);

$cmb->add_field([
	'id'                => 'cpseo_homepage_robots',
	'type'              => 'multicheck',
	'name'              => esc_html__( 'Homepage Robots Meta', 'cpseo' ),
	'desc'              => esc_html__( 'Custom values for robots meta tag on homepage.', 'cpseo' ),
	'options'           => Helper::choices_robots(),
	'select_all_button' => false,
	'dep'               => [ [ 'cpseo_homepage_custom_robots', 'on' ] ],
]);

$cmb->add_field([
	'id'              => 'cpseo_homepage_advanced_robots',
	'type'            => 'advanced_robots',
	'name'            => esc_html__( 'Homepage Advanced Robots', 'cpseo' ),
	'sanitization_cb' => [ '\Classic_SEO\CMB2', 'sanitize_advanced_robots' ],
	'dep'             => [ [ 'homepage_custom_robots', 'on' ] ],
]);

$cmb->add_field([
	'id'   => 'cpseo_homepage_facebook_title',
	'type' => 'text',
	'name' => esc_html__( 'Homepage Title for Social Media', 'cpseo' ),
	'desc' => esc_html__( 'Title of your site when shared on Facebook, Twitter and other social networks.', 'cpseo' ),
]);

$cmb->add_field([
	'id'   => 'cpseo_homepage_facebook_description',
	'type' => 'textarea_small',
	'name' => esc_html__( 'Homepage Description for Social Media', 'cpseo' ),
	'desc' => esc_html__( 'Description of your site when shared on Facebook, Twitter and other social networks.', 'cpseo' ),
]);

$cmb->add_field([
	'id'   => 'cpseo_homepage_facebook_image',
	'type' => 'file',
	'name' => esc_html__( 'Homepage Thumbnail for Social Media', 'cpseo' ),
	'desc' => esc_html__( 'Image displayed when your homepage is shared on Facebook and other social networks. Use images that are at least 1200 x 630 pixels for the best display on high resolution devices.', 'cpseo' ),
]);
