<?php
/**
 * The Updates routine for version 1.0.0 beta 4
 *
 * @since      1.0.0 beta 4
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Updates
 */


use Classic_SEO\Helper;
use Classic_SEO\Admin\Admin_Helper;


/**
 * Convert old snippet variables to new one
 */
function cpseo_1_0_0_beta_4_convert_snippet_variables() {
	$all_opts = cpseo()->settings->all_raw();
	$titles   = $all_opts['titles'];

	// Post Types.
	$post_types   = Helper::get_accessible_post_types();
	$post_types[] = 'product';

	foreach ( $post_types as $post_type ) {
		if ( isset( $titles[ 'cpseo_pt_' . $post_type . '_default_snippet_name' ] ) && '%title%' === $titles[ 'cpseo_pt_' . $post_type . '_default_snippet_name' ] ) {
			$titles[ 'cpseo_pt_' . $post_type . '_default_snippet_name' ] = '%seo_title%';
		}

		if ( isset( $titles[ 'cpseo_pt_' . $post_type . '_default_snippet_desc' ] ) && '%excerpt%' === $titles[ 'cpseo_pt_' . $post_type . '_default_snippet_desc' ] ) {
			$titles[ 'cpseo_pt_' . $post_type . '_default_snippet_desc' ] = '%seo_description%';
		}
	}

	Helper::update_all_settings( null, $titles, null );
	cpseo()->settings->reset();
}
cpseo_1_0_0_beta_4_convert_snippet_variables();
