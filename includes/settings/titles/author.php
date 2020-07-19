<?php
/**
 * The authors settings.
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Settings
 */

use Classic_SEO\Helper;

$dep = [ [ 'cpseo_disable_author_archives', 'off' ] ];

$cmb->add_field([
	'id'      => 'cpseo_disable_author_archives',
	'type'    => 'switch',
	'name'    => esc_html__( 'Author Archives', 'cpseo' ),
	'desc'    => wp_kses_post( __( 'If this option is set to <strong>Enabled</strong>, you will be able to access an author\'s archive page by going to <code>mydomain.com/author/username/</code>. Note that the <code>/author/</code> part of the URL can be changed in the setting below. The author archive page shows all posts written by <code>username</code>. If this option is set to <strong>Disabled</strong>, the author archive is, instead, redirected to the homepage. To avoid duplicate content issues, noindex author archives if you keep them enabled.', 'cpseo' ) ),
	'options' => [
		'on'  => esc_html__( 'Disabled', 'cpseo' ),
		'off' => esc_html__( 'Enabled', 'cpseo' ),
	],
	'default' => $this->do_filter( 'settings/titles/cpseo_disable_author_archives', 'off' ),
]);

$cmb->add_field([
	'id'      => 'cpseo_url_author_base',
	'type'    => 'text',
	'name'    => esc_html__( 'Author Base', 'cpseo' ),
	'desc'    => wp_kses_post( __( 'Change the <code>/author/</code> part in author archive URLs.', 'cpseo' ) ),
	'default' => 'author',
	'dep'     => $dep,
]);

$cmb->add_field([
	'id'      => 'cpseo_author_custom_robots',
	'type'    => 'switch',
	'name'    => esc_html__( 'Author Robots Meta', 'cpseo' ),
	'desc'    => wp_kses_post( __( 'Select custom robots meta for author page, such as <code>nofollow</code>, <code>noarchive</code>, etc. Otherwise the default meta will be used, as set in the Global Meta tab.', 'cpseo' ) ),
	'options' => [
		'off' => esc_html__( 'Default', 'cpseo' ),
		'on'  => esc_html__( 'Custom', 'cpseo' ),
	],
	'default' => 'on',
	'dep'     => $dep,
]);

$cmb->add_field([
	'id'                => 'cpseo_author_robots',
	'type'              => 'multicheck',
	/* translators: post type name */
	'name'              => esc_html__( 'Author Robots Meta', 'cpseo' ),
	'desc'              => esc_html__( 'Custom values for robots meta tag on author page.', 'cpseo' ),
	'options'           => Helper::choices_robots(),
	'select_all_button' => false,
	'dep'               => [
		'relation' => 'and',
		[ 'cpseo_author_custom_robots', 'on' ],
		[ 'cpseo_disable_author_archives', 'off' ],
	],
]);

$cmb->add_field([
	'id'              => 'cpseo_author_advanced_robots',
	'type'            => 'advanced_robots',
	'name'            => esc_html__( 'Author Advanced Robots', 'cpseo' ),
	'sanitization_cb' => [ '\Classic_SEO\CMB2', 'sanitize_advanced_robots' ],
	'dep'             => [
		'relation' => 'and',
		[ 'author_custom_robots', 'on' ],
		[ 'disable_author_archives', 'off' ],
	],
]);

$cmb->add_field([
	'id'              => 'cpseo_author_archive_title',
	'type'            => 'text',
	'name'            => esc_html__( 'Author Archive Title', 'cpseo' ),
	'desc'            => esc_html__( 'Title tag on author archives. SEO options for specific authors can be set with the meta box available in the user profiles.', 'cpseo' ),
	'classes'         => 'cpseo-supports-variables cpseo-title',
	'default'         => '%name% %sep% %sitename% %page%',
	'dep'             => $dep,
	'sanitization_cb' => false,
	'attributes'      => [ 'data-exclude-variables' => 'seo_title,seo_description' ],
]);

$cmb->add_field([
	'id'              => 'cpseo_author_archive_description',
	'type'            => 'textarea_small',
	'name'            => esc_html__( 'Author Archive Description', 'cpseo' ),
	'desc'            => esc_html__( 'Author archive meta description. SEO options for specific author archives can be set with the meta box in the user profiles.', 'cpseo' ),
	'classes'         => 'cpseo-supports-variables cpseo-description',
	'dep'             => $dep,
	'attributes'      => [
		'class'             => 'cmb2-textarea-small wp-exclude-emoji',
		'data-gramm_editor' => 'false',
		'data-exclude-variables'	=> 'seo_title,seo_description',
	],
	'sanitization_cb' => false,
]);

$cmb->add_field([
	'id'      => 'cpseo_author_add_meta_box',
	'type'    => 'switch',
	'name'    => esc_html__( 'Add SEO Meta Box for Users', 'cpseo' ),
	'desc'    => esc_html__( 'Add SEO Meta Box for user profile pages. Access to the Meta Box can be fine tuned with code, using a special filter hook.', 'cpseo' ),
	'default' => 'on',
	'dep'     => $dep,
]);
