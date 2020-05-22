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


/*
 * GitHub issue #80
 * https://github.com/ClassicPress-plugins/classicpress-seo/issues/80
 * Fixes missing "cpseo_" prefix in some settings
 */
function cpseo_1_0_0_beta_4_add_settings_prefix() {
	$prefix				= 'cpseo_';
	$all_opts			= cpseo()->settings->all_raw();
	$general_options	= $all_opts['general'];
	$titles_options		= $all_opts['titles'];
	$sitemap_options	= $all_opts['sitemap'];

	$general_includes = [
		'stopwords',
		'breadcrumbs_blog_page',
		'rss_before_content',
		'rss_after_content',
		'breadcrumbs',
		'console_profile',
	];

	$titles_includes = [
		'pt_post_default_article_type',
		'pt_page_default_article_type',
		'pt_attachment_default_article_type',
		'pt_product_default_article_type',
		'knowledgegraph_type',
	];

	$sitemap_includes = [
		'news_sitemap_post_type',
		'include_featured_image',
		'ping_search_engines',
		'exclude_posts',
		'exclude_terms',
		'include_images',
	];

	foreach ( $general_includes as $general ) {
		$gen_opt = Helper::get_settings( "general.{$general}" );
		if ( $gen_opt ) {
			$general_options[ "{$prefix}{$general}" ] = $gen_opt;
		}
	}
	if ( $general_options ) {
		Helper::update_all_settings( $general_options, null, null );
	}

	foreach ( $titles_includes as $titles ) {
		$title_opt = Helper::get_settings( "titles.{$titles}" );
		if ( $title_opt ) {
			$titles_options[ "{$prefix}{$titles}" ] = $title_opt;
		}
	}
	if ( $titles_options ) {
		Helper::update_all_settings( null, $titles_options, null );
	}

	foreach ( $sitemap_includes as $sitemap ) {
		$sitemap_opt = Helper::get_settings( "sitemap.{$sitemap}" );
		if ( $sitemap_opt ) {
			$sitemap_options[ "{$prefix}{$sitemap}" ] = "{$prefix}{$sitemap}";
		}
	}
	if ( $sitemap_options ) {
		Helper::update_all_settings( null, null, $sitemap_options );
	}
}

cpseo_1_0_0_beta_4_convert_snippet_variables();
cpseo_1_0_0_beta_4_add_settings_prefix();
