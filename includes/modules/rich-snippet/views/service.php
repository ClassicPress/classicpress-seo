<?php
/**
 * Metabox - Service Rich Snippet
 *
 * @package    ClassicPress_SEO
 * @subpackage ClassicPress_SEO\RichSnippet
 */

$service = [ [ 'cpseo_rich_snippet', 'service' ] ];

$cmb->add_field([
	'id'   => 'cpseo_snippet_service_type',
	'name' => esc_html__( 'Service Type', 'cpseo' ),
	'type' => 'text',
	'desc' => esc_html__( 'The type of service being offered, e.g. veterans\' benefits, emergency relief, etc.', 'cpseo' ),
	'dep'  => $service,
]);

$cmb->add_field([
	'id'         => 'cpseo_snippet_service_price',
	'type'       => 'text',
	'name'       => esc_html__( 'Price', 'cpseo' ),
	'desc'       => esc_html__( 'Insert price, e.g. "50.00", or a price range, e.g. "40.00-50.00".', 'cpseo' ),
	'classes'    => 'cmb-row-50 cpseo-validate-field',
	'dep'        => $service,
	'attributes' => [
		'data-rule-regex'       => 'true',
		'data-validate-pattern' => '[\d -]+',
		'data-msg-regex'        => esc_html__( 'Please use the correct format. Example: 25', 'cpseo' ),
	],
]);

$cmb->add_field([
	'id'         => 'cpseo_snippet_service_price_currency',
	'type'       => 'text',
	'name'       => esc_html__( 'Price Currency', 'cpseo' ),
	'desc'       => esc_html__( 'ISO 4217 Currency code. Example: EUR', 'cpseo' ),
	'classes'    => 'cmb-row-50 cpseo-validate-field',
	'attributes' => [
		'data-rule-regex'       => 'true',
		'data-validate-pattern' => '^[A-Z]{3}$',
		'data-msg-regex'        => esc_html__( 'Please use the correct format. Example: EUR', 'cpseo' ),
	],
	'dep'        => $service,
]);

$cmb->add_field([
	'id'         => 'cpseo_snippet_service_rating_value',
	'name'       => esc_html__( 'Rating', 'cpseo' ),
	'desc'       => esc_html__( 'Average of all ratings (1-5). Example: 4.7', 'cpseo' ),
	'type'       => 'text',
	'dep'        => $service,
	'classes'    => 'cmb-row-50',
	'attributes' => [
		'type' => 'number',
		'min'  => 1,
		'max'  => 5,
		'step' => 'any',
	],
]);

$cmb->add_field([
	'id'         => 'cpseo_snippet_service_rating_count',
	'name'       => esc_html__( 'Rating Count', 'cpseo' ),
	'desc'       => esc_html__( 'Number of ratings', 'cpseo' ),
	'type'       => 'text',
	'dep'        => $service,
	'classes'    => 'cmb-row-50',
	'attributes' => [ 'type' => 'number' ],
]);
