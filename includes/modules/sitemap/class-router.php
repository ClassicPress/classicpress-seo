<?php
/**
 * The Sitemap rewrite setup and handling functionality.
 *
 * @since      0.1.8
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Sitemap

 */

namespace Classic_SEO\Sitemap;

use Classic_SEO\Traits\Hooker;
use Classic_SEO\Helpers\Str;
use Classic_SEO\Helpers\Url;

defined( 'ABSPATH' ) || exit;

/**
 * Router class
 */
class Router {

	use Hooker;

	/**
	 * The Constructor.
	 */
	public function __construct() {
		$this->action( 'init', 'init', 1 );
		$this->action( 'parse_query', 'request_sitemap', 1 );
		$this->filter( 'redirect_canonical', 'redirect_canonical' );
		$this->action( 'template_redirect', 'template_redirect', 0 );
		$this->action( 'after_setup_theme', 'reduce_query_load', 99 );
	}

	/**
	 * Sets up rewrite rules.
	 */
	public function init() {
		global $wp;

		$wp->add_query_var( 'sitemap' );
		$wp->add_query_var( 'sitemap_n' );
		$wp->add_query_var( 'xsl' );

		add_rewrite_rule( 'sitemap_index\.xml$', 'index.php?sitemap=1', 'top' );
		add_rewrite_rule( '([^/]+?)-sitemap([0-9]+)?\.xml$', 'index.php?sitemap=$matches[1]&sitemap_n=$matches[2]', 'top' );
		add_rewrite_rule( '([a-z]+)?-?sitemap\.xsl$', 'index.php?xsl=$matches[1]', 'top' );
	}

	/**
	 * Stop trailing slashes on sitemap.xml URLs.
	 *
	 * @param  string $redirect The redirect URL currently determined.
	 * @return boolean|string $redirect
	 */
	public function redirect_canonical( $redirect ) {
		return ( get_query_var( 'sitemap' ) || get_query_var( 'xsl' ) ) ? false : $redirect;
	}

	/**
	 * Serves sitemap when needed using correct sitemap module
	 *
	 * @param WP_Query $query The WP_Query instance (passed by reference).
	 */
	public function request_sitemap( $query ) {
		if ( ! $query->is_main_query() ) {
			return;
		}

		$xsl = get_query_var( 'xsl' );
		if ( ! empty( $xsl ) ) {
			$stylesheet = new Stylesheet;
			$stylesheet->output( $xsl );
			return;
		}

		$type = get_query_var( 'sitemap' );
		if ( empty( $type ) ) {
			return;
		}

		new Sitemap_XML( $type );
	}

	/**
	 * Check the current request URI, if we can determine it's probably an XML sitemap, kill loading the widgets
	 */
	public function reduce_query_load() {
		if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
			return;
		}
		$request   = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
		$extension = substr( $request, -4 );
		if ( Str::contains( 'sitemap', $request ) && in_array( $extension, [ '.xml', '.xsl' ], true ) ) {
			remove_all_actions( 'widgets_init' );
		}
	}

	/**
	 * Redirects sitemap.xml to sitemap_index.xml.
	 */
	public function template_redirect() {
		if ( ! $this->needs_sitemap_index_redirect() ) {
			return;
		}

		header( 'X-Redirect-By: Classic SEO' );
		wp_redirect( home_url( '/sitemap_index.xml' ), 301 );
		exit;
	}

	/**
	 * Checks whether the current request needs to be redirected to sitemap_index.xml.
	 *
	 * @return bool True if redirect is needed, false otherwise.
	 */
	public function needs_sitemap_index_redirect() {
		global $wp_query;

		return $wp_query->is_404 && home_url( '/sitemap.xml' ) === Url::get_current_url();
	}

	/**
	 * Create base URL for the sitemap.
	 *
	 * @param  string $page Page to append to the base URL.
	 *
	 * @return string base URL (incl page)
	 */
	public static function get_base_url( $page ) {
		global $wp_rewrite;

		$page = self::get_page_url( $page );
		$base = $wp_rewrite->using_index_permalinks() ? $wp_rewrite->index . '/' : '/';

		/**
		 * Filter the base URL of the sitemaps
		 *
		 * @param string $base The string that should be added to home_url() to make the full base URL.
		 */
		$base = apply_filters( 'cpseo/sitemap/base_url', $base );
		
		return home_url( $base . $page );
	}
	
	/**
	 * Get page URL for the sitemap.
	 *
	 * @param string $page Page to append to the base URL.
	 *
	 * @return string
	 */
	public static function get_page_url( $page ) {
		global $wp_rewrite;

		if ( $wp_rewrite->using_permalinks() ) {
			return $page;
		}

		if ( 'sitemap_index.xml' === $page ) {
			return '?sitemap=1';
		}

		$page = \preg_replace( '/([^\/]+?)-sitemap([0-9]+)?\.xml$/', '?sitemap=$1&sitemap_n=$2', $page );
		$page = \preg_replace( '/([a-z]+)?-?sitemap\.xsl$/', '?xsl=$1', $page );

		return $page;
	}
}
