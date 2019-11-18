<?php
/**
 * Metabox - Product Rich Snippet
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\RichSnippet
 */

$product = [ [ 'cpseo_rich_snippet', 'product' ] ];

$cmb->add_field([
	'id'   => 'cpseo_snippet_product_sku',
	'type' => 'text',
	'name' => esc_html__( 'Product SKU', 'cpseo' ),
	'dep'  => $product,
]);

$cmb->add_field([
	'id'   => 'cpseo_snippet_product_brand',
	'type' => 'text',
	'name' => esc_html__( 'Product Brand', 'cpseo' ),
	'dep'  => $product,
]);

$cmb->add_field([
	'id'         => 'cpseo_snippet_product_currency',
	'type'       => 'text',
	'name'       => esc_html__( 'Product Currency', 'cpseo' ),
	'desc'       => esc_html__( 'ISO 4217 Currency Code', 'cpseo' ),
	'classes'    => 'cpseo-validate-field',
	'attributes' => [
		'data-rule-regex'       => 'true',
		'data-validate-pattern' => '^[A-Z]{3}$',
		'data-msg-regex'        => esc_html__( 'Please use the correct format. Example: EUR', 'cpseo' ),
	],
	'dep'        => $product,
]);

$cmb->add_field([
	'id'         => 'cpseo_snippet_product_price',
	'type'       => 'text',
	'name'       => esc_html__( 'Product Price', 'cpseo' ),
	'dep'        => $product,
	'attributes' => [
		'type' => 'number',
		'step' => 'any',
	],
]);

$cmb->add_field([
	'id'          => 'cpseo_snippet_product_price_valid',
	'type'        => 'text_date',
	'date_format' => 'Y-m-d',
	'name'        => esc_html__( 'Price Valid Until', 'cpseo' ),
	'desc'        => esc_html__( 'The date after which the price will no longer be available.', 'cpseo' ),
	'dep'         => $product,
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_product_instock',
	'type'    => 'switch',
	'name'    => esc_html__( 'Product In-Stock', 'cpseo' ),
	'dep'     => $product,
	'classes' => 'nob',
	'default' => 'on',
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_product_rating',
	'type'    => 'text',
	'name'    => esc_html__( 'Rating', 'cpseo' ),
	'desc'    => esc_html__( 'Rating score of the product. Optional.', 'cpseo' ),
	'classes' => 'cmb-row-33',
	'dep'     => $product,
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_product_rating_min',
	'type'    => 'text',
	'name'    => esc_html__( 'Rating Minimum', 'cpseo' ),
	'desc'    => esc_html__( 'Rating minimum score of the product.', 'cpseo' ),
	'classes' => 'cmb-row-33',
	'dep'     => $product,
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_product_rating_max',
	'type'    => 'text',
	'name'    => esc_html__( 'Rating Maximum', 'cpseo' ),
	'desc'    => esc_html__( 'Rating maximum score of the product.', 'cpseo' ),
	'classes' => 'cmb-row-33',
	'dep'     => $product,
]);
