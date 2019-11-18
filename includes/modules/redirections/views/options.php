<?php
/**
 * Redirections general settings.
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Redirections
 */

use Classic_SEO\Helper;

$cmb->add_field( array(
	'id'      => 'cpseo_redirections_debug',
	'type'    => 'switch',
	'name'    => esc_html__( 'Debug Redirections', 'cpseo' ),
	'desc'    => esc_html__( 'Display the Debug Console instead of being redirected. Administrators only.', 'cpseo' ),
	'default' => 'off',
) );

$cmb->add_field( array(
	'id'      => 'cpseo_redirections_fallback',
	'type'    => 'radio',
	'name'    => esc_html__( 'Fallback Behavior', 'cpseo' ),
	'desc'    => esc_html__( 'If nothing similar is found, this behavior will be applied.', 'cpseo' ),
	'options' => array(
		'default'  => esc_html__( 'Default 404', 'cpseo' ),
		'homepage' => esc_html__( 'Redirect to Homepage', 'cpseo' ),
		'custom'   => esc_html__( 'Custom Redirection', 'cpseo' ),
	),
	'default' => 'default',
) );

$cmb->add_field( array(
	'id'   => 'cpseo_redirections_custom_url',
	'type' => 'text',
	'name' => esc_html__( 'Custom Url ', 'cpseo' ),
	'dep'  => array( array( 'cpseo_redirections_fallback', 'custom' ) ),
) );

$cmb->add_field( array(
	'id'      => 'cpseo_redirections_header_code',
	'type'    => 'select',
	'name'    => esc_html__( 'Redirection Type', 'cpseo' ),
	'options' => Helper::choices_redirection_types(),
	'default' => '301',
) );

$cmb->add_field( array(
	'id'      => 'cpseo_redirections_post_redirect',
	'type'    => 'switch',
	'name'    => esc_html__( 'Auto Post Redirect', 'cpseo' ),
	'desc'    => esc_html__( 'Extend the functionality of WordPress by creating redirects in our plugin when you change the slug of a post, page, category or a CPT. You can modify the redirection further according to your needs.', 'cpseo' ),
	'default' => 'off',
) );
