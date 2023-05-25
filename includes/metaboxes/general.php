<?php
/**
 * Metabox - General Tab
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Metaboxes
 */

use Classic_SEO\Helper;
use Classic_SEO\Helpers\WordPress;
use Classic_SEO\Admin\Admin_Helper;

$cmb->add_field( array(
	'id'   => 'cpseo_serp_preview',
	'type' => 'raw',
	'file' => cpseo()->includes_dir() . 'metaboxes/serp-preview.php',
) );

$cmb->add_field( array(
	'id'              => 'cpseo_title',
	'type'            => 'text',
	'desc'            => esc_html__( 'This is what will appear in the first line when this post shows up in the search results.', 'cpseo' ),
	'classes'         => 'cpseo-supports-variables',
	'sanitization_cb' => [ '\Classic_SEO\CMB2', 'sanitize_textfield' ],
	'attributes'      => array(
		'class'             => 'regular-text wp-exclude-emoji',
		'data-gramm_editor' => 'false',
		'data-exclude-variables'	=> 'seo_title,seo_description',
	),
) );

$cmb->add_field( array(
	'id'   => 'cpseo_permalink',
	'type' => 'text',
	'desc' => esc_html__( 'This is the unique URL of this page, displayed below the post title in the search results.', 'cpseo' ),
) );

$cmb->add_field( array(
	'id'              => 'cpseo_description',
	'type'            => 'textarea',
	'desc'            => esc_html__( 'This is what will appear as the description when this post shows up in the search results.', 'cpseo' ),
	'classes'         => 'cpseo-supports-variables',
	'sanitization_cb' => true,
	'attributes'      => array(
		'class'             => 'cmb2_textarea wp-exclude-emoji',
		'rows'              => 2,
		'data-autoresize'   => true,
		'data-gramm_editor' => 'false',
		'data-exclude-variables'	=> 'seo_title,seo_description',
	),
) );

$cmb->add_field( array(
	'id'          => 'cpseo_focus_keyword',
	'type'        => 'text',
	'name'        => esc_html__( 'Focus Keyword', 'cpseo' ),
	// translators: Link to kb article
	'desc'        => sprintf( wp_kses_post( __( 'Insert keywords you want to rank for. Aim for at least 80/100.', 'cpseo' ) ) ),
	'classes'     => 'nob',
	'attributes'  => array(
		'placeholder' => esc_html__( 'Example: Classic SEO', 'cpseo' ),
	),
) );

if ( ! Admin_Helper::is_term_profile_page() ) {
	$cmb->add_field( array(
		'id'      => 'cpseo_cornerstone_content',
		'type'    => 'checkbox',
		'name'    => '&nbsp;',
		'classes' => 'nob nopt',
		'desc'    => '<strong>' . esc_html__( 'This post is Cornerstone Content', 'cpseo' ) . '</strong>' .
			Admin_Helper::get_tooltip( esc_html__( 'Cornerstone content should be among the top pages on your website and should contain the most important information.', 'cpseo' ) ),
	) );
}

if ( Helper::has_cap( 'onpage_analysis' ) ) {
	$cmb->add_field( array(
		'id'   => 'cpseo_serp_checklist',
		'type' => 'raw',
		'file' => cpseo()->includes_dir() . 'metaboxes/serp-checklist.php',
	) );
}

/**
 * Allow disabling the primary term feature.
 *
 * @param bool $return True to disable.
 */
if ( false === $this->do_filter( 'primary_term', false ) ) {
	$taxonomies = Helper::get_object_taxonomies( ( new class { use Wordpress; } )::get_post_type(), 'objects' );
	$taxonomies = wp_filter_object_list( $taxonomies, array( 'hierarchical' => true ), 'and', 'name' );
	foreach ( $taxonomies as $taxonomy ) {
		$cmb->add_field( array(
			'id'         => 'cpseo_primary_' . $taxonomy,
			'type'       => 'hidden',
			'default'    => 0,
			'attributes' => array(
				'data-primary-term' => $taxonomy,
			),
		) );
	}
}

// SEO Score.
$cmb->add_field( array(
	'id'   => 'cpseo_seo_score',
	'type' => 'hidden',
) );
