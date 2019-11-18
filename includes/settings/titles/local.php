<?php
/**
 * The local seo settings.
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Local_Seo
 */

use Classic_SEO\Helper;

$cmb->add_field([
	'id'      => 'cpseo_knowledgegraph_type',
	'type'    => 'radio_inline',
	'name'    => esc_html__( 'Person or Company', 'cpseo' ),
	'options' => [
		'person'  => esc_html__( 'Person', 'cpseo' ),
		'company' => esc_html__( 'Organization', 'cpseo' ),
	],
	'desc'    => esc_html__( 'Choose whether the site represents a person or an organization.', 'cpseo' ),
	'default' => 'person',
]);

$cmb->add_field([
	'id'      => 'cpseo_knowledgegraph_name',
	'type'    => 'text',
	'name'    => esc_html__( 'Name', 'cpseo' ),
	'desc'    => esc_html__( 'Your name or company name', 'cpseo' ),
	'default' => get_bloginfo( 'name' ),
]);

$cmb->add_field([
	'id'      => 'cpseo_knowledgegraph_logo',
	'type'    => 'file',
	'name'    => esc_html__( 'Logo', 'cpseo' ),
	'desc'    => __( '<strong>Min Size: 160Χ90px, Max Size: 1920X1080px</strong>.<br /> A squared image is preferred by the search engines.', 'cpseo' ),
	'options' => [ 'url' => false ],
]);

$cmb->add_field([
	'id'      => 'cpseo_url',
	'type'    => 'text',
	'name'    => esc_html__( 'URL', 'cpseo' ),
	'desc'    => esc_html__( 'URL of the item.', 'cpseo' ),
	'default' => site_url(),
]);
