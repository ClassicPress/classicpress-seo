<?php
/**
 * WooCommerce general settings.
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\WooCommerce
 */

use Classic_SEO\Helper;

$cmb->add_field([
	'id'      => 'cpseo_wc_remove_product_base',
	'type'    => 'switch',
	'name'    => esc_html__( 'Remove base', 'cpseo' ),
	'desc'    => esc_html__( 'Remove prefix from product URL.', 'cpseo' ) .
		'<br><code>' . esc_html__( 'default: /product/accessories/action-figures/acme/ - changed: /accessories/action-figures/acme/', 'cpseo' ) . '</code>',
	'default' => 'off',
]);

$cmb->add_field([
	'id'      => 'cpseo_wc_remove_category_base',
	'type'    => 'switch',
	'name'    => esc_html__( 'Remove category base', 'cpseo' ),
	'desc'    => esc_html__( 'Remove prefix from category URL.', 'cpseo' ) .
		'<br><code>' . esc_html__( 'default: /product-category/accessories/action-figures/ - changed: /accessories/action-figures/', 'cpseo' ) . '</code>',
	'default' => 'off',
]);

$cmb->add_field([
	'id'      => 'cpseo_wc_remove_category_parent_slugs',
	'type'    => 'switch',
	'name'    => esc_html__( ' Remove parent slugs', 'cpseo' ),
	'desc'    => esc_html__( 'Remove parent slugs from category URL.', 'cpseo' ) .
		'<br><code>' . esc_html__( 'default: /product-category/accessories/action-figures/ - changed: /product-category/action-figures/', 'cpseo' ) . '</code>',
	'default' => 'off',
]);

$cmb->add_field([
	'id'      => 'cpseo_wc_remove_generator',
	'type'    => 'switch',
	'name'    => esc_html__( 'Remove Generator Tag', 'cpseo' ),
	'desc'    => esc_html__( 'Remove Classic Commerce generator tag from the source code.', 'cpseo' ),
	'default' => 'on',
]);

$cmb->add_field([
	'id'      => 'cpseo_remove_shop_snippet_data',
	'type'    => 'switch',
	'name'    => esc_html__( 'Remove Snippet Data', 'cpseo' ),
	'desc'    => esc_html__( 'Remove Snippet Data from Classic Commerce Shop page.', 'cpseo' ),
	'default' => 'on',
]);

$cmb->add_field([
	'id'      => 'cpseo_product_brand',
	'type'    => 'select',
	'name'    => esc_html__( 'Brand', 'cpseo' ),
	'desc'    => esc_html__( 'Select Product Brand Taxonomy to use in Schema.org & OpenGraph markup.', 'cpseo' ),
	'options' => Helper::get_object_taxonomies( 'product', 'choices', false ),
]);
