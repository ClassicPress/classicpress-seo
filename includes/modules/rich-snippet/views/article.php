<?php
/**
 * Metabox - Article Rich Snippet
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\RichSnippet
 */

use Classic_SEO\Helper;

$article_dep = [ [ 'cpseo_rich_snippet', 'article' ] ];
/* translators: Google article snippet doc link */
$article_desc = 'person' === Helper::get_settings( 'titles.cpseo_knowledgegraph_type' ) ? '<div class="notice notice-warning inline"><p>' . sprintf( __( 'Google does not allow Person as the Publisher for articles. Organization will be used instead.', 'cpseo' ) ) . '</p></div>' : '';

$cmb->add_field([
	'id'      => 'cpseo_snippet_article_type',
	'type'    => 'radio_inline',
	'name'    => esc_html__( 'Article Type', 'cpseo' ),
	'options' => [
		'Article'     => esc_html__( 'Article', 'cpseo' ),
		'BlogPosting' => esc_html__( 'Blog Post', 'cpseo' ),
		'NewsArticle' => esc_html__( 'News Article', 'cpseo' ),
	],
	'default' => Helper::get_settings( "titles.cpseo_pt_{$post_type}_default_article_type" ),
	'classes' => 'nob',
	'desc'    => $article_desc,
	'dep'     => $article_dep,
]);
