<?php
/**
 * Metabox - Review Rich Snippet
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\RichSnippet
 */

$review = [ [ 'cpseo_rich_snippet', 'review' ] ];

$cmb->add_field([
	'id'         => 'cpseo_snippet_review_worst_rating',
	'name'       => esc_html__( 'Worst Rating', 'cpseo' ),
	'desc'       => esc_html__( 'Minimum rating.', 'cpseo' ),
	'type'       => 'text',
	'default'    => 1,
	'dep'        => $review,
	'classes'    => 'cmb-row-33',
	'attributes' => [ 'type' => 'number' ],
]);

$cmb->add_field([
	'id'         => 'cpseo_snippet_review_best_rating',
	'name'       => esc_html__( 'Best Rating', 'cpseo' ),
	'desc'       => esc_html__( 'Maximum rating.', 'cpseo' ),
	'type'       => 'text',
	'default'    => 5,
	'dep'        => $review,
	'classes'    => 'cmb-row-33',
	'attributes' => [ 'type' => 'number' ],
]);

$cmb->add_field([
	'id'         => 'cpseo_snippet_review_rating_value',
	'name'       => esc_html__( 'Rating', 'cpseo' ),
	'desc'       => esc_html__( 'Final rating of the item.', 'cpseo' ),
	'type'       => 'text',
	'dep'        => $review,
	'classes'    => 'cmb-row-33',
	'attributes' => [
		'type' => 'number',
		'step' => 'any',
	],
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_review_location',
	'name'    => esc_html__( 'Review Location', 'cpseo' ),
	'desc'    => esc_html__( 'The review or rating must be displayed on the page to comply with Google\'s Rich Snippet guidelines.', 'cpseo' ),
	'type'    => 'select',
	'dep'     => $review,
	'classes' => 'nob',
	'default' => 'bottom',
	'options' => [
		'bottom' => esc_html__( 'Below Content', 'cpseo' ),
		'top'    => esc_html__( 'Above Content', 'cpseo' ),
		'both'   => esc_html__( 'Above & Below Content', 'cpseo' ),
		'custom' => esc_html__( 'Custom (use shortcode)', 'cpseo' ),
	],
]);

$cmb->add_field([
	'id'         => 'cpseo_snippet_review_shortcode',
	'name'       => ' ',
	'type'       => 'text',
	'classes'    => 'nopt',
	'desc'       => esc_html__( 'Copy & paste this shortcode in the content.', 'cpseo' ),
	'dep'        => [
		'relation' => 'and',
		[ 'cpseo_rich_snippet', 'review' ],
		[ 'cpseo_snippet_review_location', 'custom' ],
	],
	'attributes' => [
		'readonly' => 'readonly',
		'value'    => '[cpseo_review_snippet]',
	],
]);
