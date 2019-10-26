<?php
/**
 * Metabox - Software Application Rich Snippet
 *
 * @package    ClassicPress_SEO
 * @subpackage ClassicPress_SEO\RichSnippet
 */

$software = [ [ 'cpseo_rich_snippet', 'software' ] ];

$cmb->add_field([
	'id'         => 'cpseo_snippet_software_price',
	'type'       => 'text',
	'name'       => esc_html__( 'Price', 'cpseo' ),
	'dep'        => $software,
	'classes'    => 'cmb-row-50',
	'attributes' => [
		'type' => 'number',
		'step' => 'any',
	],
]);

$cmb->add_field([
	'id'         => 'cpseo_snippet_software_price_currency',
	'type'       => 'text',
	'name'       => esc_html__( 'Price Currency', 'cpseo' ),
	'desc'       => esc_html__( 'ISO 4217 Currency code. Example: EUR', 'cpseo' ),
	'classes'    => 'cmb-row-50 cpseo-validate-field',
	'attributes' => [
		'data-rule-regex'       => 'true',
		'data-validate-pattern' => '^[A-Z]{3}$',
		'data-msg-regex'        => esc_html__( 'Please use the correct format. Example: EUR', 'cpseo' ),
	],
	'dep'        => $software,
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_software_operating_system',
	'name'    => esc_html__( 'Operating System', 'cpseo' ),
	'type'    => 'text',
	'desc'    => esc_html__( 'For example, "Windows 7", "OSX 10.6", "Android 1.6"', 'cpseo' ),
	'classes' => 'cmb-row-50',
	'dep'     => $software,
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_software_application_category',
	'name'    => esc_html__( 'Application Category', 'cpseo' ),
	'type'    => 'text',
	'desc'    => esc_html__( 'For example, "Game", "Multimedia"', 'cpseo' ),
	'classes' => 'cmb-row-50',
	'dep'     => $software,
]);

$cmb->add_field([
	'id'         => 'cpseo_snippet_software_rating_value',
	'name'       => esc_html__( 'Rating', 'cpseo' ),
	'desc'       => esc_html__( 'Average of all ratings (1-5). Example: 4.7', 'cpseo' ),
	'type'       => 'text',
	'dep'        => $software,
	'classes'    => 'cmb-row-50',
	'attributes' => [
		'type' => 'number',
		'min'  => 1,
		'max'  => 5,
		'step' => 'any',
	],
]);

$cmb->add_field([
	'id'         => 'cpseo_snippet_software_rating_count',
	'name'       => esc_html__( 'Rating Count', 'cpseo' ),
	'desc'       => esc_html__( 'Number of ratings', 'cpseo' ),
	'type'       => 'text',
	'dep'        => $software,
	'classes'    => 'cmb-row-50',
	'attributes' => [ 'type' => 'number' ],
]);
