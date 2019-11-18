<?php
/**
 * Metabox - Restaurant Rich Snippet
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\RichSnippet
 */

$restaurant = [ [ 'cpseo_rich_snippet', 'restaurant' ] ];

$cmb->add_field([
	'id'      => 'cpseo_snippet_restaurant_serves_cuisine',
	'type'    => 'text',
	'name'    => esc_html__( 'Serves Cuisine', 'cpseo' ),
	'desc'    => esc_html__( 'The type of cuisine we serve. Separated by comma.', 'cpseo' ),
	'classes' => 'cmb-row-50 nob',
	'dep'     => $restaurant,
]);

$cmb->add_field([
	'id'         => 'cpseo_snippet_restaurant_menu',
	'type'       => 'text_url',
	'name'       => esc_html__( 'Menu URL', 'cpseo' ),
	'desc'       => esc_html__( 'URL pointing to the menu of the restaurant.', 'cpseo' ),
	'classes'    => 'cmb-row-33 nob cpseo-validate-field',
	'attributes' => [
		'data-rule-url' => true,
	],
	'dep'        => $restaurant,
]);
