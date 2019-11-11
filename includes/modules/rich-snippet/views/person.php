<?php
/**
 * Metabox - Person Rich Snippet
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\RichSnippet
 */

$person = [ [ 'cpseo_rich_snippet', 'person' ] ];

$cmb->add_field([
	'id'         => 'cpseo_snippet_person_email',
	'type'       => 'text',
	'attributes' => [ 'type' => 'email' ],
	'name'       => esc_html__( 'Email', 'cpseo' ),
	'classes'    => 'cpseo-validate-field',
	'attributes' => [
		'data-rule-email' => true,
	],
	'dep'        => $person,
]);

$cmb->add_field([
	'id'   => 'cpseo_snippet_person_address',
	'type' => 'address',
	'name' => esc_html__( 'Address', 'cpseo' ),
	'dep'  => $person,
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_person_gender',
	'type'    => 'text',
	'name'    => esc_html__( 'Gender', 'cpseo' ),
	'classes' => 'cmb-row-50',
	'dep'     => $person,
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_person_job_title',
	'type'    => 'text',
	'name'    => esc_html__( 'Job title', 'cpseo' ),
	'desc'    => esc_html__( 'The job title of the person (for example, Financial Manager).', 'cpseo' ),
	'classes' => 'cmb-row-50',
	'dep'     => $person,
]);
