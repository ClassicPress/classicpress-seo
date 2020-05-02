<?php
/**
 * The images settings.
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Settings
 */

use Classic_SEO\Helper;

$cmb->add_field([
	'id'      => 'cpseo_add_img_alt',
	'type'    => 'switch',
	'name'    => esc_html__( 'Add missing ALT attributes', 'cpseo' ),
	'desc'    => wp_kses_post( __( 'Add <code>alt</code> attributes for <code>images</code> without <code>alt</code> attributes automatically. The attribute is dynamically applied when the content is displayed, and the stored content is not changed.', 'cpseo' ) ),
	'default' => 'off',
]);

$cmb->add_field([
	'id'              => 'cpseo_img_alt_format',
	'type'            => 'text',
	'name'            => esc_html__( 'Alt attribute format', 'cpseo' ),
	'desc'            => wp_kses_post( __( 'Format used for the new <code>alt</code> attribute values.', 'cpseo' ) ),
	'classes'         => 'large-text cpseo-supports-variables',
	'default'         => '%title% %count(alt)%',
	'dep'             => [ [ 'cpseo_add_img_alt', 'on' ] ],
	'sanitization_cb' => false,
	'attributes'      => [ 'data-exclude-variables' => 'seo_title,seo_description' ],
]);

$cmb->add_field([
	'id'      => 'cpseo_add_img_title',
	'type'    => 'switch',
	'name'    => esc_html__( 'Add missing TITLE attributes', 'cpseo' ),
	'desc'    => wp_kses_post( __( 'Add <code>TITLE</code> attribute for all <code>images</code> without a <code>TITLE</code> attribute automatically. The attribute is dynamically applied when the content is displayed, and the stored content is not changed.', 'cpseo' ) ),
	'default' => 'off',
]);

$cmb->add_field([
	'id'              => 'cpseo_img_title_format',
	'type'            => 'text',
	'name'            => esc_html__( 'Title attribute format', 'cpseo' ),
	'desc'            => wp_kses_post( __( 'Format used for the new <code>title</code> attribute values.', 'cpseo' ) ),
	'classes'         => 'large-text cpseo-supports-variables dropdown-up',
	'default'         => '%title% %count(title)%',
	'dep'             => [ [ 'cpseo_add_img_title', 'on' ] ],
	'sanitization_cb' => false,
	'attributes'      => [ 'data-exclude-variables' => 'seo_title,seo_description' ],
]);
