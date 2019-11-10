<?php
/**
 * The public-facing template tags.
 *
 * @package    Classic_SEO
 * @subpackage Classic_SEO\Frontend
 */

use Classic_SEO\Sitemap\Router;
use Classic_SEO\Frontend\Breadcrumbs;

/**
 * Get breadcrumbs.
 *
 * @param array $args Array of arguments.
 * @return string Breadcrumbs HTML output
 */
function cpseo_get_breadcrumbs( $args = [] ) {
	return Breadcrumbs::get() ? Breadcrumbs::get()->get_breadcrumb( $args ) : '';
}

/**
 * Output breadcrumbs.
 *
 * @param array $args Array of arguments.
 */
function cpseo_the_breadcrumbs( $args = [] ) {
	echo cpseo_get_breadcrumbs( $args );
}

/**
 * Get sitemap url.
 *
 * @return string
 */
function cpseo_get_sitemap_url() {
	return Router::get_base_url( 'sitemap_index.xml' );
}

/**
 * Register extra %variables%. For developers.
 * See cpseo_register_var_replacement().
 *
 * @codeCoverageIgnore
 *
 * @param  string $var       Variable name, for example %custom%. The '%' signs are optional.
 * @param  mixed  $callback  Replacement callback. Should return value and not output it.
 * @param  array  $args      Array with additional title, description and example values for the variable.
 *
 * @return bool Replacement was registered successfully or not.
 */
function cpseo_register_var_replacement( $var, $callback, $args = [] ) {
	return Classic_SEO\Replace_Vars::register_replacement( $var, $callback, $args );
}
