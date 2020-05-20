<?php
/**
 * Metabox - Job Posting Rich Snippet
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\RichSnippet
 */

use Classic_SEO\Helper;

$jobposting = [ [ 'cpseo_rich_snippet', 'jobposting' ] ];

$cmb->add_field([
	'id'         => 'cpseo_snippet_jobposting_salary',
	'type'       => 'text',
	'name'       => esc_html__( 'Salary (Recommended)', 'cpseo' ),
	'desc'       => esc_html__( 'Insert amount, e.g. "50.00", or a salary range, e.g. "40.00-50.00".', 'cpseo' ),
	'classes'    => 'cmb-row-33 cpseo-validate-field',
	'dep'        => $jobposting,
	'attributes' => [
		'data-rule-regex'       => 'true',
		'data-validate-pattern' => '[\d -]+',
		'data-msg-regex'        => esc_html__( 'Please use the correct format. Example: 50000', 'cpseo' ),
	],
]);

$cmb->add_field([
	'id'         => 'cpseo_snippet_jobposting_currency',
	'type'       => 'text',
	'name'       => esc_html__( 'Salary Currency', 'cpseo' ),
	'desc'       => esc_html__( 'ISO 4217 Currency code. Example: EUR', 'cpseo' ),
	'classes'    => 'cmb-row-33 cpseo-validate-field',
	'attributes' => [
		'data-rule-regex'       => 'true',
		'data-validate-pattern' => '^[A-Z]{3}$',
		'data-msg-regex'        => esc_html__( 'Please use the correct format. Example: EUR', 'cpseo' ),
	],
	'dep'        => $jobposting,
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_jobposting_payroll',
	'type'    => 'select',
	'name'    => esc_html__( 'Payroll (Recommended)', 'cpseo' ),
	'desc'    => esc_html__( 'Salary amount is for', 'cpseo' ),
	'options' => [
		''      => esc_html__( 'None', 'cpseo' ),
		'YEAR'  => esc_html__( 'Yearly', 'cpseo' ),
		'MONTH' => esc_html__( 'Monthly', 'cpseo' ),
		'WEEK'  => esc_html__( 'Weekly', 'cpseo' ),
		'DAY'   => esc_html__( 'Daily', 'cpseo' ),
		'HOUR'  => esc_html__( 'Hourly', 'cpseo' ),
	],
	'classes' => 'cmb-row-33',
	'dep'     => $jobposting,
]);

$cmb->add_field([
	'id'          => 'cpseo_snippet_jobposting_startdate',
	'type'        => 'text_datetime_timestamp',
	'date_format' => 'Y-m-d',
	'name'        => esc_html__( 'Date Posted', 'cpseo' ),
	'desc'        => wp_kses_post( __( 'The original date on which employer posted the job. You can leave it empty to use the post publication date as job posted date.', 'cpseo' ) ),
	'classes'     => 'cmb-row-33',
	'dep'         => $jobposting,
]);

$cmb->add_field([
	'id'          => 'cpseo_snippet_jobposting_expirydate',
	'type'        => 'text_datetime_timestamp',
	'date_format' => 'Y-m-d',
	'name'        => esc_html__( 'Expiry Posted', 'cpseo' ),
	'desc'        => esc_html__( 'The date when the job posting will expire. If a job posting never expires, or you do not know when the job will expire, do not include this property.', 'cpseo' ),
	'classes'     => 'cmb-row-33',
	'dep'         => $jobposting,
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_jobposting_unpublish',
	'type'    => 'switch',
	'name'    => esc_html__( 'Unpublish when expired', 'cpseo' ),
	'desc'    => esc_html__( 'If checked, post status will be changed to Draft and its URL will return a 404 error, as required by the Rich Result guidelines.', 'cpseo' ),
	'classes' => 'cmb-row-33',
	'default' => 'on',
	'dep'     => $jobposting,
]);

$cmb->add_field([
	'id'                => 'cpseo_snippet_jobposting_employment_type',
	'type'              => 'multicheck_inline',
	'name'              => esc_html__( 'Employment Type (Recommended)', 'cpseo' ),
	'desc'              => esc_html__( 'Type of employment. You can choose more than one value.', 'cpseo' ),
	'options'           => [
		'FULL_TIME'  => esc_html__( 'Full Time', 'cpseo' ),
		'PART_TIME'  => esc_html__( 'Part Time', 'cpseo' ),
		'CONTRACTOR' => esc_html__( 'Contractor', 'cpseo' ),
		'TEMPORARY'  => esc_html__( 'Temporary', 'cpseo' ),
		'INTERN'     => esc_html__( 'Intern', 'cpseo' ),
		'VOLUNTEER'  => esc_html__( 'Volunteer', 'cpseo' ),
		'PER_DIEM'   => esc_html__( 'Per Diem', 'cpseo' ),
		'OTHER'      => esc_html__( 'Other', 'cpseo' ),
	],
	'dep'               => $jobposting,
	'select_all_button' => false,
]);

$cmb->add_field([
	'id'         => 'cpseo_snippet_jobposting_organization',
	'type'       => 'text',
	'name'       => esc_html__( 'Hiring Organization', 'cpseo' ),
	'desc'       => esc_html__( 'The name of the company. Leave empty to use your own company information.', 'cpseo' ),
	'attributes' => [
		'placeholder' => 'company' === Helper::get_settings( 'titles.cpseo_knowledgegraph_type' ) ? Helper::get_settings( 'titles.cpseo_knowledgegraph_name' ) : get_bloginfo( 'name' ),
	],
	'dep'        => $jobposting,
	'classes'    => 'cmb-row-50',
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_jobposting_id',
	'type'    => 'text',
	'name'    => esc_html__( 'Posting ID (Recommended)', 'cpseo' ),
	'desc'    => esc_html__( 'The hiring organization\'s unique identifier for the job. Leave empty to use the post ID.', 'cpseo' ),
	'classes' => 'cmb-row-50',
	'dep'     => $jobposting,
]);

$cmb->add_field([
	'id'         => 'cpseo_snippet_jobposting_url',
	'type'       => 'text_url',
	'name'       => esc_html__( 'Organization URL (Recommended)', 'cpseo' ),
	'desc'       => esc_html__( 'The URL of the organization offering the job position. Leave empty to use your own company information.', 'cpseo' ),
	'classes'    => 'cmb-row-50 cpseo-validate-field',
	'attributes' => [
		'data-rule-url' => 'true',
	],
	'dep'        => $jobposting,
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_jobposting_logo',
	'type'    => 'text_url',
	'name'    => esc_html__( 'Organization Logo (Recommended)', 'cpseo' ),
	'desc'    => esc_html__( 'Logo URL of the organization offering the job position. Leave empty to use your own company information.', 'cpseo' ),
	'classes' => 'cmb-row-50',
	'dep'     => $jobposting,
]);

$cmb->add_field([
	'id'      => 'cpseo_snippet_jobposting_address',
	'type'    => 'address',
	'name'    => esc_html__( 'Location', 'cpseo' ),
	'classes' => 'nob',
	'dep'     => $jobposting,
]);
