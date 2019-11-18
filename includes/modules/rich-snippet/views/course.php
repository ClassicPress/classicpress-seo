<?php
/**
 * Metabox - Course Rich Snippet
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\RichSnippet
 */

$course_dep = [ [ 'cpseo_rich_snippet', 'course' ] ];

$cmb->add_field([
	'id'      => 'cpseo_snippet_course_provider_type',
	'type'    => 'radio_inline',
	'name'    => esc_html__( 'Course Provider', 'cpseo' ),
	'options' => [
		'Organization' => esc_html__( 'Organization', 'cpseo' ),
		'Person'       => esc_html__( 'Person', 'cpseo' ),
	],
	'classes' => 'cmb-row-33 nob',
	'default' => 'Organization',
	'dep'     => $course_dep,
]);

$cmb->add_field([
	'id'   => 'cpseo_snippet_course_provider',
	'type' => 'text',
	'name' => esc_html__( 'Course Provider Name', 'cpseo' ),
	'dep'  => $course_dep,
]);

$cmb->add_field([
	'id'         => 'cpseo_snippet_course_provider_url',
	'type'       => 'text_url',
	'name'       => esc_html__( 'Course Provider URL', 'cpseo' ),
	'dep'        => $course_dep,
	'attributes' => [
		'data-rule-url' => 'true',
	],
	'classes'    => 'nob cpseo-validate-field',
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_course_rating',
	'type'    => 'text',
	'name'    => esc_html__( 'Rating', 'cpseo' ),
	'desc'    => esc_html__( 'Rating score of the course. Optional.', 'cpseo' ),
	'classes' => 'cmb-row-33',
	'dep'     => $course_dep,
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_course_rating_min',
	'type'    => 'text',
	'name'    => esc_html__( 'Rating Minimum', 'cpseo' ),
	'desc'    => esc_html__( 'Rating minimum score of the course.', 'cpseo' ),
	'classes' => 'cmb-row-33',
	'dep'     => $course_dep,
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_course_rating_max',
	'type'    => 'text',
	'name'    => esc_html__( 'Rating Maximum', 'cpseo' ),
	'desc'    => esc_html__( 'Rating maximum score of the course.', 'cpseo' ),
	'classes' => 'cmb-row-33',
	'dep'     => $course_dep,
]);
